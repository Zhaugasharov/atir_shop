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
        $data = [];
        $products = Product::query()->with('keywords');
        $gender = $request->get('gender', '');

        if (in_array($gender, ['male', 'female', 'unisex'])) {
            $products->where('gender', $request->gender);
        }

        if ($request->filled('query')) {
            $search = trim($request->get('query'));

            $products->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('article', 'LIKE', "%{$search}%")
                  ->orWhereHas('keywords', function ($q2) use ($search) {
                      $q2->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if(!empty($request->get('homePage')))
            $data['homePage'] = 1;

        $data['products'] = $products->latest()
                                ->paginate(10)
                                ->withQueryString();

        if ($request->ajax()) {
            $locale = $request->get('locale', '');

            if(in_array($locale, ['ru', 'kk', 'en']))
                app()->setLocale($locale);

            return view('partials.product-cards', $data)->render();
        }

        return view('products', $data);
    }

}
