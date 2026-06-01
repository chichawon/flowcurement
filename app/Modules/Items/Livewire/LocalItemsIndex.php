<?php

namespace App\Modules\Items\Livewire;

class LocalItemsIndex extends ItemsIndex
{
    protected function itemSource(): ?string
    {
        return 'local';
    }

    protected function routePrefix(): string
    {
        return 'local-items';
    }

    protected function title(): string
    {
        return 'Local Items';
    }
}
