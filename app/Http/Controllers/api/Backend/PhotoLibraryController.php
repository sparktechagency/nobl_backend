<?php
namespace App\Http\Controllers\api\Backend;

use App\Http\Controllers\Controller;
use App\Models\PhotoLibrary;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PhotoLibraryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $query = PhotoLibrary::with('category')->whereHas('category', function ($query) {
            $query->where('type', 'Image Category');
        });

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // if (! $request->has('category_id')) {
        //     $query->inRandomOrder();
        // }

        $photos = $query->latest('id')->paginate($request->per_page ?? 10);

        return response()->json([
            'status' => true,
            'message' => 'Photos retrieved successfully',
            'data' => $photos,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|numeric',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ]);
        }
        $photo = new PhotoLibrary();
        $photo->category_id = $request->category_id;
        if ($request->hasFile('photo')) {
            $final_name = time() . '.' . $request->photo->extension();
            $request->photo->move(public_path('uploads/photo_library'), $final_name);
            $photo->photo = $final_name;
        }
        $photo->save();
        return response()->json([
            'status' => true,
            'message' => 'Photo added successfully',
            'data' => $photo,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|numeric',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ]);
        }
        try {
            $photo = PhotoLibrary::findOrFail($id);
            if ($request->hasFile('photo')) {
                $photo_location = public_path('uploads/photo_library');
                $old_photo = basename($photo->photo);
                $old_photo_location = $photo_location . '/' . $old_photo;
                if (file_exists($old_photo_location)) {
                    unlink($old_photo_location);
                }

                $final_image_name = time() . '.' . $request->photo->extension();
                $request->photo->move($photo_location, $final_image_name);
                $photo->photo = $final_image_name;
            }
            $photo->save();
            return response()->json([
                'status' => true,
                'message' => 'Photo update successfully',
                'data' => $photo,
            ]);
        } catch (Exception $e) {
            Log::error('Photo update error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Photo not found',
                'data' => null,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $photo = PhotoLibrary::findOrFail($id);
            $photo_location = public_path('uploads/photo_library');
            $old_photo = basename($photo->photo);
            $old_photo_location = $photo_location . '/' . $old_photo;
            if (file_exists($old_photo_location)) {
                unlink($old_photo_location);
            }
            $photo->delete();
            return response()->json([
                'status' => true,
                'message' => 'Photo deleted successfully',
                'data' => $photo,
            ]);
        } catch (Exception $e) {
            Log::error('Photo delete error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Photo not found',
                'data' => null,
            ]);
        }
    }
}
