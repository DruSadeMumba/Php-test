<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->get();
        return view('welcome', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $product = Product::create($data);

        $this->saveToJson();

        return response()->json(['product' => $product]);
    }

    private function saveToJson()
    {
        $products = Product::all();
        Storage::disk('local')->put('products.json', $products->toJson(JSON_PRETTY_PRINT));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->only(['name', 'quantity', 'price']));
        $this->saveToJson();
        return response()->json(['product' => $product]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        $this->saveToJson();
        return response()->json(['success' => true]);
    }
}
