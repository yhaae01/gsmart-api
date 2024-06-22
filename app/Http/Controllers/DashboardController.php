<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use Carbon\Carbon;
use App\Models\ {
    Product,
    CancelCategory,
    Area,
    AMS,
    ExportDashboard
};
use App\Exports\DashboardExport;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    private $array_months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    public function export()
    {
        return new DashboardExport();
    }

    public function cancel(Request $request)
    {
        $user = auth()->user();
        $year = $request->year ?? false;
        $categories = CancelCategory::where('name', '!=', 'N/A')->get();

        $missed_plan = Sales::user($user)->cancel()->cancelCategory('Missed Plan')->year($year)->sum('value');
        $pricing = Sales::user($user)->cancel()->cancelCategory('Pricing')->year($year)->sum('value');
        $reschedule = Sales::user($user)->cancel()->cancelCategory('Reschedule')->year($year)->sum('value');
        $capacity_capability = Sales::user($user)->cancel()->cancelCategory('Capacity Capability')->year($year)->sum('value');
        $internal_customer = Sales::user($user)->cancel()->cancelCategory('Internal Customer')->year($year)->sum('value');
        $customer_financial = Sales::user($user)->cancel()->cancelCategory('Customer Financial')->year($year)->sum('value');

        $internal_issue = (float)number_format(($missed_plan + $pricing + $reschedule + $capacity_capability) / 1000000, 2);
        $external_issue = (float)number_format(($internal_customer + $customer_financial) / 1000000, 2);

        $area1 = [];
        $area2 = [];
        $area3 = [];
        $kam_qg = [];
        $kam_ga = [];
        $bar = [];

        foreach ($categories as $category) {
            $area1[] = (float)number_format((Sales::user($user)->area('I')->cancel()->cancelCategory($category->name)->year($year)->sum('value') / 1000000), 2);
            $area2[] = (float)number_format((Sales::user($user)->area('II')->cancel()->cancelCategory($category->name)->year($year)->sum('value') / 1000000), 2);
            $area3[] = (float)number_format((Sales::user($user)->area('III')->cancel()->cancelCategory($category->name)->year($year)->sum('value') / 1000000), 2);
            $kam_qg[] = (float)number_format((Sales::user($user)->customerName('Citilink Indonesia')->cancel()->cancelCategory($category->name)->year($year)->sum('value') / 1000000), 2);
            $kam_ga[] = (float)number_format((Sales::user($user)->customerName('Garuda Indonesia')->cancel()->cancelCategory($category->name)->year($year)->sum('value') / 1000000), 2);
        }

        $areas = ['I', 'II', 'III', 'KAM QG', 'KAM GA'];
        $datas = [$area1, $area2, $area3, $kam_qg, $kam_ga];

        for ($i = 0; $i < 5; $i++) {
            $bar[] = [
                'name' => $areas[$i],
                'data' => $datas[$i]
            ];
        }

        $data = [
            'pie' => [
                $internal_issue,
                $external_issue,
            ],
            'bar' => $bar,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data succesfully',
            'data' => $data,
        ], 200);
    }

    public function total(Request $request)
    {
        $user = auth()->user();
        $year = $request->year ?? false;

        $rkap_target = (float)number_format((Sales::user($user)->rkap()->year($year)->sum('value') / 1000000), 2);
        $rkap_progress = (float)number_format((Sales::user($user)->rkap()->year($year)->level(1)->clean()->sum('value') / 1000000), 2);
        $rkap_percentage = ($rkap_target == 0) ? 0 : (float)number_format((($rkap_progress / $rkap_target) * 100), 2);

        $additional_target = (float)number_format((Sales::user($user)->additional()->year($year)->sum('value') / 1000000), 2);
        $additional_progress = (float)number_format((Sales::user($user)->additional()->year($year)->level(1)->clean()->sum('value') / 1000000), 2);
        $additional_percentage = ($additional_target == 0) ? 0 : (float)number_format((($additional_progress / $additional_target) * 100), 2);

        $data = [
            'pie' => [$rkap_target, $additional_target],
            'bar' => [
                'target' => [$rkap_target, $additional_target],
                'progress' => [$rkap_progress, $additional_progress],
                'percentage' => [
                    "RKAP ({$rkap_percentage}%)", 
                    "Additional ({$additional_percentage}%)"
                ],
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data succesfully',
            'data' => $data,
        ], 200);
    }

    public function area(Request $request)
    {
        $user = auth()->user();
        $year = $request->year ?? false;

        $areas = ['I', 'II', 'III', 'KAM GA', 'KAM QG'];
        $array_target = [];
        $array_progress = [];
        $array_percentage = [];

        for ($i = 0; $i < count($areas); $i++) {
            $target = (float)number_format((Sales::user($user)->rkap()->area($areas[$i])->year($year)->sum('value') / 1000000), 2);
            $progress = (float)number_format((Sales::user($user)->rkap()->area($areas[$i])->year($year)->level(1)->clean()->sum('value') / 1000000), 2);
            $percentage = ($target == 0) ? 0 : (float)number_format((($progress / $target) * 100), 2);

            $array_target[] = $target;
            $array_progress[] = $progress;
            $array_percentage[] = $areas[$i]." (". $percentage ."%)";
        }

        $data = [
            'pie' => $array_target,
            'bar' => [
                'target' => $array_target,
                'progress' => $array_progress,
                'percentage' => $array_percentage,
            ],
        ];

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data succesfully',
            'data' => $data,
        ], 200);
    }

    public function group(Request $request)
    {
        $user = auth()->user();
        $year = $request->year ?? false;

        $groups = [0, 1];
        $group_names = ['GA', 'NGA'];
        $array_target = [];
        $array_progress = [];
        $array_percentage = [];

        for ($i = 0; $i < count($groups); $i++) {
            $target = (float)number_format((Sales::user($user)->rkap()->year($year)->groupType($groups[$i])->sum('value') / 1000000), 2);
            $progress = (float)number_format((Sales::user($user)->rkap()->year($year)->groupType($groups[$i])->level(1)->clean()->sum('value') / 1000000), 2);
            $percentage = ($target == 0) ? 0 : (float)number_format((($progress / $target) * 100), 2);

            $array_target[] = $target;
            $array_progress[] = $progress;
            $array_percentage[] = $group_names[$i]." (". $percentage ."%)";;
        }

        $data = [
            'pie' => $array_target,
            'bar' => [
                'target' => $array_target,
                'progress' => $array_progress,
                'percentage' => $array_percentage,
            ],
        ];

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data succesfully',
            'data' => $data,
        ], 200);
    }

    public function product(Request $request)
    {
        $user = auth()->user();
        $year = $request->year ?? false;

        $product_names = Product::pluck('name')->toArray();
        $array_target = [];
        $array_progress = [];
        $array_percentage = [];

        for ($i = 0; $i < count($product_names); $i++) {
            $target = (float)number_format((Sales::user($user)->year($year)->rkap()->product($product_names[$i])->sum('value') / 1000000), 2);
            $progress = (float)number_format((Sales::user($user)->year($year)->rkap()->product($product_names[$i])->level(1)->clean()->sum('value') / 1000000), 2);
            $percentage = ($target == 0) ? 0 : (float)number_format((($progress / $target) * 100), 2);
            $product = $product_names[$i];

            $array_target[] = $target;
            $array_progress[] = $progress;
            $array_percentage[] = $product." (". $percentage ."%)";
            $label[] = $product_names[$i];
        }

        $data = [
            'pie' => [
                'target' => $array_target,
                'label' => $label
            ],
            'bar' => [
                'target' => $array_target,
                'progress' => $array_progress,
                'percentage' => $array_percentage,
            ],
        ];
        
        return response()->json([
            'success' => true,
            'message' => 'Retrieve data succesfully',
            'data' => $data,
        ], 200);
    }

    public function amsArea1(Request $request)
    {
        $user = auth()->user();
        $year = $request->year ?? false;

        $ams_names = AMS::pluck('initial')->toArray();
        $array_target = [];
        $array_progress = [];
        $array_percentage = [];
        $label = [];

        for ($i = 0; $i < count($ams_names); $i++) {
            $target = (float)number_format((Sales::user($user)->year($year)->areaByAms('I', $ams_names[$i])->ams($ams_names[$i])->sum('value') / 1000000), 2);
            $progress = (float)number_format((Sales::user($user)->year($year)->areaByAms('I', $ams_names[$i])->ams($ams_names[$i])->level(1)->clean()->sum('value') / 1000000), 2);
            
            if ($target != 0 || $progress != 0) {
                $percentage = ($target == 0) ? 0 : (float)number_format((($progress / $target) * 100), 2);
                $ams = $ams_names[$i];

                $array_target[] = $target;
                $array_progress[] = $progress;
                $array_percentage[] = $ams . " (" . $percentage . "%)";
                $label[] = $ams_names[$i];
            }
        }

        $data = [
            'pie' => [
                'target' => $array_target,
                'label' => $label
            ],
            'bar' => [
                'target' => $array_target,
                'progress' => $array_progress,
                'percentage' => $array_percentage,
            ],
        ];

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data successfully',
            'data' => $data,
        ], 200);
    }

    public function amsArea2(Request $request)
    {
        $user = auth()->user();
        $year = $request->year ?? false;

        $ams_names = AMS::pluck('initial')->toArray();
        $array_target = [];
        $array_progress = [];
        $array_percentage = [];
        $label = [];

        for ($i = 0; $i < count($ams_names); $i++) {
            $target = (float)number_format((Sales::user($user)->year($year)->areaByAms('II', $ams_names[$i])->sum('value') / 1000000), 2);
            $progress = (float)number_format((Sales::user($user)->year($year)->areaByAms('II', $ams_names[$i])->level(1)->clean()->sum('value') / 1000000), 2);

            if ($target != 0 || $progress != 0) {
                $percentage = ($target == 0) ? 0 : (float)number_format((($progress / $target) * 100), 2);
                $ams = $ams_names[$i];

                $array_target[] = $target;
                $array_progress[] = $progress;
                $array_percentage[] = $ams." (". $percentage ."%)";
                $label[] = $ams_names[$i];
            }
        }

        $data = [
            'pie' => [
                'target' => $array_target,
                'label' => $label
            ],
            'bar' => [
                'target' => $array_target,
                'progress' => $array_progress,
                'percentage' => $array_percentage,
            ],
        ];
        
        return response()->json([
            'success' => true,
            'message' => 'Retrieve data succesfully',
            'data' => $data,
        ], 200);
    }

    public function amsArea3(Request $request)
    {
        $user = auth()->user();
        $year = $request->year ?? false;

        $ams_names = AMS::pluck('initial')->toArray();
        $array_target = [];
        $array_progress = [];
        $array_percentage = [];
        $label = [];

        for ($i = 0; $i < count($ams_names); $i++) {
            $target = (float)number_format((Sales::user($user)->year($year)->areaByAms('III', $ams_names[$i])->sum('value') / 1000000), 2);
            $progress = (float)number_format((Sales::user($user)->year($year)->areaByAms('III', $ams_names[$i])->level(1)->clean()->sum('value') / 1000000), 2);

            if ($target != 0 || $progress != 0) {
                $percentage = ($target == 0) ? 0 : (float)number_format((($progress / $target) * 100), 2);
                $ams = $ams_names[$i];

                $array_target[] = $target;
                $array_progress[] = $progress;
                $array_percentage[] = $ams." (". $percentage ."%)";
                $label[] = $ams_names[$i];
            }
        }

        $data = [
            'pie' => [
                'target' => $array_target,
                'label' => $label
            ],
            'bar' => [
                'target' => $array_target,
                'progress' => $array_progress,
                'percentage' => $array_percentage,
            ],
        ];
        
        return response()->json([
            'success' => true,
            'message' => 'Retrieve data succesfully',
            'data' => $data,
        ], 200);
    }

    public function amsAreaKAM(Request $request)
    {
        $user = auth()->user();
        $year = $request->year ?? false;

        $ams_names = AMS::pluck('initial')->toArray();
        $array_target = [];
        $array_progress = [];
        $array_percentage = [];
        $label = [];

        for ($i = 0; $i < count($ams_names); $i++) {
            $target = (float)number_format((Sales::user($user)->year($year)->areaKAM($ams_names[$i])->sum('value') / 1000000), 2);
            $progress = (float)number_format((Sales::user($user)->year($year)->areaKAM($ams_names[$i])->level(1)->clean()->sum('value') / 1000000), 2);

            if ($target != 0 || $progress != 0){
                $percentage = ($target == 0) ? 0 : (float)number_format((($progress / $target) * 100), 2);
                $ams = $ams_names[$i];
    
                $array_target[] = $target;
                $array_progress[] = $progress;
                $array_percentage[] = $ams." (". $percentage ."%)";
                $label[] = $ams_names[$i];
            }
        }

        $data = [
            'pie' => [
                'target' => $array_target,
                'label' => $label
            ],
            'bar' => [
                'target' => $array_target,
                'progress' => $array_progress,
                'percentage' => $array_percentage,
            ],
        ];
        
        return response()->json([
            'success' => true,
            'message' => 'Retrieve data succesfully',
            'data' => $data,
        ], 200);
    }

    public function rofoTotalMonth()
    {
        $user = auth()->user();

        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $array_target = [];
        $array_progress = [];
        $array_percentage = [];
        $array_gap = [];

        for ($i = 1; $i <= 12; $i++) {
            if ($i < $month) {
                $total_days = Carbon::create()->day(1)->month($i)->year($year)->endOfMonth()->format('d');

                $date_range = [
                    'start_date' => Carbon::create()->day(1)->month($i)->year($year)->format('Y-m-d'),
                    'end_date' => Carbon::create()->day($total_days)->month($i)->year($year)->format('Y-m-d'),
                ];
            } else if ($i == $month) {
                $date_range = [
                    'start_date' => Carbon::create()->day(1)->month($i)->year($year)->format('Y-m-d'),
                    'end_date' => Carbon::create()->day($day)->month($i)->year($year)->format('Y-m-d'),
                ];
            }

            if ($i > $month) {
                $target = 0;
                $progress = 0;
                $percentage = 0;
            } else {
                $target = (float)number_format((Sales::user($user)->rkap()->month($i)->sum('value') / 1000000), 2);
                $progress = (float)number_format((Sales::user($user)->rkap()->filter($date_range)->level(1)->clean()->sum('value') / 1000000), 2);
                $percentage = $target == 0 ? 0 : (float)number_format((($progress / $target) * 100), 2);
            }

            if ($target != 0 || $progress != 0) {
                $array_target[] = $target;
                $array_progress[] = $progress;
                $array_percentage[] = $this->array_months[$i-1]." (". $percentage ."%)";
                $array_gap[] = $target == 0 ? 0 : (float)number_format(($target - $progress), 2);
            }
        }

        $data = [
            'target' => $array_target,
            'progress' => $array_progress,
            'percentage' => $array_percentage,
            'gap' => $array_gap,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data succesfully',
            'data' => $data,
        ], 200);
    }

    public function rofoTotalYear(Request $request)
    {
        $user = auth()->user();
        $year = $request->year ?? false;

        $target = (float)number_format((Sales::user($user)->rkap()->year($year)->sum('value') / 1000000), 2);
        $progress = (float)number_format((Sales::user($user)->rkap()->year($year)->level(1)->clean()->sum('value') / 1000000), 2);
        $percentage = $target == 0 ? 0 : (float)number_format((($progress / $target) * 100), 2);
        $gap = $target == 0 ? 0 : (float)number_format(($target - $progress), 2);

        $data = [
            'target' => [$target],
            'progress' => [$progress],
            'percentage' => ["RoFo YTD (${percentage}%)"],
            'gap' => [$gap],
        ];

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data succesfully',
            'data' => $data,
        ], 200);
    }

    public function rofoGarudaMonth()
    {
        $user = auth()->user();

        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $array_target = [];
        $array_progress = [];
        $array_percentage = [];
        $array_gap = [];

        for ($i = 1; $i <= 12; $i++) {
            if ($i < $month) {
                $total_days = Carbon::create()->day(1)->month($i)->year($year)->endOfMonth()->format('d');

                $date_range = [
                    'start_date' => Carbon::create()->day(1)->month($i)->year($year)->format('Y-m-d'),
                    'end_date' => Carbon::create()->day($total_days)->month($i)->year($year)->format('Y-m-d'),
                ];
            } else if ($i == $month) {
                $date_range = [
                    'start_date' => Carbon::create()->day(1)->month($i)->year($year)->format('Y-m-d'),
                    'end_date' => Carbon::create()->day($day)->month($i)->year($year)->format('Y-m-d'),
                ];
            }

            if ($i > $month) {
                $target = 0;
                $progress = 0;
                $percentage = 0;
            } else {
                $target = (float)number_format((Sales::user($user)->rkap()->customerName('Garuda')->month($i)->sum('value') / 1000000), 2);
                $progress = (float)number_format((Sales::user($user)->rkap()->customerName('Garuda')->filter($date_range)->level(1)->clean()->sum('value') / 1000000), 2);
                $percentage = $target == 0 ? 0 : (float)number_format((($progress / $target) * 100), 2);
            }

            if ($target != 0 || $progress != 0) {
                $array_target[] = $target;
                $array_progress[] = $progress;
                $array_percentage[] = $this->array_months[$i-1]." (". $percentage ."%)";
                $array_gap[] = $target == 0 ? 0 : (float)number_format(($target - $progress), 2);
            }
        }

        $data = [
            'target' => $array_target,
            'progress' => $array_progress,
            'percentage' => $array_percentage,
            'gap' => $array_gap,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data succesfully',
            'data' => $data,
        ], 200);
    }

    public function rofoGarudaYear(Request $request)
    {
        $user = auth()->user();
        $year = $request->year ?? false;

        $target = (float)number_format((Sales::user($user)->rkap()->customerName('Garuda Indonesia')->year($year)->sum('value') / 1000000), 2);
        $progress = (float)number_format((Sales::user($user)->rkap()->customerName('Garuda Indonesia')->year($year)->level(1)->clean()->sum('value') / 1000000), 2);
        $percentage = $target == 0 ? 0 : (float)number_format((($progress / $target) * 100), 2);
        $gap = $target == 0 ? 0 : (float)number_format(($target - $progress), 2);

        $data = [
            'target' => [$target],
            'progress' => [$progress],
            'percentage' => ["RoFo YTD (${percentage}%)"],
            'gap' => [$gap],
        ];

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data succesfully',
            'data' => $data,
        ], 200);
    }

    public function rofoCitilinkMonth()
    {
        $user = auth()->user();

        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $array_target = [];
        $array_progress = [];
        $array_percentage = [];
        $array_gap = [];

        for ($i = 1; $i <= 12; $i++) {
            if ($i < $month) {
                $total_days = Carbon::create()->day(1)->month($i)->year($year)->endOfMonth()->format('d');

                $date_range = [
                    'start_date' => Carbon::create()->day(1)->month($i)->year($year)->format('Y-m-d'),
                    'end_date' => Carbon::create()->day($total_days)->month($i)->year($year)->format('Y-m-d'),
                ];
            } else if ($i == $month) {
                $date_range = [
                    'start_date' => Carbon::create()->day(1)->month($i)->year($year)->format('Y-m-d'),
                    'end_date' => Carbon::create()->day($day)->month($i)->year($year)->format('Y-m-d'),
                ];
            }

            if ($i > $month) {
                $target = 0;
                $progress = 0;
                $percentage = 0;
            } else {
                $target = (float)number_format((Sales::user($user)->rkap()->customerName('Citilink')->month($i)->sum('value') / 1000000), 2);
                $progress = (float)number_format((Sales::user($user)->rkap()->customerName('Citilink')->filter($date_range)->level(1)->clean()->sum('value') / 1000000), 2);
                $percentage = $target == 0 ? 0 : (float)number_format((($progress / $target) * 100), 2);
            }

            if ($target != 0 || $progress != 0) {
                $array_target[] = $target;
                $array_progress[] = $progress;
                $array_percentage[] = $this->array_months[$i-1]." (". $percentage ."%)";
                $array_gap[] = $target == 0 ? 0 : (float)number_format(($target - $progress), 2);
            }
        }

        $data = [
            'target' => $array_target,
            'progress' => $array_progress,
            'percentage' => $array_percentage,
            'gap' => $array_gap,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data succesfully',
            'data' => $data,
        ], 200);
    }

    public function rofoCitilinkYear(Request $request)
    {
        $user = auth()->user();
        $year = $request->year ?? false;

        $target = (float)number_format((Sales::user($user)->rkap()->customerName('Citilink Indonesia')->year($year)->sum('value') / 1000000), 2);
        $progress = (float)number_format((Sales::user($user)->rkap()->customerName('Citilink Indonesia')->year($year)->level(1)->clean()->sum('value') / 1000000), 2);
        $percentage = $target == 0 ? 0 : (float)number_format((($progress / $target) * 100), 2);
        $gap = $target == 0 ? 0 : (float)number_format(($target - $progress), 2);

        $data = [
            'target' => [$target],
            'progress' => [$progress],
            'percentage' => ["RoFo YTD (${percentage}%)"],
            'gap' => [$gap],
        ];

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data succesfully',
            'data' => $data,
        ], 200);
    }
}
