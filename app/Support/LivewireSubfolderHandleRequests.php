<?php

namespace App\Support;

use Livewire\Mechanisms\HandleRequests\HandleRequests;

class LivewireSubfolderHandleRequests extends HandleRequests
{
    public function getUpdateUri()
    {
        $uri = parent::getUpdateUri();
        $basePath = trim((string) parse_url((string) config('app.url'), PHP_URL_PATH), '/');

        if ($basePath === '') {
            return $uri;
        }

        $prefix = '/'.$basePath.'/livewire-';

        if (str_starts_with($uri, $prefix)) {
            return substr($uri, strlen('/'.$basePath)) ?: $uri;
        }

        return $uri;
    }
}
