<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\AMS;
use App\Models\Apu;
use App\Models\TMB;
use App\Models\Hangar;
use App\Models\Line;
use App\Models\Area;
use App\Models\IGTE;
use App\Models\PBTH;
use App\Models\User;
use App\Models\Sales;
use App\Models\Engine;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Learning;
use App\Models\Prospect;
use App\Models\Component;
use App\Mail\Notification;
use App\Models\SalesLevel;
use App\Models\AMSCustomer;
use App\Models\Maintenance;
use App\Models\Requirement;
use App\Models\SalesReject;
use App\Models\SalesRequest;
use App\Models\SalesMaintenance;
use Illuminate\Support\Str;
use App\Imports\SalesImport;
use App\Models\AircraftType;
use Illuminate\Http\Request;
use App\Models\CancelCategory;
use App\Models\SalesReschedule;
use App\Models\SalesRequirement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\XpreamPlanningGates;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\PaginationHelper as PG;
use App\Http\Requests\PbthSalesRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\RecordsNotFoundException;

class SalesController extends Controller
{
    const REQUEST_APPROVED = 1;
    const REQUEST_REJECTED = 0;

    const REQUEST_STATUS = [
        self::REQUEST_APPROVED => 'Approved',
        self::REQUEST_REJECTED => 'Rejected',
    ];

    const MONTH_NAME = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    public function index(Request $request)
    {
        $user = auth()->user();

        $product = $request->product ?? false; 
        $customer = $request->customer ?? false; 
        $ams = $request->ams ?? false; 
        $date_range = $request->only(['start_date', 'end_date']);

        $target = Sales::user($user)->rkap()
                    ->productId($product)
                    ->customerId($customer)
                    ->amsInitial($ams)
                    ->filter($date_range)
                    ->thisYear($date_range)
                    ->sum('value');
        $open = Sales::user($user)->open()
                    ->productId($product)
                    ->customerId($customer)
                    ->amsInitial($ams)
                    ->filter($date_range)
                    ->thisYear($date_range)
                    ->sum('value');
        $closedSales = Sales::user($user)
                    ->closedSales()
                    ->productId($product)
                    ->customerId($customer)
                    ->amsInitial($ams)
                    ->filter($date_range)
                    ->thisYear($date_range)
                    ->sum('value');
        $closedIn = Sales::user($user)
                    ->closedIn()
                    ->productId($product)
                    ->customerId($customer)
                    ->amsInitial($ams)
                    ->filter($date_range)
                    ->thisYear($date_range)
                    ->sum('value');
        $cancel = Sales::user($user)
                    ->cancel()
                    ->productId($product)
                    ->customerId($customer)
                    ->amsInitial($ams)
                    ->filter($date_range)
                    ->thisYear($date_range)
                    ->sum('value');

        for ($i = 1; $i <= 4; $i++){
            ${"level$i"} = [
                'total' => Sales::user($user)
                        ->level($i)
                        ->productId($product)
                        ->customerId($customer)
                        ->filter($date_range)
                        ->thisYear($date_range)
                        ->sum('value'),
                'open' => Sales::user($user)
                        ->level($i)
                        ->open()
                        ->productId($product)
                        ->customerId($customer)
                        ->filter($date_range)
                        ->thisYear($date_range)
                        ->sum('value'),
                'closedSales' => Sales::user($user)
                        ->level($i)
                        ->closedSales()
                        ->productId($product)
                        ->customerId($customer)
                        ->filter($date_range)
                        ->thisYear($date_range)
                        ->sum('value'),
                'closedIn' => Sales::user($user)
                        ->level($i)
                        ->closedIn()
                        ->productId($product)
                        ->customerId($customer)
                        ->filter($date_range)
                        ->thisYear($date_range)
                        ->sum('value'),
                'cancel' => Sales::user($user)
                        ->level($i)
                        ->cancel()
                        ->productId($product)
                        ->customerId($customer)
                        ->filter($date_range)
                        ->thisYear($date_range)
                        ->sum('value'),
                'countOpen' => Sales::user($user)
                        ->level($i)
                        ->open()
                        ->productId($product)
                        ->customerId($customer)
                        ->filter($date_range)
                        ->thisYear($date_range)
                        ->count(),
                'countClosedSales' => Sales::user($user)
                        ->level($i)
                        ->closedSales()
                        ->productId($product)
                        ->customerId($customer)
                        ->filter($date_range)
                        ->thisYear($date_range)
                        ->count(),
                'countClosedIn' => Sales::user($user)
                        ->level($i)
                        ->closedIn()
                        ->productId($product)
                        ->customerId($customer)
                        ->filter($date_range)
                        ->thisYear($date_range)
                        ->count(),
                'countCancel' => Sales::user($user)
                        ->level($i)
                        ->cancel()
                        ->productId($product)
                        ->customerId($customer)
                        ->filter($date_range)
                        ->thisYear($date_range)
                        ->count(),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data successfully',
            'data' => [
                'totalTarget' => $target,
                'totalOpen' => $open,
                'totalClosedSales' => $closedSales,
                'totalClosedIn' => $closedIn,
                'totalCancel' => $cancel,
                'level4' => $level4,
                'level3' => $level3,
                'level2' => $level2,
                'level1' => $level1,
            ],
        ], 200);
    }

    public function templateExcel()
    {
        $fileName = 'Template_Import_Sales.xlsx';

        $headers = [
            'Content-Type'        => 'application/xlsx',            
            'Content-Disposition' => 'attachment; filename="'. $fileName .'"',
        ];

        return \Response::make(Storage::disk('public')->get('template/'.$fileName), 200, $headers);
    }

