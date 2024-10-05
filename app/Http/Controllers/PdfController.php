<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function export_pdf($id)
    {

        // Fetch all customers from database
        $productData['product'] = Product::find($id);
        $productData['gate_passes'] = $productData['product']->gatePasses;

        // dd($productData);
        // Send data to the view using loadView function of PDF facade
        $pdf = PDF::loadView('pdf-view-product', compact('productData'));

        // If you want to store the generated pdf to the server then you can use the store function
        $pdf->save(storage_path() . '_filename.pdf');

        // Finally, you can download the file using download function
        return $pdf->download($productData['product']->name.'_balance_sheet.pdf');
    }
}
