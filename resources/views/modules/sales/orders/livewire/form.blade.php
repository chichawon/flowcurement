<form wire:submit.prevent="save" class="space-y-5">
    <section class="erp-panel">
        <div class="erp-panel-header flex items-center justify-between gap-3">
            <h3 class="text-sm font-semibold text-slate-950">{{ $title }}</h3>
            @if ($salesOrderRecord)
                <span class="inline-flex rounded-md px-2.5 py-1 text-xs font-semibold uppercase {{ $status === 'served' ? 'bg-emerald-600 text-white' : ($status === 'partial' ? 'bg-cyan-700 text-white' : ($status === 'pending' ? 'bg-amber-600 text-white' : 'bg-slate-500 text-white')) }}">
                    {{ str($status)->headline() }}
                </span>
            @endif
        </div>
        <div class="erp-panel-body space-y-4">
            <div class="grid gap-3 lg:grid-cols-4">
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Sales Order No.</span>
                    <input type="text" value="{{ $sales_order_no }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-950 shadow-sm">
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Order Date</span>
                    <input type="date" wire:model.live="order_date" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring" @disabled($attachmentOnlyMode)>
                    @error('order_date') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">No. of Days</span>
                    <input type="number" wire:model.live="no_of_days" min="0" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring" @disabled($attachmentOnlyMode)>
                    @error('no_of_days') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Delivery Date</span>
                    <input type="date" wire:model="delivery_date" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
                    @error('delivery_date') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                </label>
            </div>

            <div class="grid gap-3 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)_minmax(0,1fr)]">
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Company Name</span>
                    <select wire:model.live="business_partner_id" class="mt-1 block h-10 w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring" @disabled($attachmentOnlyMode)>
                        <option value="">Select client</option>
                        @foreach ($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                        @endforeach
                    </select>
                    @error('business_partner_id') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                </label>
                <x-admin.form-field label="Agent Name" name="agent_name" wire:model.blur="agent_name" required :disabled="$attachmentOnlyMode" />
                <x-admin.form-field label="Customer PO" name="customer_po" wire:model.blur="customer_po" :disabled="$attachmentOnlyMode" />
            </div>

            <div class="grid gap-3 lg:grid-cols-4">
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Terms</span>
                    <input type="text" value="{{ $terms }} days" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Company Address</span>
                    <input type="text" wire:model="company_address" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Contact Person</span>
                    <input type="text" wire:model="contact_person" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Contact No.</span>
                    <input type="text" wire:model="contact_no" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
                </label>
            </div>

            <label class="block">
                <span class="text-sm font-medium text-slate-700">Remarks</span>
                <input type="text" wire:model.blur="remarks" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring" @disabled($attachmentOnlyMode)>
                @error('remarks') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
            </label>
        </div>
    </section>

    @php
    $savedAttachmentUrl = $existing_po_attachment ? Storage::disk('public')->url($existing_po_attachment) : null;
    $savedAttachmentName = $existing_po_attachment ? basename($existing_po_attachment) : '';
    $savedAttachmentExtension = strtolower(pathinfo($savedAttachmentName, PATHINFO_EXTENSION));
    $savedAttachmentKind = in_array($savedAttachmentExtension, ['jpg', 'jpeg', 'png', 'webp'], true)
    ? 'image'
    : ($savedAttachmentExtension === 'pdf' ? 'pdf' : '');
    @endphp

    <section
        class="erp-panel"
        x-data="{
            previewUrl: '',
            fileName: '',
            fileKind: '',
            savedUrl: @js($savedAttachmentUrl),
            savedName: @js($savedAttachmentName),
            savedKind: @js($savedAttachmentKind),
            setFile(event) {
                const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
                if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                this.previewUrl = '';
                this.fileName = '';
                this.fileKind = '';
                if (! file) return;
                this.previewUrl = URL.createObjectURL(file);
                this.fileName = file.name;
                if (file.type.startsWith('image/')) this.fileKind = 'image';
                else if (file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf')) this.fileKind = 'pdf';
                else this.fileKind = 'file';
            },
            activeUrl() { return this.previewUrl || this.savedUrl || ''; },
            activeName() { return this.fileName || this.savedName || 'No attachment selected'; },
            activeKind() { return this.fileKind || this.savedKind || ''; },
        }">
        <div class="erp-panel-header flex items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-semibold text-slate-950">Customer P.O. Attachment</h3>
                <p class="mt-1 text-xs text-slate-500">Attach the customer's purchase order for reference.</p>
            </div>
            @if ($existing_po_attachment)
            <a href="{{ $savedAttachmentUrl }}" target="_blank" class="text-xs font-semibold text-cyan-700 hover:text-cyan-800">View Current</a>
            @endif
        </div>
        <div class="erp-panel-body">
            <p class="text-xs font-semibold uppercase text-slate-500">Upload Attachment</p>
            <label class="mt-2 flex min-h-40 cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-white px-4 py-7 text-center shadow-sm transition hover:border-cyan-400 hover:bg-cyan-50/40">
                <input type="file" wire:model="po_attachment_upload" accept=".pdf,.jpg,.jpeg,.png,.webp" class="sr-only" @change="setFile($event)">
                <span class="grid size-12 place-items-center rounded-full bg-cyan-100 text-cyan-700">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5h10.5A2.25 2.25 0 0 0 19.5 17.25V6.75A2.25 2.25 0 0 0 17.25 4.5H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5A2.25 2.25 0 0 0 6.75 19.5Z" />
                    </svg>
                </span>
                <span class="mt-3 text-sm font-semibold text-slate-950">Choose customer P.O. file</span>
                <span class="mt-1 text-xs text-slate-500">PDF, JPG, PNG, or WEBP up to 5MB</span>
            </label>

            <div class="mt-3 flex flex-col gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex min-w-0 items-center gap-3">
                    <span class="grid size-11 shrink-0 place-items-center rounded-md bg-white text-cyan-700 ring-1 ring-slate-200">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H6.75A2.25 2.25 0 0 0 4.5 4.5v15A2.25 2.25 0 0 0 6.75 21h10.5a2.25 2.25 0 0 0 2.25-2.25v-4.5Z" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-950" x-text="activeName()"></p>
                        <p class="text-xs text-slate-500" x-text="activeUrl() ? 'Attachment ready for preview.' : 'No attachment selected.'"></p>
                    </div>
                </div>

                <button
                    type="button"
                    x-bind:disabled="! activeUrl()"
                    @click="if (activeUrl()) window.open(activeUrl(), '_blank')"
                    class="inline-flex items-center justify-center rounded-md bg-cyan-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-cyan-700 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500">
                    Preview
                </button>
            </div>

            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs">
                <span wire:loading wire:target="po_attachment_upload" class="font-semibold text-cyan-700">Uploading attachment...</span>
                <span x-show="previewUrl" x-cloak class="font-semibold text-emerald-700">New attachment selected.</span>
                @if ($existing_po_attachment)
                <a href="{{ $savedAttachmentUrl }}" target="_blank" class="font-semibold text-cyan-700 hover:text-cyan-800">View current attachment</a>
                @endif
            </div>

            @error('po_attachment_upload') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
        </div>
    </section>

    <section class="erp-panel">
        <div class="erp-panel-header flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <h3 class="text-sm font-semibold text-slate-950">Items</h3>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" wire:click="openQuickItemModal" class="inline-flex items-center rounded-md bg-cyan-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-cyan-700 erp-focus-ring disabled:cursor-not-allowed disabled:opacity-50" @disabled($attachmentOnlyMode)>Add New Item</button>
                <button type="button" wire:click="openQuotationModal" class="inline-flex items-center rounded-md border border-cyan-200 bg-cyan-50 px-3 py-2 text-sm font-semibold text-cyan-800 shadow-sm hover:border-cyan-300 hover:bg-cyan-100 erp-focus-ring disabled:cursor-not-allowed disabled:opacity-50" @disabled($attachmentOnlyMode)>Add Items from Quotation</button>
                <button type="button" wire:click="addRow" class="inline-flex items-center rounded-md bg-slate-950 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 erp-focus-ring disabled:cursor-not-allowed disabled:opacity-50" @disabled($attachmentOnlyMode)> <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>Add Row</button>
            </div>
        </div>
        <div class="erp-panel-body">
            @if ($attachmentOnlyMode)
            <div class="mb-3 rounded-md border border-amber-300 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-800">
                Item panel is locked because a Delivery Receipt is already issued. Only P.O. attachment can be updated.
            </div>
            @endif
            <div class="relative overflow-visible border border-slate-400 bg-white">
                <table class="w-full table-fixed border-collapse text-sm">
                    <colgroup>
                        <col class="w-[20%]">
                        <col class="w-[18%]">
                        <col class="w-[10%]">
                        <col class="w-[10%]">
                        <col class="w-[12%]">
                        <col class="w-[10%]">
                        <col class="w-[12%]">
                        <col class="w-14">
                    </colgroup>
                    <thead class="bg-slate-200 text-xs font-bold uppercase text-slate-700">
                        <tr>
                            <th class="border border-slate-400 px-2 py-2 text-left">Item</th>
                            <th class="border border-slate-400 px-2 py-2 text-left">Description</th>
                            <th class="border border-slate-400 px-2 py-2 text-center">Qty</th>
                            <th class="border border-slate-400 px-2 py-2 text-left">Unit</th>
                            <th class="border border-slate-400 px-2 py-2 text-right">Price</th>
                            <th class="border border-slate-400 px-2 py-2 text-right">Stock</th>
                            <th class="border border-slate-400 px-2 py-2 text-right">Total</th>
                            <th class="border border-slate-400 px-2 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $index => $row)
                        @php($rowKey = $row['row_key'] ?? $row['_key'] ?? 'row-'.$index)
                        <tr wire:key="sales-order-item-row-{{ $rowKey }}">
                            <td class="border border-slate-300 p-2 align-top">
                                <select wire:key="sales-order-item-select-{{ $rowKey }}-{{ $row['item_id'] ?? 'empty' }}" wire:model.live="items.{{ $index }}.item_id" class="block h-9 w-full border border-slate-400 text-sm erp-focus-ring" @disabled($attachmentOnlyMode)>
                                    <option value="">Select item</option>
                                    @foreach ($availableItems as $item)
                                    <option wire:key="sales-order-item-option-{{ $rowKey }}-{{ $item->id }}" value="{{ $item->id }}">{{ $item->item_name }}</option>
                                    @endforeach
                                </select>
                                @error('items.'.$index.'.item_id') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                            </td>
                            <td class="border border-slate-300 p-2 align-top">
                                <input type="text" wire:model.blur="items.{{ $index }}.description" class="block h-9 w-full border border-slate-400 px-2 text-sm erp-focus-ring" @disabled($attachmentOnlyMode)>
                                @error('items.'.$index.'.description') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                            </td>
                            <td class="border border-slate-300 p-2 align-top">
                                <input type="number" wire:model.live.debounce.250ms="items.{{ $index }}.order_quantity" min="1" step="1" class="block h-9 w-full border border-slate-400 px-2 text-center text-sm erp-focus-ring" @disabled($attachmentOnlyMode)>
                                @error('items.'.$index.'.order_quantity') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                            </td>
                            <td class="border border-slate-300 p-2 align-top">
                                <select wire:key="sales-order-unit-select-{{ $rowKey }}-{{ $row['unit_measure_id'] ?? 'empty' }}" wire:model.live="items.{{ $index }}.unit_measure_id" class="block h-9 w-full border border-slate-400 text-sm erp-focus-ring" @disabled($attachmentOnlyMode)>
                                    <option value="">Unit</option>
                                    @foreach ($unitMeasures as $unit)
                                    <option wire:key="sales-order-unit-option-{{ $rowKey }}-{{ $unit->id }}" value="{{ $unit->id }}">{{ str($unit->name)->headline() }}</option>
                                    @endforeach
                                </select>
                                @error('items.'.$index.'.unit_measure_id') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                            </td>
                            <td class="border border-slate-300 p-2 align-top">
                                <input type="number" wire:model.live.debounce.250ms="items.{{ $index }}.price" min="0" step="0.01" class="block h-9 w-full border border-slate-400 bg-white px-2 text-right text-sm font-semibold text-slate-950 erp-focus-ring" @disabled($attachmentOnlyMode)>
                                @error('items.'.$index.'.price') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                            </td>
                            <td class="border border-slate-300 p-2 align-top">
                                <input type="text" value="{{ number_format((float) ($row['available_stock'] ?? 0), 2) }}" readonly class="block h-9 w-full border border-slate-300 bg-slate-100 px-2 text-right text-sm text-slate-700">
                            </td>
                            <td class="border border-slate-300 p-2 align-top">
                                <input type="text" value="{{ number_format((float) ($row['total'] ?? 0), 2) }}" readonly class="block h-9 w-full border border-slate-300 bg-slate-100 px-2 text-right text-sm font-semibold text-slate-950">
                            </td>
                            <td class="border border-slate-300 p-2 text-center align-top">
                                <button type="button" wire:click="removeRow({{ $index }})" class="inline-flex size-9 items-center justify-center border border-red-700 bg-red-600 text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50" aria-label="Remove row" @disabled($attachmentOnlyMode)>
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @error('items') <span class="mt-2 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
        </div>
    </section>

    <section class="erp-panel">
        <div class="erp-panel-header">
            <h3 class="text-sm font-semibold text-slate-950">Summary</h3>
        </div>
        <div class="erp-panel-body space-y-4">
            <div class="grid gap-3 lg:grid-cols-2">
                <x-admin.select-field label="Currency" name="currency" wire:model.live="currency" required :disabled="$attachmentOnlyMode">
                    <option value="php">PHP</option>
                    <option value="dollar">Dollar</option>
                </x-admin.select-field>
                <x-admin.select-field label="Tax Rate" name="tax_rate" wire:model.live="tax_rate" required :disabled="$attachmentOnlyMode">
                    <option value="0">0%</option>
                    <option value="12">12%</option>
                </x-admin.select-field>
            </div>
            <div class="overflow-hidden border border-slate-400 bg-white">
                <table class="w-full table-fixed border-collapse text-sm">
                    <thead class="bg-slate-200 text-xs font-bold uppercase text-slate-700">
                        <tr>
                            <th class="border border-slate-400 px-3 py-2 text-left">Summary</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Subtotal</td>
                            <td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format($subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Tax Amount</td>
                            <td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format($tax_amount, 2) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-950 text-white">
                            <td class="border border-slate-950 px-3 py-3 text-sm font-bold uppercase">Total Amount</td>
                            <td class="border border-slate-950 px-3 py-3 text-right text-base font-bold">{{ number_format($total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>

    <div class="sticky bottom-0 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
        <a href="{{ $cancelRoute }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="submit" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">{{ $submitLabel }}</button>
    </div>

    <div x-data="{ open: @entangle('showQuickItemModal').live }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-slate-950/60" @click="$wire.closeQuickItemModal()"></div>
        <div class="relative w-full max-w-lg rounded-xl bg-white shadow-2xl">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-base font-semibold text-slate-950">Add New Item</h3>
            </div>
            <div class="space-y-4 px-5 py-4">
                <x-admin.select-field label="Origin" name="quick_item_source" wire:model.live="quick_item_source">
                    <option value="local">Local</option>
                    <option value="import">Imported</option>
                </x-admin.select-field>
                <x-admin.form-field label="Item Name" name="quick_item_name" wire:model.blur="quick_item_name" />
                <div class="grid gap-3 sm:grid-cols-3">
                    <x-admin.form-field label="Supplier Price" name="quick_supplier_price" type="number" step="0.01" wire:model.live="quick_supplier_price" />
                    <x-admin.form-field label="Markup %" name="quick_markup_percentage" type="number" step="0.01" wire:model.live="quick_markup_percentage" />
                    <label class="block"><span class="text-sm font-medium text-slate-700">Item Price</span><input type="text" value="{{ $quick_item_price }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-950 shadow-sm"></label>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-200 px-5 py-4"><button type="button" wire:click="closeQuickItemModal" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700">Cancel</button><button type="button" wire:click="createQuickItem" class="rounded-md bg-cyan-600 px-3 py-2 text-sm font-semibold text-white hover:bg-cyan-700">Add Item</button></div>
        </div>
    </div>

    <div x-data="{ open: @entangle('showQuotationModal').live }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-slate-950/60" @click="$wire.closeQuotationModal()"></div>
        <div class="relative w-full max-w-lg rounded-xl bg-white shadow-2xl">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-base font-semibold text-slate-950">Add Items from Quotation</h3>
            </div>
            <div class="px-5 py-4">
                <x-admin.select-field label="Quotation" name="selected_quotation_id" wire:model.live="selected_quotation_id">
                    <option value="">Select quotation</option>
                    @foreach ($quotations as $quotation)
                    <option value="{{ $quotation->id }}">{{ $quotation->quotation_no }} - {{ $quotation->businessPartner?->company_name }}</option>
                    @endforeach
                </x-admin.select-field>
                @error('selected_quotation_id') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-200 px-5 py-4"><button type="button" wire:click="closeQuotationModal" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700">Cancel</button><button type="button" wire:click="loadQuotationItems" class="rounded-md bg-cyan-600 px-3 py-2 text-sm font-semibold text-white hover:bg-cyan-700">Load Items</button></div>
        </div>
    </div>
</form>
