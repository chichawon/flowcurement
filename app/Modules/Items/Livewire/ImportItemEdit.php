<?php

namespace App\Modules\Items\Livewire;

use App\Modules\Items\Models\Item;

class ImportItemEdit extends ItemEdit
{
    public function mount(int $item): void
    {
        $itemRecord = Item::query()->import()->find($item);

        if (! $itemRecord) {
            $this->redirectRoute($this->indexRoute(), navigate: false);

            return;
        }

        $this->authorize('update', $itemRecord);
        $this->fillFromItem($itemRecord);
    }

    protected function itemSource(): string
    {
        return 'import';
    }

    protected function indexRoute(): string
    {
        return 'import-items.index';
    }
}