    public function uploadSales(Request $request)
    {
        activity()->disableLogging();

        $rules = ['fileUpload' => 'required|file|mimes:xlsx,xls|max:2048'];
        $messages = [
            'uploaded' => 'Check the file (size or type) then try again.',
            'file' => 'The uploaded content must be a file.',
            'mimes' => 'The uploaded file must be type of XLS or XLSX.',
            'max' => 'The uploaded file must not be greater than 5 MB.'
        ];

        $validator = Validator::make($request->only(['fileUpload']), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'message' => "Error occured while uploading file: {$validator->errors()->first()}"
            ], 400);
        }

        $import_sales = new SalesImport;
        Excel::import($import_sales, $request->file('fileUpload'));

        return response()->json([
            'message' => $import_sales->message,
        ], $import_sales->status);
    }

    public function table(Request $request)
    {
        // Use only the query parameters
        $filters = $request->only([
            'start_date', 'end_date', 'type', 'customer', 'product', 'ac_type',
            'component', 'engine', 'apu', 'ac_reg', 'other', 'level', 'progress',
            'status', 'year', 'ams', 'area'
        ]);

        // Handle multi-select filters
        $multiSelectFilters = ['type', 'customer', 'product', 'ac_type', 'component', 'engine', 'apu', 'ac_reg', 'level', 'progress', 'status', 'ams', 'area'];

        foreach ($multiSelectFilters as $filter) {
            if ($request->has($filter)) {
                // Convert the multi-select values to an array
                $filters[$filter] = is_array($request->$filter) ? $request->$filter : [$request->$filter];
            }
        }

        $search = $request->search;
        $order = $request->order ?? 'id';
        $by = $request->by ?? 'desc';
        $paginate = $request->paginate ?? 10;

        $salesplan = Sales::search($search)
                            ->filter($filters)
                            ->thisYear($request->only(['start_date', 'end_date']))
                            ->user(auth()->user())
                            ->sort($order, $by)
                            ->paginate($paginate)
                            ->withQueryString();

        $totalSales = Sales::search($search)
                            ->filter($filters)
                            ->clean()
                            ->thisYear($request->only(['start_date', 'end_date']))
                            ->user(auth()->user())->sum('value');

        $data = new Collection();
        foreach ($salesplan as $sales) {
            $data->push((object)[
                'id' => $sales->id,
                'customer' => $sales->customer->name,
                'product' => $sales->product->name ?? null,
                'month' => $sales->month_sales,
                'registration' => $sales->registration,
                'acReg' => $sales->ac_reg ?? '-',
                'ams' => $sales->ams_initial,
                'area' => $sales->area,
                'value' => $sales->value,
                'other' => $sales->other,
                'type' => $sales->type,
                'level' => $sales->level,
                'progress' => $sales->progress,
                'status' => $sales->status,
                'upgrade' => $sales->upgrade_level,
                'request' => $sales->active_request,
                'startDate' => Carbon::parse($sales->start_date)->format('Y-m-d'),
                'endDate' => Carbon::parse($sales->end_date)->format('Y-m-d'),
            ]);
        }

        $salesplan->setCollection($data);

        return response()->json([ 
            'success' => true,
            'message' => 'Retrieve data successfully',
            'data' => $salesplan,
            'totalValue' => $totalSales,
        ], 200);
    }

    private function requestArrayInput($request, $name, $value)
    {
        return $request->has($name) ? array($value) : null;
    }

    public function createTmb(Request $request)
    {
        activity()->disableLogging();

        $request->validate([
            'transaction_type_id' => 'sometimes|required|integer|between:1,2',
            'customer_id' => 'required|integer|exists:customers,id',
            'prospect_id' => 'sometimes|required|integer|exists:prospects,id',
            'maintenance_id' => 'nullable|array',
            'maintenance_id.*' => 'integer|exists:maintenances,id',
            'product_id' => 'sometimes|required|integer|exists:products,id',
            'ac_type_id' => 'sometimes|required|integer|exists:ac_type_id,id',
            'ac_reg' => 'sometimes|required|string',
            'value' => 'sometimes|required|numeric',
            'tat' => 'required|integer',
            'start_date' => 'required|date',
            'is_rkap' => 'required|boolean',
        ]);

        if (isset($request->prospect_id)) {
            $prospect = Prospect::find($request->prospect_id);

            $this->authorize('pickUpSales', $prospect);
        }

        try {
            DB::beginTransaction();

            $user = auth()->user();
            $sales = new Sales;

            $start_date = Carbon::parse($request->start_date);
            $end_date = Carbon::parse($request->start_date)->addDays($request->tat);

            if ($request->is_rkap) {
                $prospect = $prospect ?? Prospect::find($request->prospect_id);
                $sales->prospect_id = $prospect->id;
                $sales->customer_id = $prospect->amsCustomer->customer->id;
                $sales->transaction_type_id = $prospect->transaction_type_id;
                $sales->product_id = $prospect->tmb->product_id;
                $sales->ac_type_id = $prospect->tmb->ac_type_id ?? null;
                $sales->component_id = $prospect->tmb->component_id ?? null;
                $sales->engine_id = $prospect->tmb->engine_id ?? null;
                $sales->apu_id = $prospect->tmb->apu_id ?? null;
                $sales->ams_id = $prospect->amsCustomer->ams_id;

                $maintenance_id = $request?->maintenance_id ?? $prospect->tmb->maintenance_id;

                if ($prospect->transaction_type_id == 1) {
                    $optional_requirements = [1,2,3,4,5,6,7];
                    $sales_level = 2;
                } else {
                    $optional_requirements = ($prospect->tmb->product_id == 1) ? [1,5] : [1,4,5];
                    $sales_level = 4;
                }
            } else {
                $sales->customer_id = $request->customer_id;
                $sales->product_id = $request->product_id;
                $sales->ac_type_id = $request->ac_type_id;
                $sales->transaction_type_id = $request->transaction_type_id;
                $sales->ams_id = $user->ams->id ;

                $maintenance_id = $request->maintenance_id;

                if ($request->transaction_type_id == 1) {
                    $optional_requirements = [1,2,3,4,5,6,7];
                    $sales_level = 2;
                } else {
                    $optional_requirements = ($request->product_id == 1) ? [1,5] : [1,4,5];
                    $sales_level = 4;
                }
            }
            $sales->ac_reg = $request->ac_reg ?? null;
            $sales->value = $request->value;
            $sales->is_rkap = $request->is_rkap;
            $sales->tat = $request->tat;
            $sales->start_date = $start_date->format('Y-m-d');
            $sales->end_date = $end_date->format('Y-m-d');
            $sales->save();

            if (is_array($maintenance_id)) {
                for ($i = 0; $i < count($maintenance_id); $i++) { 
                    $sales_maintenance = new SalesMaintenance;
                    $sales_maintenance->sales_id = $sales->id;
                    $sales_maintenance->maintenance_id = $maintenance_id[$i];
                    $sales_maintenance->save();
                }
            } else {
                $sales_maintenance = new SalesMaintenance;
                $sales_maintenance->sales_id = $sales->id;
                $sales_maintenance->maintenance_id = $maintenance_id;
                $sales_maintenance->save();
            }

            $level = new SalesLevel;
            $level->level_id = $sales_level;
            $level->sales_id = $sales->id;
            $level->status = 1;
            $level->save();

            for ($i = 1; $i <= 10; $i++) { 
                $requirement = Requirement::findOrFail($i);

                $sales_requirement = new SalesRequirement;
                $sales_requirement->sales_id = $sales->id;
                $sales_requirement->requirement_id = $requirement->id;
                $sales_requirement->value = $requirement->value;
                $sales_requirement->status = in_array($i, $optional_requirements) ? 1 : 0;
                $sales_requirement->save();
            }

            DB::commit();

            // Mengaktifkan kembali pencatatan aktivitas
            activity()->enableLogging();

            $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';

            // Menambahkan aktivitas log setelah data berhasil diperbarui
            activity()
                ->causedBy(auth()->user())
                ->performedOn($sales)
                ->withProperties([
                    'user_name' => auth()->user()->name,
                ])
                ->log("{$causer} created TMB Sales");

            $salesLogs = Activity::where('subject_type', Sales::class)
                                ->where('subject_id', $sales->id)
                                ->latest()
                                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Salesplan created successfully',
                'data'    => $sales,
                'logs'    => $salesLogs
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error occured, failed to create Salesplan',
            ], 500);
        }
    }

    public function createPbth(Request $request)
    {
        // Menonaktifkan logging sementara
        activity()->disableLogging();

        $request->validate([
            'prospect_id' => 'required|integer|exists:prospects,id',
            'pbth' => 'required|array',
        ]);

        $prospect = Prospect::findOrFail($request->prospect_id);

        $this->authorize('pickUpSales', $prospect);

        try {
            DB::beginTransaction();

            $customer = $prospect->amsCustomer->customer;
            $ams = $prospect->amsCustomer->ams;

            foreach ($request->pbth as $pbth) {
                $full_month = self::MONTH_NAME[(int)$pbth['month'] - 1];
                $s_date = Carbon::parse("1 {$full_month} {$prospect->year}");
                $e_date = Carbon::parse("1 {$full_month} {$prospect->year}")->endOfMonth();
                $tat = $s_date->diffInDays($e_date);

                $sales = new Sales;
                $sales->customer_id = $customer->id;
                $sales->prospect_id = $prospect->id;
                $sales->product_id = $pbth['product_id'];
                $sales->ac_type_id = $pbth['ac_type_id'];
                $sales->ams_id = $ams->id;
                $sales->transaction_type_id = $prospect->transaction_type_id;
                $sales->value = $pbth['value'];
                $sales->is_rkap = true;
                $sales->tat = $tat;
                $sales->start_date = $s_date->format('Y-m-d');
                $sales->end_date = $e_date->format('Y-m-d');
                $sales->save();

                $level = new SalesLevel;
                $level->level_id = 1;
                $level->sales_id = $sales->id;
                $level->status = 1;
                $level->save();

                for ($i = 1; $i <= 10; $i++) { 
                    $requirement = Requirement::findOrFail($i);

                    $sales_requirement = new SalesRequirement;
                    $sales_requirement->sales_id = $sales->id;
                    $sales_requirement->requirement_id = $requirement->id;
                    $sales_requirement->value = $requirement->value;
                    $sales_requirement->status = in_array($i, [9,10]) ? 0 : 1;
                    $sales_requirement->save();
                }
            }

            DB::commit();

            // Mengaktifkan kembali pencatatan aktivitas
            activity()->enableLogging();

            $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';

            // Menambahkan aktivitas log setelah data berhasil diperbarui
            activity()
                ->causedBy(auth()->user())
                ->performedOn($sales)
                ->withProperties([
                    'user_name' => auth()->user()->name,
                ])
                ->log("{$causer} created PBTH Sales");

            $salesLogs = Activity::where('subject_type', Sales::class)
                ->where('subject_id', $sales->id)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Salesplan created successfully',
                'data'    => $sales,
                'logs'    => $salesLogs
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error occured, failed to create Salesplan',
            ], 500);
        }
    }

    public function show($id)
    {
        $sales = Sales::findOrFail($id);

        $this->authorize('show', $sales);

        if ($sales->salesReschedule) {
            $current_year = Carbon::parse($sales->start_date)->format('Y');
            $requested_year = Carbon::parse($sales->salesReschedule->start_date)->format('Y');
            $same_year = ($current_year == $requested_year);

            $sales_reschedule = [
                'id' => $sales->salesReschedule->id,
                'prospect_id' => $sales->prospect_id,
                'hangar' => $sales->salesReschedule?->hangar,
                'line' => $sales->salesReschedule?->line,
                'registration' => $sales->ac_reg ?? '-',
                'rawStartDate' => $sales->salesReschedule->start_date,
                'startDate' => Carbon::parse($sales->salesReschedule->start_date)->format('d-m-Y'),
                'endDate' => Carbon::parse($sales->salesReschedule->end_date)->format('d-m-Y'),
                'tat' => $sales->salesReschedule->tat,
                'currentDate' => $sales->salesReschedule->current_date,
                'salesMonth' => Carbon::parse($sales->start_date)->format('F'),
                'sameYear' => $same_year,
            ];
        }

        if ($sales->salesReject) {
            $sales_reject = [
                'id' => $sales->salesReject->id,
                'category' => $sales->salesReject->category->name,
                'reason' => $sales->salesReject->reason,
            ];
        }

        $total_sales = $sales->value;
        $sales_rkap = $sales->market_share;
        $deviation = $total_sales - $sales_rkap;

        $data = collect([
            'user' => auth()->user(),
            'salesDetail' => [
                'id' => $sales->id,
                'ams' => $sales->ams_info,
                'prospect_id' => $sales->prospect_id,
                'customer' => $sales->customer->only(['id', 'name']),
                'acReg' => $sales->ac_reg ?? '-',
                'registration' => $sales->registration,
                'level' => $sales->level,
                'status' => $sales->status,
                'other' => $sales->other,
                'type' => $sales->type,
                'progress' => $sales->progress,
                'monthSales' => $sales->month_sales,
                'tat' => $sales->tat,
                'year' => $sales->year,
                'startDate' => Carbon::parse($sales->start_date)->format('Y-m-d'),
                'endDate' => Carbon::parse($sales->end_date)->format('Y-m-d'),
                'product' => $sales->product,
                'location' => $sales->hangar_name,
                'maintenance' => $sales->maintenance_name,
                'so_number' => $sales->so_number,
                'upgrade' => $sales->upgrade_level,
                'totalSales' => $total_sales,
                'salesRkap' => $sales_rkap,
                'deviasi' => $deviation,
            ],
            'requestUpgrade' => $sales->requestUpgrade ?? null,
            'requestHangar' => $sales->requestHangar ?? null,
            'requestReschedule' => $sales->requestReschedule ?? null,
            'requestCancel' => $sales->requestCancel ?? null,
            'requestClosed' => $sales->requestClosed ?? null,
            'salesReschedule' => $sales_reschedule ?? null,
            'salesReject' => $sales_reject ?? null,
            'level4' => $sales->level4,
            'level3' => $sales->level3,
            'level2' => $sales->level2,
            'level1' => $sales->level1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data successfully',
            'data' => $data,
        ], 200);
    }

    public function showTmbSales($id, Request $request)
    {
        $search = $request->search ?? false;
        $order = $request->order ?? 'id';
        $by = $request->by ?? 'desc';
        $paginate = $request->paginate ?? 10;

        $prospect = Prospect::findOrFail($id);
        $sales_plan = $prospect->sales_plan;
        $market_share = $prospect->market_share;
        $deviation = $market_share - $sales_plan;

        $data = Sales::search($search)
                    ->where('prospect_id', $prospect->id)
                    ->get();
        
        $sales_by_prospect = new Collection();

        foreach ($data as $sales) {
            $sales_by_prospect->push((object)[
                'id' => $sales->id,
                'registration' => $sales->ac_reg,
                'maintenance' => $sales->maintenance_name,
                'ams' => $sales->ams_initial,
                'location' => $sales->hangar_name,
                'sales_plan' => $sales->value,
                'tat' => $sales->tat,
                'start_date' => Carbon::parse($sales->start_date)->format('Y-m-d'),
                'end_date' => Carbon::parse($sales->end_date)->format('Y-m-d'),
                'level' => $sales->level,
                'status' => $sales->status,
                'customer' => $sales->customer,
            ]);
        }

        $sales_by_prospect = $sales_by_prospect->sortBy([[$order, $by]])->values();
        $salesplan = PG::paginate($sales_by_prospect, $paginate);

        $salesplan->appends([
            'search' => $search,
            'order' => $order,
            'by' => $by,
        ]);

        return response()->json([
            'salesplan' => $sales_plan,
            'market_share' => $market_share,
            'deviation' => $deviation,
            'customer' => $prospect->amsCustomer->customer,
            'sales' => $salesplan,
        ], 200);
    }

    public function deleteTmbSales($id)
    {
        // Menonaktifkan logging sementara
        activity()->disableLogging();

        $sales = Sales::find($id);

        if ($sales) {
            $this->authorize('deleteTmb', $sales);

            try {
                DB::beginTransaction();

                $requirements = $sales->salesRequirements;

                $sales->salesLevel->delete();

                $temp_files = [];
                foreach ($requirements as $requirement) {
                    $files = $requirement->files;
                    foreach ($files as $file) {
                        $temp_files[] = (object)$file;
                        $file->delete();
                    }
                    $requirement->delete();
                }
                $sales->delete();
                
                DB::commit();

                foreach ($temp_files as $file) {
                    if (Storage::disk('public')->exists($file->path)) {
                        Storage::disk('public')->delete($file->path);
                    }
                }
                
                // Mengaktifkan kembali pencatatan aktivitas
                activity()->enableLogging();

                $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';

                // Menambahkan aktivitas log setelah data berhasil diperbarui
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($sales)
                    ->withProperties([
                        'user_name' => auth()->user()->name,
                    ])
                    ->log("{$causer} deleted TMB Sales");

                $salesLogs = Activity::where('subject_type', Sales::class)
                    ->where('subject_id', $sales->id)
                    ->latest()
                    ->get();

                return response()->json([
                    'message' => 'TMB Sales has been deleted successfully!',
                    'data'    => $sales,
                    'logs'    => $salesLogs
                ], 200);
            } catch (QueryException $e) {
                DB::rollback();

                Log::error($e->getMessage());

                return response()->json([
                    'message' => 'Error occured, failed to delete Salesplan data.',
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Salesplan data not found.',
            ], 404);
        }
    }

    public function switchAMS($id, Request $request)
    {
        try {
            activity()->disableLogging();

            $request->validate(['ams_id' => 'required|integer|exists:ams,id']);

            $sales = Sales::findOrFail($id);

            // Mencatat perbedaan nilai sebelum perubahan
            $oldValues = $sales->getAttributes();
            $oldValues['ams_initial'] = AMS::find($sales->getOriginal('ams_id'))->initial;
            $oldValues['product_name'] = Product::find($sales->product_id)->name;
            $oldValues['maintenance_name'] = $sales->maintenance_name;

            $sales->ams_id = $request->ams_id;
            $sales->push();

            // Mendapatkan nilai aktual setelah menyimpan perubahan
            $sales->refresh();

            // Mencatat perbedaan nilai setelah perubahan
            $newValues = $sales->getChanges();
            $newValues['ams_initial'] = AMS::find($request->ams_id)->initial;
            $newValues['product_name'] = null;
            $newValues['maintenance_name'] = null;

            // Mengaktifkan kembali pencatatan aktivitas
            activity()->enableLogging();

            $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';

            // Menambahkan aktivitas log setelah data berhasil diperbarui
            activity()  
                ->causedBy(auth()->user())
                ->performedOn($sales)
                ->withProperties([
                    'user_name'  => auth()->user()->name,
                    'old_values' => $oldValues,
                    'new_values' => $newValues,
                ])
                ->log("{$causer} switched AMS Salesplan");

            $salesLogs = Activity::where('subject_type', Sales::class)
                ->where('subject_id', $sales->id)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'AMS Salesplan changed successfully.',
                'data'    => $sales,
                'logs'    => $salesLogs
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error occured, failed to change AMS Salesplan.'
            ], 500);
        }
    }

    public function updatePbth(Request $request)
    {
        try {
            // Menonaktifkan logging sementara
            activity()->disableLogging();
    
            $request->validate([
                'rate'        => 'required|integer',
                'flight_hour' => 'required|integer',
            ]);
    
            $pbth = PBTH::where('prospect_id', $id);
            $sales = $pbth->prospect->sales;
    
            // Mencatat perbedaan nilai sebelum perubahan
            $oldValues = $sales->getAttributes();
            
            $pbth->rate = $request->rate;
            $pbth->flight_hour = $request->flight_hour;
            $pbth->update($request->all());
    
            // Hanya menambahkan nilai 'value' jika memang terjadi perubahan
            if ($sales->isDirty('rate')) {
                $newValues['rate'] = $sales->getOriginal('rate');
            }
    
            $sales->save();
    
            // Mendapatkan nilai aktual setelah menyimpan perubahan
            $sales->refresh();
    
            // Mencatat perbedaan nilai setelah perubahan
            $newValues = $sales->getChanges();
    
            // Mengaktifkan kembali pencatatan aktivitas
            activity()->enableLogging();
    
            $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';
    
            // Menambahkan aktivitas log setelah data berhasil diperbarui
            activity()
                ->causedBy(auth()->user())
                ->performedOn($sales)
                ->withProperties([
                    'user_name'   => auth()->user()->name,
                    'old_values'  => $oldValues,
                    'new_values'  => $newValues,
                    'rate'        => $request->rate,
                    'flight_hour' => $request->flight_hour,
                ])
                ->log("{$causer} updated PBTH Sales data");
    
            $salesLogs = Activity::where('subject_type', Sales::class)
                ->where('subject_id', $sales->id)
                ->latest()
                ->get();
    
            return response()->json([
                'success' => true,
                'message' => 'PBTH data updated successfully',
                'data'    => $pbth,
                'logs'    => $salesLogs,
            ], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error occured, failed to update PBTH data.'
            ], 500);
        }
    }

    public function updateTmb($id, Request $request)
    {
        // Menonaktifkan logging sementara
        activity()->disableLogging();

        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'maintenance_id' => 'required|array',
            'maintenance_id.*' => 'integer|exists:maintenances,id',
            'ac_reg' => 'nullable|string',
            'value' => 'required|numeric'
        ]);

        $sales = Sales::findOrFail($id);

        try {
            DB::beginTransaction();

            // Mencatat perbedaan nilai sebelum perubahan
            $oldValues = $sales->getAttributes();
            $oldValues['ams_initial'] = AMS::find($sales->ams_id)->initial;
            $oldValues['product_name'] = Product::find($sales->getOriginal('product_id'))->name;
            $oldValues['maintenance_name'] = $sales->maintenance_name;

            // Update requirement status for Aircraft product
            $hangar_requirement = $sales->salesRequirements->firstWhere('requirement_id', 4);
            // return response()->json($hangar_requirement);

            if ($request->product_id != 1) {
                $hangar_requirement->status = true;
            } else {
                $hangar_request = $sales->requestHangar;

                if ($hangar_requirement->status == true) {
                    if (($hangar_request && $hangar_request?->status != 3) || 
                        (!$sales->hangar_id || !$sales->line_id)) {
                        $hangar_requirement->status = false;
                    }
                }
            }

            $hangar_requirement->save();

            $sales->product_id = $request->product_id;
            $sales->ac_reg = $request->ac_reg ?? null;
            $sales->value = $request->value;
            $sales->save();

            SalesMaintenance::where('sales_id', $sales->id)->delete();

            foreach ($request->maintenance_id as $maintenance_id) {
                $sales_maintenance = new SalesMaintenance;
                $sales_maintenance->sales_id = $sales->id;
                $sales_maintenance->maintenance_id = $maintenance_id;
                $sales_maintenance->save();
            }

            // Mendapatkan nilai aktual setelah menyimpan perubahan
            $sales->refresh();

            // Mencatat perbedaan nilai setelah perubahan
            $newValues = $sales->getChanges();
            $newValues['ams_initial'] = null;
            $newValues['product_name'] = Product::find($request->product_id)->name;
            $newValues['maintenance_name'] = $sales->maintenance_name;

            // Hanya menambahkan nilai 'value' jika memang terjadi perubahan
            if ($sales->isDirty('value')) {
                $newValues['value'] = $sales->getOriginal('value');
            }

            // Mengaktifkan kembali pencatatan aktivitas
            activity()->enableLogging();

            $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';

            // Menambahkan aktivitas log setelah data berhasil diperbarui
            activity()
                ->causedBy(auth()->user())
                ->performedOn($sales)
                ->withProperties([
                    'user_name'      => auth()->user()->name,
                    'old_values'     => $oldValues,
                    'new_values'     => $newValues,
                ])
                ->log("{$causer} updated TMB Sales data");

            $salesLogs = Activity::where('subject_type', Sales::class)
                ->where('subject_id', $sales->id)
                ->latest()
                ->get();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sales Plan data updated successfully',
                'data'    => $sales,
                'logs'    => $salesLogs,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error occured, failed to update Sales Plan data.'
            ], 500);
        }
    }

    public function SalesDetailUpdateLog($id)
    {
        $sales = Sales::find($id);

        if (!$sales) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        $logs = Activity::where('subject_type', Sales::class)
            ->where('subject_id', $sales->id)
            ->latest()
            ->get();

        return response()->json([
            // 'sales' => $sales,
            'logs'  => $logs,
        ]);
    }

    public function requestClosedSales(Request $request)
    {
        // Menonaktifkan logging sementara
        activity()->disableLogging();

        $request->validate([
            'sales_id' => 'required|integer|exists:sales,id',
            'user_id' => 'required|integer|exists:users,id',
            'target_url' => 'required|string',
        ]);

        $sales = Sales::findOrFail($request->sales_id);
        $user = User::findOrFail($request->user_id);

        try {
            $tpr_mail = $user->email;
            $tpr_name = $user->name;
            $link = env('FRONTEND_URL').$request->target_url;
            $data = [
                'type' => 1,
                'subject' => "New Request to Closed Sales Plan",
                'body' => [
                    'message' => "You have new request to closed Sales Plan",
                    'user_name' => $tpr_name,
                    'link' => $link,
                    'ams_name' => "{$sales->ams->user->name} ({$sales->ams->initial})",
                    'customer' => "{$sales->customer->name} ({$sales->customer->code})",
                    'ac_reg' => $sales->ac_reg ?? '-',
                    'type' => $sales->type,
                    'level' => "{$sales->level}",
                    'progress' => $sales->progress ?? '-',
                    'tat' => "{$sales->tat} days",
                    'start_date' => Carbon::parse($sales->start_date)->format('d F Y'),
                    'end_date' => Carbon::parse($sales->end_date)->format('d F Y'),
                ]
            ];

            $mail_sent = Mail::to($tpr_mail)->send(new Notification($data));

            if (!$mail_sent) {
                throw new \Exception("Email not sent to {$tpr_mail}", 1);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        try {
            DB::beginTransaction();

            $sales_request = SalesRequest::where('sales_id', $sales->id)
                                        ->where('category', SalesRequest::REQUEST_CLOSED_SALES)
                                        ->first();

            if ($sales_request) {
                $sales_request->reviewer_id = $user->id;
                $sales_request->status = SalesRequest::STATUS_REQUESTED;
                $sales_request->reject_reason = null;
                $sales_request->commit = false;
                $sales_request->save();
            } else {
                $sales_request = new SalesRequest;
                $sales_request->sales_id = $sales->id;
                $sales_request->reviewer_id = $user->id;
                $sales_request->category = SalesRequest::REQUEST_CLOSED_SALES;
                $sales_request->status = SalesRequest::STATUS_REQUESTED;
                $sales_request->commit = false;
                $sales_request->save();
            }

            DB::commit();

            // Mengaktifkan kembali pencatatan aktivitas
            activity()->enableLogging();

            $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';

            // Menambahkan aktivitas log setelah data berhasil diperbarui
            activity()
                ->causedBy(auth()->user())
                ->performedOn($sales)
                ->withProperties([
                    'user_name' => auth()->user()->name,
                ])
                ->log("{$causer} requested to {$tpr_name} to closed Sales Plan");

            $salesLogs = Activity::where('subject_type', Sales::class)
                ->where('subject_id', $sales->id)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => "Closed Sales Plan requested successfully",
                'log' => Activity::where('subject_type', Sales::class)
                    ->where('subject_id', $sales->id)
                    ->latest()
                    ->get(),
                'logs' => $salesLogs,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "'Something\'s wrong with the proccess, failed to request Cancel Sales Plan'",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function approveClosedSales($id, Request $request)
    {
        // Menonaktifkan logging sementara
        activity()->disableLogging();

        $request->validate([
            'is_approved' => 'required|boolean',
            'reject_reason' => 'sometimes|string',
            'target_url' => 'required|string',
        ]);

        $sales = Sales::findOrFail($id);
        $status = self::REQUEST_STATUS[$request->is_approved];

        try {
            $user_mail = $sales->ams->user->email;
            $link = env('FRONTEND_URL').$request->target_url;
            $data = [
                'type' => 1,
                'subject' => "Your Sales Plan Upgrade Request {$status}",
                'body' => [
                    'message' => "Your Sales Plan upgrade request was {$status}",
                    'user_name' => $sales->ams->user->name,
                    'link' => $link,
                    'ams_name' => "{$sales->ams->user->name} ({$sales->ams->initial})",
                    'customer' => "{$sales->customer->name} ({$sales->customer->code})",
                    'ac_reg' => $sales->ac_reg ?? '-',
                    'type' => $sales->type,
                    'level' => $sales->level,
                    'progress' => $sales->progress ?? '-',
                    'tat' => "{$sales->tat} days",
                    'start_date' => Carbon::parse($sales->start_date)->format('d F Y'),
                    'end_date' => Carbon::parse($sales->end_date)->format('d F Y'),
                ]
            ];

            $mail_sent = Mail::to($user_mail)->send(new Notification($data));

            if (!$mail_sent) {
                throw new \Exception("Email not sent to {$user_mail}", 1);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        try {
            DB::beginTransaction();

            $sales_request = $sales->requestClosed;
            $sales_request->status = $request->is_approved ? SalesRequest::STATUS_APPROVED : SalesRequest::STATUS_REJECTED;
            $sales_request->reject_reason = $request->is_approved ? null : $request->reject_reason;
            $sales_request->commit = $request->is_approved;
            $sales_request->save();

            if ($request->is_approved) {
                $sales_level = $sales->salesLevel;
                $sales_level->status = Sales::STATUS_CLOSED_SALES;
                $sales_level->save();
            }

            DB::commit();

            // Mengaktifkan kembali pencatatan aktivitas
            activity()->enableLogging();

            $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';

            // Menambahkan aktivitas log setelah data berhasil diperbarui
            activity()
                ->causedBy(auth()->user())
                ->performedOn($sales)
                ->withProperties([
                    'user_name' => auth()->user()->name,
                ])
                ->log("{$causer} {$status} Sales Plan closed request");

            $salesLogs = Activity::where('subject_type', Sales::class)
                ->where('subject_id', $sales->id)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => "Sales Plan closed request {$status} successfully",
                'data'    => $sales,
                'logs'    => $salesLogs,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Something's wrong, failed to {$status} Sales Plan closed request",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function responseClosedSales($id)
    {
        $sales = Sales::findOrFail($id);

        $upgrade_request = $sales->requestClosed;

        if ($upgrade_request->status == SalesRequest::STATUS_REJECTED) {
            $upgrade_request->status = SalesRequest::STATUS_NO_REQUEST;
            $upgrade_request->reject_reason = null;
        }

        $upgrade_request->save();

        return response()->json([
            'success' => true,
            'message' => 'Closed Sales request status confirmed!'
        ], 200);
    }

    public function requestUpgrade(Request $request)
    {
        // Menonaktifkan logging sementara
        activity()->disableLogging();

        $request->validate([
            'sales_id' => 'required|integer|exists:sales,id',
            'user_id' => 'required|integer|exists:users,id',
            'target_url' => 'required|string',
        ]);

        $sales = Sales::findOrFail($request->sales_id);
        $user = User::findOrFail($request->user_id);

        if (!$sales->upgrade_level) {
            return response()->json([
                'success' => false,
                'message' => 'Oops, complete the requirement first',
            ], 422);
        }

        try {
            $next_level = $sales->level - 1;
            $tpr_mail = $user->email;
            $tpr_name = $user->name;
            $link = env('FRONTEND_URL').$request->target_url;
            $data = [
                'type' => 1,
                'subject' => "New Request to Upgrade Sales Plan ",
                'body' => [
                    'message' => "You have new request to upgrade Sales Plan level",
                    'user_name' => $tpr_name,
                    'link' => $link,
                    'ams_name' => "{$sales->ams->user->name} ({$sales->ams->initial})",
                    'customer' => "{$sales->customer->name} ({$sales->customer->code})",
                    'ac_reg' => $sales->ac_reg ?? '-',
                    'type' => $sales->type,
                    'level' => "{$sales->level} to {$next_level}",
                    'progress' => $sales->progress ?? '-',
                    'tat' => "{$sales->tat} days",
                    'start_date' => Carbon::parse($sales->start_date)->format('d F Y'),
                    'end_date' => Carbon::parse($sales->end_date)->format('d F Y'),
                ]
            ];

            $mail_sent = Mail::to($tpr_mail)->send(new Notification($data));

            if (!$mail_sent) {
                throw new \Exception("Email not sent to {$tpr_mail}", 1);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        try {
            DB::beginTransaction();

            $sales_request = SalesRequest::where('sales_id', $sales->id)
                                        ->where('category', SalesRequest::REQUEST_UPGRADE_LEVEL)
                                        ->first();

            if ($sales_request) {
                $sales_request->reviewer_id = $user->id;
                $sales_request->status = SalesRequest::STATUS_REQUESTED;
                $sales_request->reject_reason = null;
                $sales_request->commit = false;
                $sales_request->save();
            } else {
                $sales_request = new SalesRequest;
                $sales_request->sales_id = $sales->id;
                $sales_request->reviewer_id = $user->id;
                $sales_request->category = SalesRequest::REQUEST_UPGRADE_LEVEL;
                $sales_request->status = SalesRequest::STATUS_REQUESTED;
                $sales_request->commit = false;
                $sales_request->save();
            }

            DB::commit();

            // Mengaktifkan kembali pencatatan aktivitas
            activity()->enableLogging();

            $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';

            // Menambahkan aktivitas log setelah data berhasil diperbarui
            activity()
                ->causedBy(auth()->user())
                ->performedOn($sales)
                ->withProperties([
                    'user_name' => auth()->user()->name,
                ])
                ->log("{$causer} requested to {$tpr_name} to upgrade Sales Plan level");

            $salesLogs = Activity::where('subject_type', Sales::class)
                ->where('subject_id', $sales->id)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => "Upgrade Sales Plan level requested successfully",
                'log' => Activity::where('subject_type', Sales::class)
                    ->where('subject_id', $sales->id)
                    ->latest()
                    ->get(),
                'logs' => $salesLogs,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "'Something\'s wrong with the proccess, failed to request upgrade Sales Plan level'",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function approveUpgrade($id, Request $request)
    {
        // Menonaktifkan logging sementara
        activity()->disableLogging();

        $request->validate([
            'is_approved' => 'required|boolean',
            'reject_reason' => 'sometimes|string',
            'target_url' => 'required|string',
        ]);

        $sales = Sales::findOrFail($id);
        $status = self::REQUEST_STATUS[$request->is_approved];

        if (!$sales->upgrade_level) {
            return response()->json([
                'success' => false,
                'message' => "Oops, Sales Plan level cannot be upgraded yet",
            ], 422);
        }

        try {
            $next_level = $sales->level - 1;
            $user_mail = $sales->ams->user->email;
            $link = env('FRONTEND_URL').$request->target_url;
            $data = [
                'type' => 1,
                'subject' => "Your Sales Plan Upgrade Request {$status}",
                'body' => [
                    'message' => "Your Sales Plan upgrade request was {$status}",
                    'user_name' => $sales->ams->user->name,
                    'link' => $link,
                    'ams_name' => "{$sales->ams->user->name} ({$sales->ams->initial})",
                    'customer' => "{$sales->customer->name} ({$sales->customer->code})",
                    'ac_reg' => $sales->ac_reg ?? '-',
                    'type' => $sales->type,
                    'level' => "{$sales->level} to {$next_level}",
                    'progress' => $sales->progress ?? '-',
                    'tat' => "{$sales->tat} days",
                    'start_date' => Carbon::parse($sales->start_date)->format('d F Y'),
                    'end_date' => Carbon::parse($sales->end_date)->format('d F Y'),
                ]
            ];

            $mail_sent = Mail::to($user_mail)->send(new Notification($data));

            if (!$mail_sent) {
                throw new \Exception("Email not sent to {$user_mail}", 1);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        try {
            DB::beginTransaction();

            $sales_request = $sales->requestUpgrade;
            $sales_request->status = $request->is_approved ? SalesRequest::STATUS_NO_REQUEST : SalesRequest::STATUS_REJECTED;
            $sales_request->reject_reason = $request->is_approved ? null : $request->reject_reason;
            $sales_request->save();

            if ($request->is_approved) {
                $sales_level = $sales->salesLevel;
                $sales_level->level_id = $next_level;
                $sales_level->save();
            }

            DB::commit();

            // Mengaktifkan kembali pencatatan aktivitas
            activity()->enableLogging();

            $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';

            // Menambahkan aktivitas log setelah data berhasil diperbarui
            activity()
                ->causedBy(auth()->user())
                ->performedOn($sales)
                ->withProperties([
                    'user_name' => auth()->user()->name,
                ])
                ->log("{$causer} {$status} Sales Plan upgrade request");

            $salesLogs = Activity::where('subject_type', Sales::class)
                ->where('subject_id', $sales->id)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => "Sales Plan upgrade request {$status} successfully",
                'data'    => $sales,
                'logs'    => $salesLogs,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Something's wrong, failed to {$status} Sales Plan upgrade request",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function responseUpgrade($id)
    {
        $sales = Sales::findOrFail($id);

        $upgrade_request = $sales->requestUpgrade;

        if ($upgrade_request->status == SalesRequest::STATUS_REJECTED) {
            $upgrade_request->status = SalesRequest::STATUS_NO_REQUEST;
            $upgrade_request->reject_reason = null;
        }

        $upgrade_request->save();

        return response()->json([
            'success' => true,
            'message' => 'Upgrade Level request status confirmed!'
        ], 200);
    }

    public function requestHangar(Request $request)
    {
        // Menonaktifkan logging sementara
        activity()->disableLogging();

        $request->validate([
            'sales_id' => 'required|integer|exists:sales,id',
            'hangar_id' => 'required|integer|exists:hangars,id',
            'line_id' => 'required|integer|exists:lines,id',
            'user_id' => 'required|integer|exists:users,id',
            'target_url' => 'required|string',
        ]);

        $sales = Sales::findOrFail($request->sales_id);
        $user = User::findOrFail($request->user_id);
        $hangar_name = Hangar::findOrFail($request->hangar_id)->name;
        $line_name = Line::findOrFail($request->line_id)->name;

        if ($sales->level != 4) {
            return response()->json([
                'success' => false,
                'message' => 'Oops, this action only available at level 4',
            ], 422);
        }

        try {
            $cbo_mail = $user->email;
            $cbo_name = $user->name;
            $link = env('FRONTEND_URL').$request->target_url;
            $data = [
                'type' => 2,
                'subject' => 'New Hangar Slot Request',
                'body' => [
                    'message' => 'You have new request for Hangar Slot.',
                    'user_name' => $cbo_name,
                    'ams_name' => $sales->ams->user->name ?? '-',
                    'hangar' => $hangar_name,
                    'line' => $line_name,
                    'ac_reg' => $sales->ac_reg ?? '-',
                    'tat' => $sales->tat,
                    'start_date' => Carbon::parse($sales->start_date)->format('d F Y'),
                    'end_date' => Carbon::parse($sales->end_date)->format('d F Y'),
                    'link' => $link,
                ]
            ];

            $mail_sent = Mail::to($cbo_mail)->send(new Notification($data));

            if (!$mail_sent) {
                throw new \Exception("Email not sent to {$cbo_mail}", 1);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        try {
            DB::beginTransaction();
        
            $sales->hangar_id = $request->hangar_id;
            $sales->line_id = $request->line_id;
            $sales->push();

            $sales_request = SalesRequest::where('sales_id', $sales->id)
                                        ->where('category', SalesRequest::REQUEST_HANGAR_SLOT)
                                        ->first();

            if ($sales_request) {
                $sales_request->reviewer_id = $user->id;
                $sales_request->status = SalesRequest::STATUS_REQUESTED;
                $sales_request->commit = false;
                $sales_request->save();
            } else {
                $sales_request = new SalesRequest;
                $sales_request->sales_id = $sales->id;
                $sales_request->reviewer_id = $user->id;
                $sales_request->category = SalesRequest::REQUEST_HANGAR_SLOT;
                $sales_request->status = SalesRequest::STATUS_REQUESTED;
                $sales_request->commit = false;
                $sales_request->save();
            }

            DB::commit();

            // Mengaktifkan kembali pencatatan aktivitas
            activity()->enableLogging();

            $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';

            // Menambahkan aktivitas log setelah data berhasil diperbarui
            activity()
                ->causedBy(auth()->user())
                ->performedOn($sales)
                ->withProperties([
                    'user_name' => auth()->user()->name,
                ])
                ->log("{$causer} requested Hangar slot");

            $salesLogs = Activity::where('subject_type', Sales::class)
                ->where('subject_id', $sales->id)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Hangar Slot requested successfully',
                'logs'    => $salesLogs,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something\'s wrong with the process, failed to request Hangar Slot',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function approveHangar($id, Request $request)
    {
        // Menonaktifkan logging sementara
        activity()->disableLogging();

        $request->validate([
            'is_approved' => 'required|boolean',
            'reject_reason' => 'sometimes|string',
            'target_url' => 'required|string',
        ]);

        $sales = Sales::findOrFail($id);
        $status = self::REQUEST_STATUS[$request->is_approved];

        if (!$sales->line && !$sales->hangar) {
            return response()->json([
                'success' => false,
                'message' => 'Oops, this sales does not have a Line Hangar yet',
            ], 422);
        }

        try {
            $cbo = auth()->user();
            $ams = $sales->ams->user;
            $link = env('FRONTEND_URL').$request->target_url;
            $data = [
                'type' => 20,
                'subject' => 'Your Hangar Slot Request '.$status,
                'body' => [
                    'message' => 'Your Hangar slot request was '.$status,
                    'user_name' => $ams->name,
                    'cbo_name' => $cbo->name,
                    'hangar' => $sales->hangar_name,
                    'line' => $sales->line_name,
                    'ac_reg' => $sales->ac_reg ?? '-',
                    'tat' => $sales->tat,
                    'start_date' => Carbon::parse($sales->start_date)->format('d F Y'),
                    'end_date' => Carbon::parse($sales->end_date)->format('d F Y'),
                    'link' => $link,
                ]
            ];

            $mail_sent = Mail::to($ams->email)->send(new Notification($data));

            if (!$mail_sent) {
                throw new \Exception("Email not sent to {$ams->email}", 1);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        try {
            DB::beginTransaction();

            if ($request->is_approved) {
                $sales->setRequirement(4);
            } else {
                $sales->hangar_id = null;
                $sales->line_id = null;
                $sales->save();
            }

            $sales_request = $sales->requestHangar;
            $sales_request->status = $request->is_approved ? SalesRequest::STATUS_APPROVED : SalesRequest::STATUS_REJECTED;
            $sales_request->reject_reason = $request->reject_reason ?? null;
            $sales_request->save();

            DB::commit();

            // Mengaktifkan kembali pencatatan aktivitas
            activity()->enableLogging();

            $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';

            // Menambahkan aktivitas log setelah data berhasil diperbarui
            activity()
                ->causedBy(auth()->user())
                ->performedOn($sales)
                ->withProperties([
                    'user_name' => auth()->user()->name,
                ])
                ->log("{$causer} {$status} Line Hangar slot");

            $salesLogs = Activity::where('subject_type', Sales::class)
                ->where('subject_id', $sales->id)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => "Hangar Slot {$status} successfully",
                'data'    => $sales,
                'logs'    => $salesLogs,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Something's wrong, failed to {$status} Hangar Slot request",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function responseHangar($id)
    {
        $sales = Sales::findOrFail($id);

        $hangar_request = $sales->requestHangar;

        if ($hangar_request->status == SalesRequest::STATUS_APPROVED) {
            $hangar_request->commit = true;
            $hangar_request->reject_reason = null;
        } else if ($hangar_request->status == SalesRequest::STATUS_REJECTED) {
            $hangar_request->status = SalesRequest::STATUS_NO_REQUEST;
            $hangar_request->reject_reason = null;
        }

        $hangar_request->save();

        return response()->json([
            'success' => true,
            'message' => 'Hangar Slot request status confirmed!'
        ], 200);
    }

    public function requestReschedule(Request $request)
    {
        // Menonaktifkan logging sementara
        activity()->disableLogging();

        $request->validate([
            'sales_id' => 'required|integer|exists:sales,id',
            'user_id' => 'required|integer|exists:users,id',
            'start_date' => 'required|date',
            'tat' => 'required|integer',
            'hangar_id' => 'nullable|required_with:line_id|integer|exists:hangars,id',
            'line_id' => 'nullable|required_with:hangar_id|integer|exists:lines,id',
            'target_url' => 'required|string',
        ]);

        $sales = Sales::findOrFail($request->sales_id);
        $user = User::findOrFail($request->user_id);
        $reschedule_hangar = Hangar::find($request->hangar_id);
        $reschedule_line = Line::find($request->line_id);
        $start_date = Carbon::parse($request->start_date);
        $tat = $request->tat;
        $end_date = Carbon::parse($request->start_date)->addDays($tat);

        try {
            $cbo_mail = $user->email;
            $cbo_name = $user->name;
            $link = env('FRONTEND_URL').$request->target_url;
            $data = [
                'type' => 3,
                'subject' => 'New Reschedule Sales Plan Request',
                'body' => [
                    'message' => 'You have new request for Reschedule Sales Plan.',
                    'user_name' => $cbo_name,
                    'ams_name' => $sales->ams->user->name ?? '-',
                    'customer' => $sales->customer->name,
                    'hangar' => $sales->hangar_name,
                    'line' => $sales->line_name,
                    'ac_reg' => $sales->ac_reg ?? '-',
                    'tat' => $sales->tat,
                    'start_date' => Carbon::parse($sales->start_date)->format('d F Y'),
                    'end_date' => Carbon::parse($sales->end_date)->format('d F Y'),
                    'link' => $link,
                    'new_hangar' => $reschedule_hangar?->name ?? '-',
                    'new_line' => $reschedule_line?->name ?? '-',
                    'new_tat' => $tat,
                    'new_s_date' => Carbon::parse($start_date)->format('d F Y'),
                    'new_e_date' => Carbon::parse($end_date)->format('d F Y'),
                ]
            ];

            $mail_sent = Mail::to($cbo_mail)->send(new Notification($data));

            if (!$mail_sent) {
                throw new \Exception("Email not sent to {$cbo_mail}", 1);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        try {
            DB::beginTransaction();

            $reschedule = $sales->salesReschedule ?? new SalesReschedule;
            $reschedule->sales_id = $sales->id;
            $reschedule->hangar_id = $request->hangar_id ?? null;
            $reschedule->line_id = $request->line_id ?? null;
            $reschedule->start_date = $start_date;
            $reschedule->end_date = $end_date;
            $reschedule->tat = $tat;
            $reschedule->current_date = $sales->start_date;
            $reschedule->save();

            $sales_request = SalesRequest::where('sales_id', $sales->id)
                                        ->where('category', SalesRequest::REQUEST_RESCHEDULE_SALES)
                                        ->first();

            if ($sales_request) {
                $sales_request->reviewer_id = $user->id;
                $sales_request->status = SalesRequest::STATUS_REQUESTED;
                $sales_request->commit = false;
                $sales_request->save();
            } else {
                $sales_request = new SalesRequest;
                $sales_request->sales_id = $sales->id;
                $sales_request->reviewer_id = $user->id;
                $sales_request->category = SalesRequest::REQUEST_RESCHEDULE_SALES;
                $sales_request->status = SalesRequest::STATUS_REQUESTED;
                $sales_request->commit = false;
                $sales_request->save();
            }

            DB::commit();

            // Mengaktifkan kembali pencatatan aktivitas
            activity()->enableLogging();

            $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';

            // Menambahkan aktivitas log setelah data berhasil diperbarui
            activity()
                ->causedBy(auth()->user())
                ->performedOn($sales)
                ->withProperties([
                    'user_name' => auth()->user()->name,
                ])
                ->log("{$causer} sent request to Reschedule Sales Plan");

            $salesLogs = Activity::where('subject_type', Sales::class)
                ->where('subject_id', $sales->id)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Reschedule Sales Plan requested successfully',
                'logs'    => $salesLogs,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something\'s wrong with the proccess, failed to request Reschedule Sales Plan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function approveReschedule($id, Request $request)
    {
        // Menonaktifkan logging sementara
        activity()->disableLogging();

        $request->validate([
            'is_approved' => 'required|boolean',
            'reject_reason' => 'sometimes|string',
            'target_url' => 'required|string',
        ]);

        $sales = Sales::findOrFail($id);
        $status = self::REQUEST_STATUS[$request->is_approved];
        $reschedule = $sales->salesReschedule;

        if (!$reschedule) {
            return response()->json([
                'success' => false,
                'message' => "Oops, this Sales Plan doesn't have active reschedule request",
            ], 422);
        }

        if ($sales->transaction_type_id == 3) {
            return response()->json([
                'success' => false,
                'message' => "Oops, You can't reschedule PBTH Sales Plan",
            ], 422);
        }

        try {
            $cbo = auth()->user();
            $ams = $sales->ams->user;
            $link = env('FRONTEND_URL').$request->target_url;
            $data = [
                'type' => 30,
                'subject' => 'Your Reschedule Sales Plan Request '.$status,
                'body' => [
                    'message' => "Your request for rescheduling Sales Plan has been {$status}",
                    'user_name' => $ams->name,
                    'customer' => $sales->customer->name,
                    'hangar' => $reschedule?->hangar->name ?? '-',
                    'line' => $reschedule?->line->name ?? '-',
                    'ac_reg' => $sales->ac_reg ?? '-',
                    'tat' => $reschedule->tat,
                    'start_date' => Carbon::parse($reschedule->start_date)->format('d F Y'),
                    'end_date' => Carbon::parse($reschedule->end_date)->format('d F Y'),
                    'link' => $link,
                ]
            ];

            $mail_sent = Mail::to($ams->email)->send(new Notification($data));

            if (!$mail_sent) {
                throw new \Exception("Email not sent to {$ams->email}", 1);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        try {
            DB::beginTransaction();

            $old_sales = $sales;
            $replicated_sales = null;

            $sales_request = $old_sales->requestReschedule;
            $sales_request->status = $request->is_approved ? SalesRequest::STATUS_NO_REQUEST : SalesRequest::STATUS_REJECTED;
            $sales_request->reject_reason = $request->is_approved ? null : $request->reject_reason;
            $sales_request->save();

            if ($request->is_approved) {
                $sales_year = Carbon::parse($old_sales->start_date)->format('Y');
                $reschedule_year = Carbon::parse($reschedule->start_date)->format('Y');

                if ($sales_year != $reschedule_year) {
                    if ($old_sales->is_rkap) {
                        $old_prospect = $old_sales->prospect;

                        $new_prospect = $old_prospect->replicate();
                        $new_prospect->year = $reschedule_year;
                        $new_prospect->save();

                        $new_tmb = $old_prospect->tmb->replicate();
                        $new_tmb->prospect_id = $new_prospect->id;
                        $new_tmb->save();
                    }

                    $new_sales = $old_sales->replicate();
                    $new_sales->prospect_id = $new_prospect->id;
                    if ($reschedule->hangar_id && $reschedule->line_id) {
                        $new_sales->hangar_id = $reschedule->hangar_id;
                        $new_sales->line_id = $reschedule->line_id;
                    }
                    $new_sales->tat = $reschedule->tat;
                    $new_sales->start_date = $reschedule->start_date;
                    $new_sales->end_date = $reschedule->end_date;
                    $new_sales->save();

                    $new_sales_level = $old_sales->salesLevel->replicate();
                    $new_sales_level->sales_id = $new_sales->id;
                    $new_sales_level->save();

                    $new_sales_requirements = $old_sales->salesRequirements;
                    foreach ($new_sales_requirements as $requirement) {
                        $new_requirement = $requirement->replicate();
                        $new_requirement->sales_id = $new_sales->id;
                        $new_requirement->save();

                        foreach ($requirement->files as $file) {
                            $new_file = $file->replicate();
                            $new_file->sales_requirement_id = $new_requirement->id;
                            $new_file->save();
                        }
                    }

                    $new_sales_requests = $old_sales->requests;
                    foreach ($new_sales_requests as $request) {
                        $new_request = $request->replicate();
                        $new_request->sales_id = $new_sales->id;
                        $new_request->save();
                    }

                    $old_sales_level = $old_sales->salesLevel;
                    $old_sales_level->status = Sales::STATUS_CANCEL;
                    $old_sales_level->save();

                    $cancel_category = CancelCategory::firstWhere('name', 'Reschedule');
                    if (!$cancel_category) {
                        $cancel_category = new CancelCategory;
                        $cancel_category->name = 'Reschedule';
                        $cancel_category->save();
                    }

                    $old_sales_cancel = new SalesReject;
                    $old_sales_cancel->sales_id = $old_sales->id;
                    $old_sales_cancel->category_id = $cancel_category->id;
                    $old_sales_cancel->reason = "Cancelled by System - Sales Plan has been rescheduled to different year";
                    $old_sales_cancel->save();

                    $replicated_sales = $new_sales;
                } else {
                    if ($reschedule->hangar_id && $reschedule->line_id) {
                        $old_sales->hangar_id = $reschedule->hangar_id;
                        $old_sales->line_id = $reschedule->line_id;
                    }
                    $old_sales->tat = $reschedule->tat;
                    $old_sales->start_date = $reschedule->start_date;
                    $old_sales->end_date = $reschedule->end_date;
                    $old_sales->save();
                }

                $old_sales->salesReschedule->delete();
            }

            // Mengaktifkan kembali pencatatan aktivitas
            activity()->enableLogging();

            $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';

            // Menambahkan aktivitas log setelah data berhasil diperbarui
            activity()
                ->causedBy(auth()->user())
                ->performedOn($sales)
                ->withProperties([
                    'user_name' => auth()->user()->name,
                ])
                ->log("{$causer} {$status} Sales Plan reschedule request");

            $salesLogs = Activity::where('subject_type', Sales::class)
                ->where('subject_id', $sales->id)
                ->latest()
                ->get();

            if ($replicated_sales) {
                $old_sales_logs = Activity::where('subject_id', $replicated_sales->id)->get();

                if ($old_sales_logs) {
                    foreach ($old_sales_logs as $log) {
                        $new_log = $log->replicate();
                        $new_log->subject_id = $replicated_sales->id;
                        $new_log->save();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Sales Plan reschedule request {$status} successfully",
                'data'    => $sales,
                'logs'    => $salesLogs,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Something's wrong, failed to {$status} Sales Plan reschedule request",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function responseReschedule($id)
    {
        $sales = Sales::findOrFail($id);

        try {
            DB::beginTransaction();

            $reschedule_request = $sales->requestReschedule;

            if ($reschedule_request->status == SalesRequest::STATUS_REJECTED) {
                $reschedule_request->status = SalesRequest::STATUS_NO_REQUEST;
                $reschedule_request->reject_reason = null;
            }

            $reschedule_request->save();

            $sales->salesReschedule->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reschedule request status confirmed!'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getError());

            return response()->json([
                'success' => false,
                'message' => "Something's wrong, failed to confirm reschedule rejection",
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function requestCancel(Request $request)
    {
        // Menonaktifkan logging sementara
        activity()->disableLogging();

        $request->validate([
            'sales_id' => 'required|integer|exists:sales,id',
            'user_id' => 'required|integer|exists:users,id',
            'category_id' => 'required|integer|exists:cancel_categories,id',
            'reason' => 'required|string|min:10',
            'target_url' => 'required|string',
        ]);

        $sales = Sales::findOrFail($request->sales_id);
        $user = User::findOrFail($request->user_id);
        $category = CancelCategory::findOrFail($request->category_id);

        try {
            $cbo_mail = $user->email;
            $cbo_name = $user->name;
            $link = env('FRONTEND_URL').$request->target_url;
            $data = [
                'type' => 4,
                'subject' => 'New Cancel Sales Request',
                'body' => [
                    'message' => 'You have new request for Cancel Sales.',
                    'user_name' => $cbo_name,
                    'ams_name' => $sales->ams->user->name ?? '-',
                    'customer' => $sales->customer->name,
                    'ac_reg' => $sales->ac_reg ?? '-',
                    'tat' => $sales->tat,
                    'start_date' => Carbon::parse($sales->start_date)->format('d F Y'),
                    'end_date' => Carbon::parse($sales->end_date)->format('d F Y'),
                    'category' => $category->name,
                    'reason' => $request->reason,
                    'link' => $link,
                ]
            ];

            $mail_sent = Mail::to($cbo_mail)->send(new Notification($data));

            if (!$mail_sent) {
                throw new \Exception("Email not sent to {$cbo_mail}", 1);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        try {
            DB::beginTransaction();

            $sales_request = SalesRequest::where('sales_id', $request->sales_id)
                                        ->where('category', SalesRequest::REQUEST_CANCEL_SALES)
                                        ->first();

            if ($sales_request) {
                $sales_request->reviewer_id = $user->id;
                $sales_request->status = SalesRequest::STATUS_REQUESTED;
                $sales_request->commit = false;
                $sales_request->save();
            } else {
                $sales_request = new SalesRequest;
                $sales_request->sales_id = $sales->id;
                $sales_request->reviewer_id = $user->id;
                $sales_request->category = SalesRequest::REQUEST_CANCEL_SALES;
                $sales_request->status = SalesRequest::STATUS_REQUESTED;
                $sales_request->commit = false;
                $sales_request->save();
            }

            $sales_cancel =  new SalesReject;
            $sales_cancel->sales_id = $sales->id;
            $sales_cancel->category_id = $category->id;
            $sales_cancel->reason = $request->reason;
            $sales_cancel->save();

            DB::commit();

            // Mengaktifkan kembali pencatatan aktivitas
            activity()->enableLogging();

            $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';

            // Menambahkan aktivitas log setelah data berhasil diperbarui
            activity()
                ->causedBy(auth()->user())
                ->performedOn($sales)
                ->withProperties([
                    'user_name' => auth()->user()->name,
                ])
                ->log("{$causer} requested to Cancel Salesplan");

            $salesLogs = Activity::where('subject_type', Sales::class)
                ->where('subject_id', $sales->id)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Cancel sales requested successfully',
                'logs'    => $salesLogs,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something\'s wrong with the proccess, failed to request Cancel Sales Plan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function approveCancel($id, Request $request)
    {
        // Menonaktifkan logging sementara
        activity()->disableLogging();

        $request->validate([
            'is_approved' => 'required|boolean',
            'reject_reason' => 'sometimes|string',
            'target_url' => 'required|string',
        ]);

        $sales = Sales::findOrFail($id);
        $status = self::REQUEST_STATUS[$request->is_approved];
        $reject = $sales->salesReject;

        if (!$reject) {
            return response()->json([
                'success' => false,
                'message' => "Oops, this Sales Plan doesn't have active cancel request",
            ], 422);
        }

        try {
            $cbo = auth()->user();
            $ams = $sales->ams->user;
            $link = env('FRONTEND_URL').$request->target_url;
            $data = [
                'type' => 40,
                'subject' => 'Your Cancel Sales Request '.Str::title($status),
                'body' => [
                    'message' => "Your request for canceling sales has been {$status}",
                    'user_name' => $ams->name,
                    'customer' => $sales->customer->name,
                    'ac_reg' => $sales->ac_reg ?? '-',
                    'tat' => $sales->tat,
                    'start_date' => Carbon::parse($sales->start_date)->format('d F Y'),
                    'end_date' => Carbon::parse($sales->end_date)->format('d F Y'),
                    'link' => $link,
                ]
            ];

            $mail_sent = Mail::to($ams->email)->send(new Notification($data));

            if (!$mail_sent) {
                throw new \Exception("Email not sent to {$ams->email}", 1);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        try {
            DB::beginTransaction();

            $sales_request = $sales->requestCancel;
            $sales_request->status = $request->is_approved ? SalesRequest::STATUS_APPROVED : SalesRequest::STATUS_REJECTED;
            $sales_request->reject_reason = $request->is_approved ? null : $request->reject_reason;
            $sales_request->commit = $request->is_approved;
            $sales_request->save();

            if ($request->is_approved) {
                $sales_level = $sales->salesLevel;
                $sales_level->status = Sales::STATUS_CANCEL;
                $sales_level->push();
            }

            DB::commit();

            // Mengaktifkan kembali pencatatan aktivitas
            activity()->enableLogging();

            $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';

            // Menambahkan aktivitas log setelah data berhasil diperbarui
            activity()
                ->causedBy(auth()->user())
                ->performedOn($sales)
                ->withProperties([
                    'user_name' => auth()->user()->name,
                ])
                ->log("{$causer} {$status} Cancel Sales request");

            $salesLogs = Activity::where('subject_type', Sales::class)
                ->where('subject_id', $sales->id)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => "Cancel Sales {$status} successfully",
                'data'    => $sales,
                'logs'    => $salesLogs,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Something's wrong, failed to {$status} Sales Plan cancel request",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function responseCancel($id)
    {
        $sales = Sales::findOrFail($id);

        try {
            DB::beginTransaction();

            $cancle_request = $sales->requestCancel;

            if ($cancle_request->status == SalesRequest::STATUS_REJECTED) {
                $cancle_request->status = SalesRequest::STATUS_NO_REQUEST;
                $cancle_request->reject_reason = null;
            }

            $cancle_request->save();

            $sales->salesReject->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cancel request status confirmed!'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getError());

            return response()->json([
                'success' => false,
                'message' => "Something's wrong, failed to confirm cancel rejection",
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function inputSONumber($id, Request $request)
    {
        // Menonaktifkan logging sementara
        activity()->disableLogging();

        $request->validate(['so_number' => 'required|string']);

        try {
            DB::beginTransaction();

            $sales = Sales::findOrFail($id);
            $wo_po = $sales->salesRequirements->where('requirement_id', 9)->first();

            if($wo_po->status != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please complete your WO/PO Number requirement!',
                ], 422);
            }

            $sales->so_number = $request->so_number;
            $sales->save();

            $sales_level = $sales->salesLevel;
            $sales_level->status = Sales::STATUS_CLOSED_IN;
            $sales_level->save();

            $sales->setRequirement(10);

            DB::commit();

            // Mengaktifkan kembali pencatatan aktivitas
            activity()->enableLogging();

            $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';

            // Menambahkan aktivitas log setelah data berhasil diperbarui
            activity()
                ->causedBy(auth()->user())
                ->performedOn($sales)
                ->withProperties([
                    'user_name' => auth()->user()->name,
                ])
                ->log("{$causer} submitted SO Number and closed-in the Sales Plan");

            $salesLogs = Activity::where('subject_type', Sales::class)
                ->where('subject_id', $sales->id)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Closed-In Sales Plan successfully with provided SO Number',
                'data'    => $sales,
                'logs'    => $salesLogs,
            ], 200);
        } catch (QueryException $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error occured, failed to Closed-In Sales Plan with provided SO Number',
            ], 500);
        }
    }

    public function acReg()
    {
        $ac_regs = Sales::select('ac_reg')->distinct()->pluck('ac_reg');

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data successfully',
            'data' => $ac_regs,
        ], 200);
    }
}
