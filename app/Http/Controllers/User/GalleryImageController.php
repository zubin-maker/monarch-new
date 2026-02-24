<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use App\Models\User\UserItemCategory;
use App\Models\User\{UserItem,UserItemContent};
use Illuminate\Http\Request;

class GalleryImageController extends Controller
{
    public function index()
    {  
      
        $images = GalleryImage::with('itemContents', 'item')->latest()->get();
        return view('user.gallery_images.index', compact('images'));
    }

    public function create()
    {
        $categories = UserItemCategory::where('user_id',11)->where('language_id',17)->get();
      
        $items = UserItem::where('user_id',11)->where('status', 1)->with('itemContents')->get();
        return view('user.gallery_images.create', compact('categories', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            
            'item_id'     => 'required',
            'image'       => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $fileName = null;
        if ($request->hasFile('image')) {
            $fileName = time() . '-' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/gallery'), $fileName);
        }

        GalleryImage::create([
            
            'item_id'     => $request->item_id,
            'image'       => $fileName,
        ]);

        return redirect()->route('user.gallery.index')
                         ->with('success', 'Image added successfully');
    }

    public function edit($id)
    {
        $image = GalleryImage::findOrFail($id);

        $categories = UserItemCategory::where('status', 1)->get();
        $items = UserItem::where('user_id',11)->where('status', 1)->with('itemContents')->get();

        return view('user.gallery_images.edit', compact('image', 'categories', 'items'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            
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

        // $image->category_id = $request->category_id;
        $image->item_id = $request->item_id;
        $image->save();

        return redirect()->route('user.gallery.index')
                         ->with('success', 'Image updated successfully');
    }

  public function destroy($id)
{
    $gallery = GalleryImage::findOrFail($id);
    $gallery->delete();

    return redirect()->route('user.gallery.index')->with('success', 'Image deleted successfully.');
}

    
    public function getItemsByCategory(Request $request)
{
    $items = UserItemContent::whereHas('item', function($q) use ($request){
        $q->where('category_id', $request->category_id)
          ->where('user_id',11);
    })->get();

    return response()->json($items);
}
}
