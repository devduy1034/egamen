<?php

/******************************************************************************
 * LARAVEL VIỆT NAM
 * Email: LARAVEL@LARAVEL.vn
 * Website: LARAVEL.vn
 * Version: 1.0
 * Đây là tài sản của CÔNG TY TNHH TM DV LARAVEL. Vui lòng không sử dụng khi chưa được phép.
 */


namespace LARAVEL\Controllers\Admin;

use Illuminate\Http\Request;
use LARAVEL\Models\CounterModel;
use Carbon\Carbon;
use DB;

class HomeController
{
    public function index(Request $request)
    {
        if ((isset($request->month) && $request->month != '') && (isset($request->year) && $request->year != '')) {
            $time = $request->year . '-' . $request->month . '-1';
            $date = strtotime($time);
        } else {
            $date = strtotime(date('y-m-d'));
        }
        $day = date('d', $date);
        $month = date('m', $date);
        $year = date('Y', $date);
        $firstDay = mktime(0, 0, 0, $month, 1, $year);
        $dayOfWeek = date('D', $firstDay);
        $daysInMonth = cal_days_in_month(0, $month, $year);
        $timestamp = strtotime('next Sunday');
        $weekDays = array();
        /* Make data for js chart */
        $charts = array();
        $charts['month'] = $month;
        $startDate = Carbon::create($year, $month, 1, 0, 0, 0)->timestamp;
        $endDate = Carbon::create($year, $month, 28, 23, 59, 59)->timestamp;
        // Truy vấn duy nhất lấy tổng số lượt truy cập theo từng ngày trong tháng
        $records = CounterModel::selectRaw('DATE(FROM_UNIXTIME(tm)) as date, COUNT(*) as total')
            ->whereBetween('tm', [$startDate, $endDate])
            ->groupBy('date')
            ->pluck('total', 'date');

        // Tạo dữ liệu cho biểu đồ
        $charts = ['series' => [], 'labels' => []];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::create($year, $month, $i)->toDateString(); // Tạo chuỗi ngày: YYYY-MM-DD
            $charts['series'][] = $records[$date] ?? 0; // Lấy dữ liệu hoặc mặc định là 0
            $charts['labels'][] = 'D' . $i;
        }
        $data = CounterModel::select(
            DB::raw("COUNT(CASE WHEN browser <> '' THEN 1 END) as countBrowser"),
            DB::raw("COUNT(CASE WHEN device <> '' AND device <> 'robot' THEN 1 END) as countDevice")
        )
            ->addSelect([
                'browser_data' => CounterModel::selectRaw("JSON_ARRAYAGG(JSON_OBJECT('browser', browser, 'count', total))")
                    ->fromSub(
                        CounterModel::select('browser', DB::raw('COUNT(*) as total'))
                            ->where('browser', '<>', '')
                            ->groupBy('browser'),
                        'browser_subquery'
                    ),

                'device_data' => CounterModel::selectRaw("JSON_ARRAYAGG(JSON_OBJECT('device', device, 'count', total))")
                    ->fromSub(
                        CounterModel::select('device', DB::raw('COUNT(*) as total'))
                            ->where('device', '<>', '')
                            ->where('device', '<>', 'robot')
                            ->groupBy('device'),
                        'device_subquery'
                    ),

                'topIP' => CounterModel::selectRaw("JSON_ARRAYAGG(JSON_OBJECT('ip', ip, 'visits', total))")
                    ->fromSub(
                        CounterModel::select('ip', DB::raw('COUNT(*) as total'))
                            ->groupBy('ip')
                            ->orderBy('total', 'desc')
                            ->limit(5),
                        'ip_subquery'
                    )
            ])
            ->first();
        $browser = json_decode($data->browser_data, true);
        $device = json_decode($data->device_data, true);
        $topIP = json_decode($data->topIP, true);
        $countBrowser = $data->countBrowser;
        $countDevice = $data->countDevice;
        return view('index.index', compact(
            'charts',
            'day',
            'month',
            'year',
            'browser',
            'countBrowser',
            'device',
            'countDevice',
            'topIP'
        ));
    }
}