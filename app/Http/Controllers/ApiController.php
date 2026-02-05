<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ApiController extends Controller
{

    public function getKeyWords() {

    }

    public function products(Request $request)
    {
        $products = Product::latest()->paginate(9);

        if ($request->ajax()) {
            return view('partials.product-cards', compact('products'))->render();
        }

        return view('products', compact('products'));
    }

}
