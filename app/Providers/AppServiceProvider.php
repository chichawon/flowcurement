<?php

namespace App\Providers;

use App\Models\User;
use App\Http\Controllers\LivewireTemporaryUploadController;
use App\Modules\BusinessPartners\Livewire\ClientCreate;
use App\Modules\BusinessPartners\Livewire\ClientEdit;
use App\Modules\BusinessPartners\Livewire\ClientIndex;
use App\Modules\BusinessPartners\Livewire\SupplierCreate;
use App\Modules\BusinessPartners\Livewire\SupplierEdit;
use App\Modules\BusinessPartners\Livewire\SupplierIndex;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use App\Modules\BusinessPartners\Policies\BusinessPartnerPolicy;
use App\Modules\Items\Livewire\ItemCreate;
use App\Modules\Items\Livewire\ItemEdit;
use App\Modules\Items\Livewire\ImportItemCreate;
use App\Modules\Items\Livewire\ImportItemEdit;
use App\Modules\Items\Livewire\ImportItemsIndex;
use App\Modules\Items\Livewire\LocalItemCreate;
use App\Modules\Items\Livewire\LocalItemEdit;
use App\Modules\Items\Livewire\LocalItemsIndex;
use App\Modules\Items\Livewire\ItemsIndex;
use App\Modules\Items\Models\Item;
use App\Modules\Items\Policies\ItemPolicy;
use App\Modules\Quotations\Livewire\QuotationCreate;
use App\Modules\Quotations\Livewire\QuotationEdit;
use App\Modules\Quotations\Livewire\QuotationsIndex;
use App\Modules\Quotations\Models\Quotation;
use App\Modules\Quotations\Policies\QuotationPolicy;
use App\Modules\Sales\Livewire\Orders\Create as SalesOrderCreate;
use App\Modules\Sales\Livewire\Orders\Edit as SalesOrderEdit;
use App\Modules\Sales\Livewire\Orders\Index as SalesOrderIndex;
use App\Modules\Sales\Livewire\DeliveryReceipts\Create as DeliveryReceiptCreate;
use App\Modules\Sales\Livewire\DeliveryReceipts\Edit as DeliveryReceiptEdit;
use App\Modules\Sales\Livewire\DeliveryReceipts\Index as DeliveryReceiptIndex;
use App\Modules\Sales\Livewire\DeliveryReceipts\UploadDetails as DeliveryReceiptUploadDetails;
use App\Modules\Sales\Models\DeliveryReceipt;
use App\Modules\Sales\Models\SalesOrder;
use App\Modules\Sales\Policies\DeliveryReceiptPolicy;
use App\Modules\Sales\Policies\SalesOrderPolicy;
use App\Modules\UserManagement\Livewire\UsersIndex;
use App\Modules\UserManagement\Policies\UserPolicy;
use App\Support\LivewireSubfolderHandleRequests;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleRequests\HandleRequests;
use Livewire\Mechanisms\HandleRequests\EndpointResolver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->instance(HandleRequests::class, new LivewireSubfolderHandleRequests());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(BusinessPartner::class, BusinessPartnerPolicy::class);
        Gate::policy(Item::class, ItemPolicy::class);
        Gate::policy(Quotation::class, QuotationPolicy::class);
        Gate::policy(SalesOrder::class, SalesOrderPolicy::class);
        Gate::policy(DeliveryReceipt::class, DeliveryReceiptPolicy::class);

        Livewire::component('business-partners.client-index', ClientIndex::class);
        Livewire::component('business-partners.client-create', ClientCreate::class);
        Livewire::component('business-partners.client-edit', ClientEdit::class);
        Livewire::component('business-partners.supplier-index', SupplierIndex::class);
        Livewire::component('business-partners.supplier-create', SupplierCreate::class);
        Livewire::component('business-partners.supplier-edit', SupplierEdit::class);
        Livewire::component('items.index', ItemsIndex::class);
        Livewire::component('items.create', ItemCreate::class);
        Livewire::component('items.edit', ItemEdit::class);
        Livewire::component('items.local-index', LocalItemsIndex::class);
        Livewire::component('items.local-create', LocalItemCreate::class);
        Livewire::component('items.local-edit', LocalItemEdit::class);
        Livewire::component('items.import-index', ImportItemsIndex::class);
        Livewire::component('items.import-create', ImportItemCreate::class);
        Livewire::component('items.import-edit', ImportItemEdit::class);
        Livewire::component('quotations.index', QuotationsIndex::class);
        Livewire::component('quotations.create', QuotationCreate::class);
        Livewire::component('quotations.edit', QuotationEdit::class);
        Livewire::component('sales.orders.index', SalesOrderIndex::class);
        Livewire::component('sales.orders.create', SalesOrderCreate::class);
        Livewire::component('sales.orders.edit', SalesOrderEdit::class);
        Livewire::component('sales.delivery-receipts.index', DeliveryReceiptIndex::class);
        Livewire::component('sales.delivery-receipts.create', DeliveryReceiptCreate::class);
        Livewire::component('sales.delivery-receipts.edit', DeliveryReceiptEdit::class);
        Livewire::component('sales.delivery-receipts.upload-details', DeliveryReceiptUploadDetails::class);
        Livewire::component('user-management.users-index', UsersIndex::class);

        $this->app->booted(function (): void {
            Route::post(ltrim(EndpointResolver::uploadPath(), '/'), [LivewireTemporaryUploadController::class, 'handle'])
                ->middleware('web')
                ->name('livewire.upload-file');
        });

        if (! $this->app->runningInConsole() && $rootUrl = config('app.url')) {
            URL::forceRootUrl($rootUrl);
        }
    }
}
