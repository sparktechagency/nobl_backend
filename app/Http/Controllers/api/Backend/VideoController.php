<?php
namespace App\Http\Controllers\api\Backend;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Video::with('category')->whereHas('category', function ($query) {
            $query->where('type', 'Video Category');
        });

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        $videos = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'status'  => true,
            'message' => 'Videos retrieved successfully',
            'data'    => $videos,
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
            'category_id' => 'required|numeric',
            'title'       => 'required|string|max:255',
            'thumbnail'   => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'video'       => 'required|mimes:mp4,mov,avi,wmv',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }
        $video              = new Video();
        $video->category_id = $request->category_id;
        $video->title       = $request->title;
        if ($request->hasFile('thumbnail')) {
            $final_name = time() . '.' . $request->thumbnail->extension();
            $request->thumbnail->move(public_path('uploads/videos/thumbnail'), $final_name);
            $video->thumbnail = $final_name;
        }
        if ($request->hasFile('video')) {
            $final_name = time() . '.' . $request->video->extension();
            $request->video->move(public_path('uploads/videos/video'), $final_name);
            $video->video = $final_name;
        }
        $video->save();
        return response()->json([
            'status'  => true,
            'message' => 'Video added successfully',
            'data'    => $video,
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
            'title'       => 'required|string|max:255',
            'thumbnail'   => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'video'       => 'sometimes|mimes:mp4,mov,avi,wmv',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }
        try {
            $video = Video::findOrFail($id);
            if ($request->hasFile('thumbnail')) {
                $photo_location     = public_path('uploads/videos/thumbnail');
                $old_photo          = basename($video->thumbnail);
                $old_photo_location = $photo_location . '/' . $old_photo;
                if (file_exists($old_photo_location)) {
                    unlink($old_photo_location);
                }

                $final_image_name = time() . '.' . $request->thumbnail->extension();
                $request->thumbnail->move($photo_location, $final_image_name);
                $video->thumbnail = $final_image_name;
            }
            if ($request->hasFile('video')) {
                $video_location     = public_path('uploads/videos/video');
                $old_video          = basename($video->video);
                $old_video_location = $video_location . '/' . $old_video;
                if (file_exists($old_video_location)) {
                    unlink($old_video_location);
                }

                $final_video_name = time() . '.' . $request->video->extension();
                $request->video->move($video_location, $final_video_name);
                $video->video = $final_video_name;
            }
            $video->category_id = $request->category_id;
            $video->title       = $request->title;
            $video->save();
            return response()->json([
                'status'  => true,
                'message' => 'Video update successfully',
                'data'    => $video,
            ]);
        } catch (Exception $e) {
            Log::error('Video update error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Video not found',
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
            $video = Video::findOrFail($id);
            // delete thumbnail
            $photo_location     = public_path('uploads/videos/thumbnail');
            $old_photo          = basename($video->thumbnail);
            $old_photo_location = $photo_location . '/' . $old_photo;
            if (file_exists($old_photo_location)) {
                unlink($old_photo_location);
            }
            //  delete video
            $video_location     = public_path('uploads/videos/video');
            $old_video          = basename($video->video);
            $old_video_location = $video_location . '/' . $old_video;
            if (file_exists($old_video_location)) {
                unlink($old_video_location);
            }
            $video->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Video deleted successfully',
                'data'    => $video,
            ]);
        } catch (Exception $e) {
            Log::error('Video delete error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Video not found',
                'data'    => null,
            ]);
        }
    }
}
