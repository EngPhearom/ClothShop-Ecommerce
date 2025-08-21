<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    //
    public function index()
    {
        return view('admin.index');
    }

    public function brand()
    {
        $brands = Brand::orderBy('id', 'DESC')->paginate(10);
        return view('admin.brand', compact('brands'));
    }

    public function brand_add()
    {
        return view('admin.brand-add');
    }

    public function brand_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'unique:brands,slug',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $brands = new Brand();
        $brands->name = $request->name;
        $brands->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateBrandThumbailsImage($image, $file_name);
        $brands->image = $file_name;
        $brands->save();
        return redirect()->route('admin.brand')->with('status', 'Brand added successfully');
    }

    public function GenerateBrandThumbailsImage($image, $imageName)
    {
        $destaintionPath = public_path('uploads/brands');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destaintionPath . '/' . $imageName);
    }

    public function brand_edit($id)
    {
        $brands = Brand::find($id);
        return view('admin.brand-edit', compact('brands'));
    }

    public function brand_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'unique:brands,slug,' . $request->id,
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $brands = Brand::find($request->id);
        $brands->name = $request->name;
        $brands->slug = Str::slug($request->name);
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/brands/') . '/' . $brands->image)) {
                File::delete(public_path('uploads/brands/') . '/' . $brands->image);
            }
            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateBrandThumbailsImage($image, $file_name);
            $brands->image = $file_name;
        }

        $brands->save();
        return redirect()->route('admin.brand')->with('status', 'Brand updated successfully');
    }

    public function brand_delete($id)
    {
        $brands = Brand::find($id);
        if (File::exists(public_path('uploads/brands/') . '/' . $brands->image)) {
            File::delete(public_path('uploads/brands/') . '/' . $brands->image);
        }
        $brands->delete();
        return redirect()->route('admin.brand')->with('status', 'Brand deleted successfully');
    }

    public function category()
    {
        $categories = Category::orderBy('id', 'DESC')->paginate(10);
        return view('admin.category', compact('categories'));
    }

    public function category_add()
    {
        return view('admin.category-add');
    }

    public function category_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'unique:categories,slug',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $cateogries = new Category();
        $cateogries->name = $request->name;
        $cateogries->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateCategoryThumbailsImage($image, $file_name);
        $cateogries->image = $file_name;
        $cateogries->save();
        return redirect()->route('admin.category')->with('status', 'category added successfully');
    }

    public function GenerateCategoryThumbailsImage($image, $imageName)
    {
        $destaintionPath = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destaintionPath . '/' . $imageName);
    }

    public function category_edit($id)
    {
        $categories = Category::find($id);
        return view('admin.category-edit', compact('categories'));
    }

    public function category_update(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'unique:categories,slug,' . $request->id,
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $categories = Category::find($request->id);
        $categories->name = $request->name;
        $categories->slug = Str::slug($request->name);
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/categories/') . '/' . $categories->image)) {
                File::delete(public_path('uploads/categories/') . '/' . $categories->image);
            }
            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateCategoryThumbailsImage($image, $file_name);
            $categories->image = $file_name;
        }

        $categories->save();
        return redirect()->route('admin.category')->with('status', 'Category updated successfully');
    }

    public function category_delete($id){
        $categories = Category::find($id);
        if (File::exists(public_path('uploads/categories/') . '/' . $categories->image)) {
            File::delete(public_path('uploads/categories/') . '/' . $categories->image);
        }
        $categories->delete();
        return redirect()->route('admin.category')->with('status', 'Category deleted successfully');
    }
}
