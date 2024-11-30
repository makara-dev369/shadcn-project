<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {

        $perPage = $request->input('per_page', 10);
        $status = $request->input('is_active', null);
        $sortField = $request->input('sort_field', 'name');
        $sortDirection = $request->input('sort_direction', 'desc');
        $filters = [];
        if (!empty($status)) {
            $filters[] = [
                'id' => 'is_active',
                'value' => $status
            ];
        }


        $products =  Product::query()->when($status, function ($query, $status) {
            if (is_array($status) && !empty($status)) {
                $query->whereIn('is_active', $status);
            }
        })->orderBy($sortField, $sortDirection)->paginate(perPage: $perPage);
        return Inertia::render('Product/Index', [
            'data' => $products,
            'filter' => $filters
        ]);
    }

    public function store(Request $request)
    {

        //Validate data 
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'title' => 'required',
            'category_ref_id' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        //Create Product Record
        $product = new Product();
        $product->name = $request->input('name');
        $product->title = $request->input('title');
        $product->description = $request->input('description');
        $product->category_ref_id = $request->input('category_ref_id');
        $product->brand_ref_id = $request->input('brand_ref_id');
        $product->price = $request->input('price');
        $product->discount_price = $request->input('discount_price');
        $product->benefits_content = $request->input('benefits_content');
        $product->ingredients_content = $request->input('ingredients_content');
        $product->howtouse_content = $request->input('howtouse_content');
        $product->product_size_id = $request->input('product_size_id');
        $product->is_active = $request->input('is_active');

        $product->save();

        $product->attechImages($request->input('image'));

        return redirect()->back()->with('success', 'Product Create Successful.');
    }

    public function edit($id)
    {
        $product = Product::with('image')->find($id);

        return response()->json(['data' => $product]);
    }

    public  function update(Request $request, $id)
    {
        //Validate data 
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'title' => 'required',
            'category_ref_id' => 'required',
        ]);



        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        //Create Product Record
        $product =  Product::find($id);

        if (empty($product)) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        $product->name = $request->input('name');
        $product->title = $request->input('title');
        $product->description = $request->input('description');
        $product->category_ref_id = $request->input('category_ref_id');
        $product->brand_ref_id = $request->input('brand_ref_id');
        $product->price = $request->input('price');
        $product->discount_price = $request->input('discount_price');
        $product->benefits_content = $request->input('benefits_content');
        $product->ingredients_content = $request->input('ingredients_content');
        $product->howtouse_content = $request->input('howtouse_content');
        $product->product_size_id = $request->input('product_size_id');
        $product->is_active = $request->input('is_active');

        $product->save();

        $product->syncImages($request->input('image'));

        return redirect()->back()->with('success', 'Product Update Successful.');
    }
}
