<?php

namespace App\Http\Controllers\api\Backend;

use Exception;
use App\Models\Audio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AudioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Audio::with('category')->whereHas('category', function ($query) {
            $query->where('type', 'Audio Category');
        });

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        $audios = $query->latest('id')->paginate($request->per_page ?? 10);

        return response()->json([
            'status'  => true,
            'message' => 'Audios retrieved successfully',
            'data'    => $audios,
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
            'audio'       => 'required|mimes:mp3,mpeg',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }
        $audio              = new Audio();
        $audio->category_id = $request->category_id;
        $audio->title       = $request->title;
        if ($request->hasFile('thumbnail')) {
            $final_name = time() . '.' . $request->thumbnail->extension();
            $request->thumbnail->move(public_path('uploads/audios/thumbnail'), $final_name);
            $audio->thumbnail = $final_name;
        }
        if ($request->hasFile('audio')) {
            $final_name = time() . '.' . $request->audio->extension();
            $request->audio->move(public_path('uploads/audios/audio'), $final_name);
            $audio->audio = $final_name;
        }
        $audio->save();
        return response()->json([
            'status'  => true,
            'message' => 'Audio added successfully',
            'data'    => $audio,
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
            'audio'       => 'sometimes|mimes:mp3,mpeg',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }
        try {
            $audio = Audio::findOrFail($id);
            if ($request->hasFile('thumbnail')) {
                $photo_location     = public_path('uploads/audios/thumbnail');
                $old_photo          = basename($audio->thumbnail);
                $old_photo_location = $photo_location . '/' . $old_photo;
                if (file_exists($old_photo_location)) {
                    unlink($old_photo_location);
                }

                $final_image_name = time() . '.' . $request->thumbnail->extension();
                $request->thumbnail->move($photo_location, $final_image_name);
                $audio->thumbnail = $final_image_name;
            }
            if ($request->hasFile('audio')) {
                $audio_location     = public_path('uploads/audios/audio');
                $old_audio          = basename($audio->audio);
                $old_audio_location = $audio_location . '/' . $old_audio;
                if (file_exists($old_audio_location)) {
                    unlink($old_audio_location);
                }

                $final_audio_name = time() . '.' . $request->audio->extension();
                $request->audio->move($audio_location, $final_audio_name);
                $audio->audio = $final_audio_name;
            }
            $audio->category_id = $request->category_id;
            $audio->title       = $request->title;
            $audio->save();
            return response()->json([
                'status'  => true,
                'message' => 'Audio update successfully',
                'data'    => $audio,
            ]);
        } catch (Exception $e) {
            Log::error('Audio update error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Audio not found',
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
            $audio = Audio::findOrFail($id);
            // delete thumbnail
            $photo_location     = public_path('uploads/audios/thumbnail');
            $old_photo          = basename($audio->thumbnail);
            $old_photo_location = $photo_location . '/' . $old_photo;
            if (file_exists($old_photo_location)) {
                unlink($old_photo_location);
            }
            //  delete audio
            $audio_location     = public_path('uploads/audios/audio');
            $old_audio          = basename($audio->audio);
            $old_audio_location = $audio_location . '/' . $old_audio;
            if (file_exists($old_audio_location)) {
                unlink($old_audio_location);
            }
            $audio->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Audio deleted successfully',
                'data'    => $audio,
            ]);
        } catch (Exception $e) {
            Log::error('Audio delete error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Audio not found',
                'data'    => null,
            ]);
        }
    }

    public function relatedAudios(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'audio_id'    => 'required',
            'category_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }
        $related_audios = Audio::with('category')->where('category_id', $request->category_id)->where('id', '!=', $request->audio_id)->latest('id')->take(10)->get();
        return response()->json([
            'status'  => true,
            'message' => 'Related audio retreived successfully',
            'data'    => $related_audios,
        ]);
    }
}
