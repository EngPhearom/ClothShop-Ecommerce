<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
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

    public function category_update(Request $request)
    {
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

    public function category_delete($id)
    {
        $categories = Category::find($id);
        if (File::exists(public_path('uploads/categories/') . '/' . $categories->image)) {
            File::delete(public_path('uploads/categories/') . '/' . $categories->image);
        }
        $categories->delete();
        return redirect()->route('admin.category')->with('status', 'Category deleted successfully');
    }

    public function product()
    {
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.product', compact('products'));
    }

    public function product_add()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.product-add', compact('categories', 'brands'));
    }

    public function product_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'mimes:png,jpg,jpeg,gif,svg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required'
        ]);

        $products = new Product();
        $products->name = $request->name;
        $products->slug = Str::slug($request->name);
        $products->short_description = $request->short_description;
        $products->description = $request->description;
        $products->regular_price = $request->regular_price;
        $products->sale_price = $request->sale_price;
        $products->SKU = $request->SKU;
        $products->stock_status = $request->stock_status;
        $products->featured = $request->featured;
        $products->quantity = $request->quantity;
        $products->category_id = $request->category_id;
        $products->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbailsImage($image, $imageName);
            $products->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;

        if ($request->hasFile('images')) {
            $allowedfileExtion = ['jpg', 'png', 'jpeg', 'gif', 'svg'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedfileExtion);
                if ($gcheck) {
                    $gfileName = $current_timestamp . '.' . $counter . '.' . $gextension;
                    $this->GenerateProductThumbailsImage($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
        }

        $products->images = $gallery_images;
        $products->save();
        return redirect()->route('admin.product')->with('status', 'Product added successfully');
    }

    public function GenerateProductThumbailsImage($image, $imageName)
    {
        $destaintionPathThumbnail = public_path('uploads/products/thumbnails');
        $destaintionPath = public_path('uploads/products');
        $img = Image::read($image->path());

        $img->cover(540, 689, "top");
        $img->resize(540, 689, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destaintionPath . '/' . $imageName);

        $img->resize(104, 104, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destaintionPathThumbnail . '/' . $imageName);
    }

    public function product_edit($id)
    {
        $products = Product::find($id);
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.product-edit', compact('products', 'categories', 'brands'));
    }

    public function product_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,' . $request->id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'mimes:png,jpg,jpeg,gif,svg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required'
        ]);

        $products = Product::find($request->id);
        $products->name = $request->name;
        $products->slug = Str::slug($request->name);
        $products->short_description = $request->short_description;
        $products->description = $request->description;
        $products->regular_price = $request->regular_price;
        $products->sale_price = $request->sale_price;
        $products->SKU = $request->SKU;
        $products->stock_status = $request->stock_status;
        $products->featured = $request->featured;
        $products->quantity = $request->quantity;
        $products->category_id = $request->category_id;
        $products->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/products') . '/' . $products->image)) {
                File::delete(public_path('uploads/products') . '/' . $products->image);
            }
            if (File::exists(public_path('uploads/products/thumbnails') . '/' . $products->image)) {
                File::delete(public_path('uploads/products/thumbnails') . '/' . $products->image);
            }
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbailsImage($image, $imageName);
            $products->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;

        if ($request->hasFile('images')) {
            foreach (explode(',', $products->images) as $ofile) {
                if (File::exists(public_path('uploads/products') . '/' . $ofile)) {
                    File::delete(public_path('uploads/products') . '/' . $ofile);
                }
                if (File::exists(public_path('uploads/products/thumbnails') . '/' . $ofile)) {
                    File::delete(public_path('uploads/products/thumbnails') . '/' . $ofile);
                }
            }

            $allowedfileExtion = ['jpg', 'png', 'jpeg', 'gif', 'svg'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedfileExtion);
                if ($gcheck) {
                    $gfileName = $current_timestamp . '.' . $counter . '.' . $gextension;
                    $this->GenerateProductThumbailsImage($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
            $products->images = $gallery_images;
        }

        $products->save();
        return redirect()->route('admin.product')->with('status', 'Product updated successfully');
    }

    public function product_delete($id)
    {
        $products = Product::find($id);
        if (File::exists(public_path('uploads/products') . '/' . $products->image)) {
            File::delete(public_path('uploads/products') . '/' . $products->image);
        }
        if (File::exists(public_path('uploads/products/thumbnails') . '/' . $products->image)) {
            File::delete(public_path('uploads/products/thumbnails') . '/' . $products->image);
        }

        foreach (explode(',', $products->images) as $ofile) {
            if (File::exists(public_path('uploads/products') . '/' . $ofile)) {
                File::delete(public_path('uploads/products') . '/' . $ofile);
            }
            if (File::exists(public_path('uploads/products/thumbnails') . '/' . $ofile)) {
                File::delete(public_path('uploads/products/thumbnails') . '/' . $ofile);
            }
        }

        $products->delete();
        return redirect()->route('admin.product')->with('status', 'Product deleted successfully');
    }
}
