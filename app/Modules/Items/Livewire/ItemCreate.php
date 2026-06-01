<?php

namespace App\Modules\Items\Livewire;

use App\Modules\Items\Livewire\Concerns\ManagesItemForm;
use App\Modules\Items\Models\Item;
use App\Modules\Items\Services\ItemService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;

class ItemCreate extends Component
{
    use AuthorizesRequests;
    use ManagesItemForm;
    use WithFileUploads;

    public function mount(): void
    {
        $this->authorize('create', Item::class);
        $this->recalculateItemPrice();
    }

    public function render()
    {
        return view('modules.items.livewire.form', [
            'title' => 'Create '.str($this->itemSource())->headline().' Item',
            'submitLabel' => 'Create Item',
            'cancelRoute' => route($this->indexRoute()),
            'suppliers' => app(ItemService::class)->suppliers(),
            'itemTypes' => app(ItemService::class)->itemTypes(),
        ]);
    }
}
