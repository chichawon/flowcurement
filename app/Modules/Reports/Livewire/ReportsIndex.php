<?php

namespace App\Modules\Reports\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ReportsIndex extends Component
{
    use AuthorizesRequests;

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('reports.view'), 403);
    }

    public function render()
    {
        return view('modules.reports.livewire.index');
    }
}
