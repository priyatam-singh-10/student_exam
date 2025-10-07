<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    public function generateReceipt(array $data, string $path): string
    {
        $pdf = Pdf::loadView('pdf.receipt', $data);
        Storage::disk('local')->put($path, $pdf->output());      
        return $path;
    }
}

