<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    //
    public function index()
    {
        $products = Product::with(['category'])->paginate(10);
        return view('pages.products.index', compact('products'));
    }

    // create
    public function create()
    {
        $categories = Category::all();
        return view('pages.products.create', compact('categories'));
    }

    // store
    public function store(Request $request)
    {
        $request->validate([
            "name" => 'required',
            "description" => 'required',
            "price" => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            "stock" => 'required|numeric',
            "status" => 'required|boolean',
            "is_favorite" => 'required|boolean',
        ]);

        $product = new Product;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->category_id = $request->category_id;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->status = $request->status;
        $product->is_favorite = $request->is_favorite;
        $product->save();


        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/products', $product->id . "." . $image->getClientOriginalExtension());
            $product->image = 'storage/products/' . $product->id . "." . $image->getClientOriginalExtension();
            $product->save();
        }



        return redirect()->route('products.index')->with("success", "Product created successfully");
    }

    // show 
    public function show($id)
    {
        return view('pages.products.show');
    }

    // edit
    public function edit($id)
    {
        $product = Product::find($id);
        $categories = DB::table('categories')->get();
        return view('pages.products.edit', compact(['product', 'categories']));
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);


        $request->validate([
            "name" => 'required',
            "description" => 'required',
            "price" => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            "stock" => 'required|numeric',
            "status" => 'required|boolean',
            "is_favorite" => 'required|boolean',
        ]);

        $product = Product::find($id);
        $product->name = $request->name;
        $product->description = $request->description;
        $product->category_id = $request->category_id;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->status = $request->status;
        $product->is_favorite = $request->is_favorite;
        $product->save();

        if ($request->hasFile('image')) {
            // delete existing file
            $splitText = explode("/", $product->image);
            $link = $splitText[1] . "/" . $splitText[2];
            Storage::delete("/public" . "/" . $link);

            $image = $request->file('image');
            $image->storeAs('public/products', $product->id . "." . $image->getClientOriginalExtension());
            $product->image = 'storage/products/' . $product->id . "." . $image->getClientOriginalExtension();
            $product->save();
        }

        return redirect()->route('products.index')->with("success", "Product updated successfully");
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if ($product->image) {
            $splitText = explode("/", $product->image);
            $link = $splitText[1] . "/" . $splitText[2];
            Storage::delete("/public" . "/" . $link);
        }

        $product->delete();
        return redirect()->route('products.index')->with("success", "Product deleted successfully");
    }
}
