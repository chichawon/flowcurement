<?php

namespace App\Modules\Items\Livewire;

use App\Modules\Items\Models\Item;

class LocalItemEdit extends ItemEdit
{
    public function mount(int $item): void
    {
        $itemRecord = Item::query()->local()->find($item);

        if (! $itemRecord) {
            $this->redirectRoute($this->indexRoute(), navigate: false);

            return;
        }

        $this->authorize('update', $itemRecord);
        $this->fillFromItem($itemRecord);
    }

    protected function itemSource(): string
    {
        return 'local';
    }

    protected function indexRoute(): string
    {
        return 'local-items.index';
    }
}
