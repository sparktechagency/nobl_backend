<?php
namespace App\Http\Controllers\api\Backend;

use App\Http\Controllers\Controller;
use App\Models\AppOpenCount;
use App\Models\Document;
use App\Models\PhotoLibrary;
use App\Models\User;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $type            = $request->query('type', 'Weekly');
        $total_users     = User::where('role', 'USER')->count();
        $total_videos    = Video::count();
        $total_images    = PhotoLibrary::count();
        $total_documents = Document::count();
        //user_statistics
        $user_statistics = collect();

        if ($type === 'Weekly') {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek   = Carbon::now()->endOfWeek();

            $days = ["Sat", "Sun", "Mon", "Tue", "Wed", "Thu", "Fri"];

            $data = AppOpenCount::select(
                DB::raw('DATE_FORMAT(created_at, "%a") as day'),
                DB::raw('COUNT(*) as total')
            )
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->groupBy('day')
                ->get()
                ->pluck('total', 'day');

            $user_statistics = collect();
            foreach ($days as $day) {
                $user_statistics->push([
                    'day'   => $day,
                    'total' => $data->get($day, 0),
                ]);
            }

        } elseif ($type === 'Monthly') {
            $startOfYear = Carbon::now()->startOfYear();
            $endOfYear   = Carbon::now()->endOfYear();

            $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

            $data = AppOpenCount::select(
                DB::raw('DATE_FORMAT(created_at, "%b") as month'),
                DB::raw('COUNT(*) as total')
            )
                ->whereBetween('created_at', [$startOfYear, $endOfYear])
                ->groupBy('month')
                ->get()
                ->pluck('total', 'month');

            $user_statistics = collect();
            foreach ($months as $month) {
                $user_statistics->push([
                    'month' => $month,
                    'total' => $data->get($month, 0),
                ]);
            }

        } elseif ($type === 'Yearly') {
            $data = AppOpenCount::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as total')
            )
                ->groupBy('year')
                ->orderBy('year', 'ASC')
                ->get()
                ->map(function ($item) {
                    return [
                        'year'  => $item->year,
                        'total' => $item->total,
                    ];
                });

            $user_statistics = $data;
        }
        $data = [
            'total_users'     => $total_users,
            'total_videos'    => $total_videos,
            'total_images'    => $total_images,
            'total_documents' => $total_documents,
            'user_statistics' => $user_statistics,
        ];
        return response()->json([
            'status'  => true,
            'message' => 'Dashboard data retreived successfully.',
            'data'    => $data,
        ]);
    }
}
