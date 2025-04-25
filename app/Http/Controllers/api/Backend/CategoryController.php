<?php
namespace App\Http\Controllers\api\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }
        $categories = Category::where('type', $request->type)->paginate($request->per_page ?? 10);
        return response()->json([
            'status'  => true,
            'message' => 'Category retreived successfully',
            'data'    => $categories,
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
            'type' => 'required|string',
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }
        $category = Category::create([
            'type' => $request->type,
            'name' => $request->name,
        ]);
        return response()->json([
            'status'  => true,
            'message' => 'Category added successfully',
            'data'    => $category,
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
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }
        try {
            $category = Category::findOrFail($id);
            $category->update([
                'name' => $request->name,
            ]);
            return response()->json([
                'status'  => true,
                'message' => 'Category update successfully',
                'data'    => $category,
            ]);
        } catch (Exception $e) {
            Log::error('Category update error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Category not found',
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
            $category = Category::findOrFail($id);
            $category->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Category deleted successfully',
                'data'    => $category,
            ]);
        } catch (Exception $e) {
            Log::error('Category delete error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Category not found',
                'data'    => null,
            ]);
        }
    }
}
