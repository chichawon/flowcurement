<?php

namespace App\Modules\Items\Livewire;

use App\Modules\Items\Livewire\Concerns\ManagesItemForm;
use App\Modules\Items\Models\Item;
use App\Modules\Items\Services\ItemService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;

class ItemEdit extends Component
{
    use AuthorizesRequests;
    use ManagesItemForm;
    use WithFileUploads;

    public function mount(int $item): void
    {
        $itemRecord = Item::query()->find($item);

        if (! $itemRecord) {
            $this->redirectRoute($this->indexRoute(), navigate: false);

            return;
        }

        $this->authorize('update', $itemRecord);
        $this->fillFromItem($itemRecord);
    }

    public function render()
    {
        return view('modules.items.livewire.form', [
            'title' => 'Edit '.str($this->itemSource())->headline().' Item',
            'submitLabel' => 'Save Item',
            'cancelRoute' => route($this->indexRoute()),
            'suppliers' => app(ItemService::class)->suppliers(),
            'itemTypes' => app(ItemService::class)->itemTypes(),
        ]);
    }
}
