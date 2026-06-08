<?php

namespace App\Modules\Sales\Services;

use App\Modules\AuditTrail\Services\AuditTrailService;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use App\Modules\Sales\Models\SalesCollection;
use App\Modules\Sales\Models\SalesInvoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesCollectionService
{
    private const MODULE = 'sales-collections';

    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return SalesCollection::query()
            ->with(['businessPartner:id,company_name', 'invoices:id,sales_collection_id,sales_invoice_no'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('collection_no', 'like', "%{$search}%")
                        ->orWhere('collection_receipt_no', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhereHas('invoices', fn (Builder $invoice) => $invoice->where('sales_invoice_no', 'like', "%{$search}%"));
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('collection_receipt_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('collection_receipt_date', '<=', $date))
            ->latest('id')
            ->paginate($perPage);
    }

    public function nextCollectionNo(): string
    {
        return DB::transaction(function (): string {
            $prefix = 'COL'.now()->format('y-m').'-';
            $last = SalesCollection::query()
                ->where('collection_no', 'like', $prefix.'%')
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('collection_no');

            $next = $last ? ((int) Str::afterLast($last, '-')) + 1 : 1;

            return $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
        });
    }

    public function create(array $payload): SalesCollection
    {
        abort_unless(auth()->user()?->can('sales-collections.create'), 403);

        return DB::transaction(function () use ($payload): SalesCollection {
            $client = BusinessPartner::query()
                ->clients()
                ->whereKey($payload['business_partner_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $invoices = SalesInvoice::query()
                ->whereIn('id', $payload['selected_invoice_ids'])
                ->where('business_partner_id', $client->id)
                ->where('status', 'unpaid')
                ->lockForUpdate()
                ->orderBy('invoice_date')
                ->orderBy('id')
                ->get();

            if ($invoices->count() !== count($payload['selected_invoice_ids'])) {
                throw new \RuntimeException('Some selected invoices are no longer available for collection.');
            }

            $receiptCents = $this->toCents($payload['collection_receipt_amount']);
            $receiptAmount = $this->fromCents($receiptCents);
            $selectedBalanceCents = $invoices->sum(fn (SalesInvoice $invoice): int => $this->toCents($invoice->balance_amount));
            $selectedBalance = $this->fromCents($selectedBalanceCents);

            if ($receiptCents <= 0 || $receiptCents > $selectedBalanceCents) {
                throw new \RuntimeException('Collection receipt amount must be greater than zero and not exceed the selected invoice balance.');
            }

            $collection = SalesCollection::query()->create([
                'collection_no' => $this->nextCollectionNo(),
                'business_partner_id' => $client->id,
                'company_name' => $client->company_name,
                'agent_name' => $client->agent_name,
                'contact_person' => $client->contact_person,
                'bank_name' => $payload['bank_name'],
                'check_number' => $payload['check_number'],
                'check_date' => $payload['check_date'],
                'check_amount' => (float) $payload['check_amount'],
                'collection_receipt_no' => $payload['collection_receipt_no'],
                'collection_receipt_date' => $payload['collection_receipt_date'],
                'collection_receipt_amount' => $receiptAmount,
                'applied_amount' => 0,
                'status' => 'pending',
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            $remainingReceiptCents = $receiptCents;
            $appliedTotalCents = 0;

            foreach ($invoices as $invoice) {
                if ($remainingReceiptCents <= 0) {
                    break;
                }

                $previousBalanceCents = $this->toCents($invoice->balance_amount);
                $appliedCents = min($previousBalanceCents, $remainingReceiptCents);
                $remainingBalanceCents = max($previousBalanceCents - $appliedCents, 0);
                $previousBalance = $this->fromCents($previousBalanceCents);
                $appliedAmount = $this->fromCents($appliedCents);
                $remainingBalance = $this->fromCents($remainingBalanceCents);

                $collection->invoices()->create([
                    'sales_invoice_id' => $invoice->id,
                    'sales_invoice_no' => $invoice->sales_invoice_no,
                    'subtotal' => (float) $invoice->subtotal,
                    'tax_amount' => (float) $invoice->tax_amount,
                    'total_invoice_amount' => (float) $invoice->total_amount,
                    'withholding_tax_amount' => (float) $invoice->withholding_tax_amount,
                    'previous_balance' => $previousBalance,
                    'applied_amount' => $appliedAmount,
                    'remaining_balance' => $remainingBalance,
                ]);

                $invoice->update([
                    'amount_paid' => $this->fromCents($this->toCents($invoice->amount_paid) + $appliedCents),
                    'balance_amount' => $remainingBalance,
                    'status' => $remainingBalanceCents <= 0 ? 'paid' : 'unpaid',
                    'updated_by' => auth()->id(),
                ]);

                $remainingReceiptCents -= $appliedCents;
                $appliedTotalCents += $appliedCents;
            }

            $collection->update(['applied_amount' => $this->fromCents($appliedTotalCents)]);

            app(AuditTrailService::class)->record(
                self::MODULE,
                'created',
                $collection,
                null,
                $collection->load('invoices')->toArray(),
                'Sales collection saved: '.$collection->collection_no
            );

            return $collection->refresh();
        });
    }

    private function toCents(float|int|string|null $amount): int
    {
        return (int) round(((float) ($amount ?? 0)) * 100);
    }

    private function fromCents(int $cents): float
    {
        return round($cents / 100, 2);
    }
}
