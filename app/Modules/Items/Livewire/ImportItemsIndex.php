<?php

namespace App\Modules\Items\Livewire;

class ImportItemsIndex extends ItemsIndex
{
    protected function itemSource(): ?string
    {
        return 'import';
    }

    protected function routePrefix(): string
    {
        return 'import-items';
    }

    protected function title(): string
    {
        return 'Import Items';
    }
}
