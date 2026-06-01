<?php

namespace App\Modules\Items\Livewire;

class ImportItemCreate extends ItemCreate
{
    public function mount(): void
    {
        parent::mount();
        $this->item_source = 'import';
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
