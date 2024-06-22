<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Sales;
use App\Models\SalesRequirement;
use App\Models\SalesLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Http\Requests\FileRequest;
use Spatie\Activitylog\Models\Activity;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $requirement = $request->requirement;

        $files = File::when($requirement, function ($query) use ($requirement) {
                            $query->where('sales_requirement_id', $requirement);
                        })->get();

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data successfully',
            'data' => $files,
        ], 200);
    }

    public function store(FileRequest $request)
    {
        // Menonaktifkan logging sementara
        activity()->disableLogging();

        try {
            DB::beginTransaction();

            $sales = Sales::findOrFail($request->sales_id);
            $requirement_id = $request->requirement_id;
            $files = $request->file('files');
            $temp_paths = [];
            $temp_files = [];

            $requirement = $sales->setRequirement($requirement_id);

            foreach ($files as $file) {
                $year = Carbon::now()->format('Y');
                $month = Carbon::now()->format('F');

                $clean_name = preg_replace('/\.(?=.*\.)/', '', $file->getClientOriginalName());
                $file_name = Carbon::now()->format('dmyHis').'_'.$clean_name;
                $file_path = Storage::disk('public')->putFileAs("attachment/{$year}/{$month}", $file, $file_name);
                $temp_paths[] = $file_path;

                $new_file = new File;
                $new_file->sales_requirement_id = $requirement->id;
                $new_file->path = $file_path;
                $new_file->uploaded_by = auth()->user()->id;
                $new_file->save();

                $temp_files[] = $new_file;
            }

            DB::commit();

            // Mengaktifkan kembali pencatatan aktivitas
            activity()->enableLogging();
            
            $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';
            $requirement_name = $requirement->requirement->requirement;

            // Menambahkan aktivitas log setelah data berhasil diperbarui
            activity()
                ->causedBy(auth()->user())
                ->performedOn($sales)
                ->withProperties([
                    'user_name' => auth()->user()->name,
                ])
                ->log("{$causer} uploaded \"{$requirement_name}\" file requirement");

            $salesLogs = Activity::where('subject_type', Sales::class)
                                ->where('subject_id', $sales->id)
                                ->latest()
                                ->get();

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => $temp_files,
            ], 200);
        } catch (QueryException $e) {
            DB::rollback();
            Log::error($e->getMessage());

            for ($i = 0; $i < count($temp_paths); $i++) {
                Storage::disk('public')->delete($temp_paths[$i]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error occured, failed to upload file',
            ], 500);
        }
    }

    public function show($id)
    {
        $file = File::find($id);

        if (!$file || !$file->path) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found',
            ], 404);
        }

        $headers = [
            'Content-Type' => $file->content_type,            
            'Content-Disposition' => 'attachment; filename="'.$file->file_name.'"',
        ];

        return \Response::make(Storage::disk('public')->get($file->path), 200, $headers);
    }

    public function history($sales_id, Request $request)
    {
        $sales = Sales::findOrFail($sales_id);
        $filter = $request->filter;

        if (!$sales) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found',
            ], 404);
        }

        $requirements = $sales->salesRequirements->whereNotIn('requirement_id', [1, 4, 10]);
        $requirement_ids = [];
        
        foreach ($requirements as $item) {
            $requirement_ids[] = $item->id;
        }

        $file_histories = [];
        for ($i = 1; $i <= 4; $i++) {
            $files = File::whereIn('sales_requirement_id', $requirement_ids)
                        ->whereHas('salesRequirement', function ($query) use ($i) {
                            $query->whereRelation('requirement', 'level_id', $i);
                        })
                        ->when($filter, function ($query) use ($filter) {
                            $query->whereMonth("updated_at", $filter);
                        })
                        ->get()
                        ->groupBy(function ($item) {
                            return $item->updated_at->format('d F Y');
                        });

            $level_history = [];
            foreach ($files as $key => $file) {
                $date = $key;
                $level_history[] = [
                    'uploadedAt' => $date,
                    'totalFiles' => $file->count(),
                    'files' => $file,
                ];
            }

            if (count($level_history) >= 1) {
                $data_files = collect($level_history)->sortByDesc('uploadedAt')->values();
            } else {
                $data_files = null;
            }

            $file_histories["level$i"] = $data_files;
        }

        $month = Carbon::create()->day(1)->month($filter)->year(Carbon::now()->format('Y'));

        $data = collect([
            'month' => $month->format('F Y'),
            'history' => $file_histories,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data successfully',
            'data' => $data,
        ], 200);
    }

    public function destroy($id)
    {
        // Menonaktifkan logging sementara
        activity()->disableLogging();

        $file = File::find($id);

        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found',
            ], 404);
        }

        try {
            DB::beginTransaction();
            
            $requirement = $file->salesRequirement;
            $sales = $requirement->sales;

            $file_path = $file->path;
            $file->delete();

            $files = $requirement->files;
            $requirement->status = $files->isNotEmpty();
            $requirement->save();

            // Mengaktifkan kembali pencatatan aktivitas
            activity()->enableLogging();

            $causer = auth()->user()->name.' ('.auth()->user()->nopeg.')';
            $requirement_name = $requirement->requirement->requirement;

            // Menambahkan aktivitas log setelah data berhasil diperbarui
            activity()
                ->causedBy(auth()->user())
                ->performedOn($sales)
                ->withProperties([
                    'user_name' => auth()->user()->name,
                ])
                ->log("{$causer} deleted \"{$requirement_name}\" file requirement");

            $salesLogs = Activity::where('subject_type', Sales::class)
                                ->where('subject_id', $sales->id)
                                ->latest()
                                ->get();

            DB::commit();

            if (Storage::disk('public')->exists($file_path)) {
                Storage::disk('public')->delete($file_path);
            }

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error occured, failed to delete file',
            ], 500);
        }
    }
}
