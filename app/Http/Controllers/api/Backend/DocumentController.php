<?php
namespace App\Http\Controllers\api\Backend;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $query = Document::with('category')->whereHas('category', function ($query) {
            $query->where('type', 'Documents Category');
        });

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        $documents = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'status'  => true,
            'message' => 'Documents retrieved successfully',
            'data'    => $documents,
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
            'category_id'   => 'required|numeric',
            'document_type' => 'required|string|max:10',
            'title'         => 'required|string|max:255',
            'thumbnail'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'file'          => 'required|mimes:pdf,doc,docx',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }

        $document                = new Document();
        $document->category_id   = $request->category_id;
        $document->document_type = $request->document_type;
        $document->title         = $request->title;
        if ($request->hasFile('thumbnail')) {
            $final_name = time() . '.' . $request->thumbnail->extension();
            $request->thumbnail->move(public_path('uploads/documents/thumbnail'), $final_name);
            $document->thumbnail = $final_name;
        }
        if ($request->hasFile('file')) {
            $final_name = time() . '.' . $request->file->extension();
            $request->file->move(public_path('uploads/documents/file'), $final_name);
            $document->file = $final_name;
        }
        $document->save();
        return response()->json([
            'status'  => true,
            'message' => 'Document added successfully',
            'data'    => $document,
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
            'category_id'   => 'required|numeric',
            'document_type' => 'required|string|max:10',
            'title'         => 'required|string|max:255',
            'thumbnail'     => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'file'          => 'sometimes|mimes:pdf,doc,docx',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }
        try {
            $document = Document::findOrFail($id);
            if ($request->hasFile('thumbnail')) {
                $photo_location     = public_path('uploads/documents/thumbnail');
                $old_photo          = basename($document->thumbnail);
                $old_photo_location = $photo_location . '/' . $old_photo;
                if (file_exists($old_photo_location)) {
                    unlink($old_photo_location);
                }

                $final_image_name = time() . '.' . $request->thumbnail->extension();
                $request->thumbnail->move($photo_location, $final_image_name);
                $document->thumbnail = $final_image_name;
            }
            if ($request->hasFile('file')) {
                $file_location     = public_path('uploads/documents/file');
                $old_file          = basename($document->file);
                $old_file_location = $file_location . '/' . $old_file;
                if (file_exists($old_file_location)) {
                    unlink($old_file_location);
                }

                $final_file_name = time() . '.' . $request->file->extension();
                $request->file->move($file_location, $final_file_name);
                $document->file = $final_file_name;
            }
            $document->category_id   = $request->category_id;
            $document->title         = $request->title;
            $document->document_type = $request->document_type;
            $document->save();
            return response()->json([
                'status'  => true,
                'message' => 'Document update successfully',
                'data'    => $document,
            ]);
        } catch (Exception $e) {
            Log::error('Document update error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Document not found',
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
            $document = Document::findOrFail($id);
            // delete thumbnail
            $photo_location     = public_path('uploads/documents/thumbnail');
            $old_photo          = basename($document->thumbnail);
            $old_photo_location = $photo_location . '/' . $old_photo;
            if (file_exists($old_photo_location)) {
                unlink($old_photo_location);
            }
            //  delete file
            $file_location     = public_path('uploads/documents/file');
            $old_file          = basename($document->file);
            $old_file_location = $file_location . '/' . $old_file;
            if (file_exists($old_file_location)) {
                unlink($old_file_location);
            }
            $document->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Document deleted successfully',
                'data'    => $document,
            ]);
        } catch (Exception $e) {
            Log::error('Document delete error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Document not found',
                'data'    => null,
            ]);
        }
    }
}
