<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    //index
    public function index()
    {
        $categories = DB::table('categories')->paginate(10);
        return view('pages.category.index', compact('categories'));
    }

    public function create()
    {
        return view('pages.category.create');
    }

    public function store(Request $request)
    {
        // validate request
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $category = new Category;
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();

        if ($request->hasFile('image')) {


            $image = $request->file('image');
            $image->storeAs('public/categories', $category->id . "." . $image->getClientOriginalExtension());
            $category->image = 'storage/categories/' . $category->id . "." . $image->getClientOriginalExtension();
            $category->save();
        }

        return redirect()->route('categories.index')->with('success', 'Category created successfully');
    }

    // show
    public function show()
    {
        return view('pages.category.show');
    }

    public function edit($id)
    {
        $category = Category::find($id);
        return view('pages.category.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $category = Category::find($id);
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();

        if ($request->hasFile('image')) {
            $splitText = explode("/", $category->image);
            $link = $splitText[1] . "/" . $splitText[2];
            Storage::delete("/public" . "/" . $link);

            $image = $request->file('image');
            $image->storeAs('public/categories', $category->id . "." . $image->getClientOriginalExtension());
            $category->image = 'storage/categories/' . $category->id . "." . $image->getClientOriginalExtension();
            $category->save();
        }

        return redirect()->route('categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if ($category->image) {
            $splitText = explode("/", $category->image);
            $link = $splitText[1] . "/" . $splitText[2];
            Storage::delete("/public" . "/" . $link);
        }

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
    }
}
