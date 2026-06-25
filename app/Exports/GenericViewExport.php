<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GenericViewExport implements FromView, ShouldAutoSize
{
    public function __construct(
        private string $view,
        private array $data
    ) {
    }

    public function view(): View
    {
        return view($this->view, $this->data);
    }
}