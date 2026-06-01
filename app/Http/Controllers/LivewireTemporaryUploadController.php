<?php

namespace App\Http\Controllers;

use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\FileUploadController;

class LivewireTemporaryUploadController extends FileUploadController
{
    public function handle()
    {
        $disk = FileUploadConfiguration::disk();

        return [
            'paths' => $this->validateAndStore(request('files'), $disk),
        ];
    }
}
