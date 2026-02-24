<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use App\Models\UserItemCategory;
use App\Models\UserItem;
use Illuminate\Http\Request;

class GalleryImageController extends Controller
{
    public function index()
    {
        $images = GalleryImage::with(['category', 'item'])->latest()->get();
        return view('admin.galleryimages.index', compact('images'));
    }

    public function create()
    {
        $categories = UserItemCategory::where('status', 1)->get();
        $items = UserItem::where('status', 1)->get();

        return view('admin.galleryimages.create', compact('categories', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'item_id'     => 'required',
            'image'       => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $fileName = null;
        if ($request->hasFile('image')) {
            $fileName = time() . '-' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/gallery'), $fileName);
        }

        GalleryImage::create([
            'category_id' => $request->category_id,
            'item_id'     => $request->item_id,
            'image'       => $fileName,
        ]);

        return redirect()->route('admin.galleryimages.index')
                         ->with('success', 'Image added successfully');
    }

    public function edit($id)
    {
        $image = GalleryImage::findOrFail($id);

        $categories = UserItemCategory::where('status', 1)->get();
        $items = UserItem::where('status', 1)->get();

        return view('admin.galleryimages.edit', compact('image', 'categories', 'items'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required',
            'item_id'     => 'required',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $image = GalleryImage::findOrFail($id);

        if ($request->hasFile('image')) {
            // delete old image
            if ($image->image && file_exists(public_path('uploads/gallery/' . $image->image))) {
                unlink(public_path('uploads/gallery/' . $image->image));
            }

            $fileName = time() . '-' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/gallery'), $fileName);

            $image->image = $fileName;
        }

        $image->category_id = $request->category_id;
        $image->item_id = $request->item_id;
        $image->save();

        return redirect()->route('admin.galleryimages.index')
                         ->with('success', 'Image updated successfully');
    }

    public function destroy($id)
    {
        $image = GalleryImage::findOrFail($id);

        if ($image->image && file_exists(public_path('uploads/gallery/' . $image->image))) {
            unlink(public_path('uploads/gallery/' . $image->image));
        }

        $image->delete();

        return back()->with('success', 'Image deleted successfully');
    }
}
