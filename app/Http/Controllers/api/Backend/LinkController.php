<?php
namespace App\Http\Controllers\api\Backend;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $links = Link::paginate($request->per_page ?? 10);
        return response()->json([
            'status'  => true,
            'message' => 'Links retrieved successfully',
            'data'    => $links,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'link_type' => 'required|string|max:100',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'link'      => 'required|url',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }

        $link            = new Link();
        $link->link_type = $request->link_type;
        $link->link      = $request->link;
        if ($request->hasFile('thumbnail')) {
            $final_name = time() . '.' . $request->thumbnail->extension();
            $request->thumbnail->move(public_path('uploads/links'), $final_name);
            $link->thumbnail = $final_name;
        }
        $link->save();
        return response()->json([
            'status'  => true,
            'message' => 'Link added successfully',
            'data'    => $link,
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
            'link_type' => 'required|string|max:100',
            'thumbnail' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'link'      => 'required|url',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }
        try {
            $link = Link::findOrFail($id);
            if ($request->hasFile('thumbnail')) {
                $photo_location     = public_path('uploads/links');
                $old_photo          = basename($link->thumbnail);
                $old_photo_location = $photo_location . '/' . $old_photo;
                if (file_exists($old_photo_location)) {
                    unlink($old_photo_location);
                }

                $final_image_name = time() . '.' . $request->thumbnail->extension();
                $request->thumbnail->move($photo_location, $final_image_name);
                $link->thumbnail = $final_image_name;
            }

            $link->link_type = $request->link_type;
            $link->link      = $request->link;
            $link->save();
            return response()->json([
                'status'  => true,
                'message' => 'Link update successfully',
                'data'    => $link,
            ]);
        } catch (Exception $e) {
            Log::error('Link update error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Link not found',
                'data'    => null,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $link = Link::findOrFail($id);
            // delete thumbnail
            $photo_location     = public_path('uploads/links');
            $old_photo          = basename($link->thumbnail);
            $old_photo_location = $photo_location . '/' . $old_photo;
            if (file_exists($old_photo_location)) {
                unlink($old_photo_location);
            }

            $link->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Link deleted successfully',
                'data'    => $link,
            ]);
        } catch (Exception $e) {
            Log::error('Link delete error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Link not found',
                'data'    => null,
            ]);
        }
    }
}
