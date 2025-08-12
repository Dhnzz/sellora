<?php

namespace App\Http\Controllers;

use App\Models\ProductBrand;
use Illuminate\Http\Request;

class FrontController
{
    public function index()
    {
        $data = [
            'product_brand' => ProductBrand::all()
        ];
        return view('front.home', compact('data'));
    }
}
