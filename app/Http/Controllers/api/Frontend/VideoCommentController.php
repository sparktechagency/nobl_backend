<?php
namespace App\Http\Controllers\api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\VideoComment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class VideoCommentController extends Controller
{
    public function storeComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_id' => 'required|exists:videos,id',
            'comment'  => 'required|string|max:1000',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }

        $comment           = new VideoComment();
        $comment->user_id  = Auth::id();
        $comment->video_id = $request->video_id;
        $comment->comment  = $request->comment;
        $comment->save();

        return response()->json([
            'status'  => true,
            'message' => 'Comment added successfully',
            'data'    => $comment,
        ]);
    }

    public function getComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ]);
        }

        $comments = VideoComment::where('video_id', $request->video_id)->with('user:id,name,photo')->select('id', 'user_id', 'video_id', 'comment', 'created_at')->latest('id')->paginate($request->per_page ?? 10);

        return response()->json([
            'status'  => true,
            'message' => 'Comments retrieved successfully',
            'data'    => $comments,
        ]);
    }

    public function deleteComment($id)
    {
        try {
            $comment = VideoComment::findOrFail($id);
            $comment->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Comment deleted successfully',
                'data'    => $comment,
            ]);
        } catch (Exception $e) {
            Log::error('Video comment delete error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Comment not found',
                'data'    => null,
            ]);
        }
    }
}
