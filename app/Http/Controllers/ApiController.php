<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;

class ApiController extends Controller
{

    public function getKeyWords() {

    }

    public function searchSuggest(Request $request)
    {
        $search = trim($request->get('query', ''));

        if (mb_strlen($search) < 1) {
            return response()->json([]);
        }

        $products = Product::query()
            ->with('keywords')
            ->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('article', 'LIKE', "%{$search}%")
                  ->orWhereHas('keywords', function ($q2) use ($search) {
                      $q2->where('name', 'LIKE', "%{$search}%");
                  });
            })
            ->limit(10)
            ->get();

        $results = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'article' => $product->article,
                'gender' => $product->gender,
                'image_url' => $product->image_url,
                'keywords_string' => $product->keywords_string,
            ];
        });

        return response()->json($results);
    }

    public function brands()
    {
        return response()->json(Brand::orderBy('name')->get(['id', 'name']));
    }

    public function products(Request $request)
    {
        $data = [];
        $products = Product::query()->with(['keywords', 'brand']);
        if ($request->filled('gender')) {
            $genders = array_intersect(
                explode(',', $request->get('gender')),
                ['male', 'female', 'unisex']
            );
            if (count($genders) === 1) {
                $products->where('gender', reset($genders));
            } elseif (count($genders) > 1) {
                $products->whereIn('gender', $genders);
            }
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

        if ($request->filled('brand_id')) {
            $products->where('brand_id', $request->get('brand_id'));
        }

        if ($request->filled('quality')) {
            $qualities = array_intersect(
                explode(',', $request->get('quality')),
                ['premium', 'top']
            );
            if (count($qualities) === 1) {
                $products->where('quality', reset($qualities));
            } elseif (count($qualities) > 1) {
                $products->whereIn('quality', $qualities);
            }
        }

        if ($request->filled('is_new') && $request->get('is_new') == '1') {
            $products->where('is_new', true);
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
