<?php

namespace App\Modules\Items\Livewire;

class LocalItemCreate extends ItemCreate
{
    public function mount(): void
    {
        parent::mount();
        $this->item_source = 'local';
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
