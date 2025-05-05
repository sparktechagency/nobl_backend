<?php
namespace App\Http\Controllers\api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Audio;
use App\Models\Document;
use App\Models\PhotoLibrary;
use App\Models\Video;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // documents collect
        $documents = Document::with('category')
            ->get()
            ->map(function ($item) {
                return [
                    'id'            => $item->id,
                    'category_id'   => $item->category_id,
                    'type'          => 'document',
                    'title'         => $item->title,
                    'document_type' => $item->document_type,
                    'thumbnail'     => $item->thumbnail,
                    'file'          => $item->file,
                    'video'         => null,
                    'audio'         => null,
                    'created_at'    => $item->created_at,
                    'category'      => optional($item->category)->name,
                ];
            });

        // videos collect
        $videos = Video::with('category')
            ->get()
            ->map(function ($item) {
                return [
                    'id'            => $item->id,
                    'category_id'   => $item->category_id,
                    'type'          => 'video',
                    'title'         => $item->title,
                    'document_type' => null,
                    'thumbnail'     => $item->thumbnail,
                    'file'          => null,
                    'video'         => $item->video,
                    'audio'         => null,
                    'created_at'    => $item->created_at,
                    'category'      => optional($item->category)->name,
                ];
            });

        // audios collect
        $audios = Audio::with('category')
            ->get()
            ->map(function ($item) {
                return [
                    'id'            => $item->id,
                    'category_id'   => $item->category_id,
                    'type'          => 'audio',
                    'title'         => $item->title,
                    'document_type' => null,
                    'thumbnail'     => $item->thumbnail,
                    'file'          => null,
                    'video'         => null,
                    'audio'         => $item->audio,
                    'created_at'    => $item->created_at,
                    'category'      => optional($item->category)->name,
                ];
            });

        // photos collect
        $photos = PhotoLibrary::with('category')
            ->get()
            ->map(function ($item) {
                return [
                    'id'            => $item->id,
                    'category_id'   => $item->category_id,
                    'type'          => 'photo',
                    'title'         => null,
                    'document_type' => null,
                    'thumbnail'     => $item->photo,
                    'file'          => null,
                    'video'         => null,
                    'audio'         => null,
                    'created_at'    => $item->created_at,
                    'category'      => optional($item->category)->name,
                ];
            });

        // merge all and latest sorting
        $activities = $documents
            ->merge($videos)
            ->merge($audios)
            ->merge($photos)
            ->sortByDesc('created_at')
            ->take(20)
            ->values();

        return response()->json([
            'status'  => true,
            'message' => 'Recent activities retreived successfully',
            'data'    => $activities,
        ]);
    }
}
