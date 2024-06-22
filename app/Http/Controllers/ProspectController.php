<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\TMB;
use App\Models\PBTH;
use App\Models\Sales;
use App\Models\Customer;
use App\Models\Prospect;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Helpers\PaginationHelper as PG;
use App\Http\Requests\ProspectRequest;
use Illuminate\Support\Arr;

class ProspectController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->year ?? false;
        $search = $request->search;
        $filter = $request->filter;
        $order = $request->order ?? 'id';
        $by = $request->by ?? 'desc';
        $paginate = $request->paginate ?? 10;

        $user = auth()->user();

        $tmb = TMB::whereHas('prospect', function ($query) use ($user, $year) {
                        $query->when($year, function ($query) use ($year) {
                            $query->where('year', $year);
                        });
                        $query->when($user->hasRole('AMS'), function ($query) use ($user) {
                            $query->whereRelation('amsCustomer', 'ams_id', $user->ams->id);
                        });
                    })->sum('market_share');
        $pbth = PBTH::whereHas('prospect', function ($query) use ($user, $year) {
                        $query->when($year, function ($query) use ($year) {
                            $query->where('year', $year);
                        });
                        $query->when($user->hasRole('AMS'), function ($query) use ($user) {
                            $query->whereRelation('amsCustomer', 'ams_id', $user->ams->id);
                        });
                    })->sum('market_share');
        $total_rkap  = $tmb + $pbth;
        $total_sales = Sales::user($user)->year($year)->sum('value');

        $data = Prospect::search($search)
                        ->User($user)
                        ->filter($filter)
                        ->sort($order, $by)
                        ->paginate($paginate)
                        ->withQueryString();

        return response()->json([
            'status' => 'Success!',
            'message' => 'Successfully Get Prospect',
            'data' => [
                'prospect' => $data,
                'totalMarketShare' => $total_sales,
                'totalSalesPlan' => $total_rkap,
                'deviation' => $total_sales - $total_rkap,
            ]
        ], 200);
    }

    public function tmbOnly(Request $request)
    {
        $customer = $request->customer;
        
        $prospects = Prospect::with([
                                'transactionType',
                                'prospectType',
                                'strategicInitiative',
                                'pm',
                                'amsCustomer',
                            ])->whereIn('transaction_type_id', [1,2])
                            ->whereHas('amsCustomer', function ($query) use ($customer) {
                                $query->where('customer_id', $customer);
                            })->get();

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data successfully',
            'data' => $prospects,
        ], 200);
    }

    public function create(ProspectRequest $request)
    {
        $prospect_type = $request->prospect_type_id;
        $transaction_type = $request->transaction_type_id;

        if ($prospect_type == 1) {
            $p_type = 'Organic';
        } else if ($prospect_type == 2) {
            $p_type = 'In-organic';
        }

        if (in_array($transaction_type, [1,2])) {
            $t_type = ($transaction_type == 1) ? 'TMB Retail' : 'TMB Project';
        } else if ($transaction_type == 3) {
            $t_type = 'PBTH';
        }

        try {
            DB::beginTransaction();

            if (in_array($transaction_type, [1,2])) {
                foreach ($request->tmb as $product) {
                    foreach ($product['product'] as $data) {
                        $prospect = new Prospect;
                        $prospect->prospect_type_id = $prospect_type;
                        $prospect->transaction_type_id = $transaction_type;
                        $prospect->year = $request->year;
                        $prospect->ams_customer_id = $request->ams_customer_id;
                        $prospect->strategic_initiative_id = $request->strategic_initiative_id ?? null;
                        $prospect->pm_id = $request->pm_id ?? null;
                        $prospect->save();

                        $tmb = new TMB;
                        $tmb->prospect_id = $prospect->id;
                        $tmb->product_id = $data['product_id'];
                        $tmb->ac_type_id = $data['aircraft_type']['id'] ?? null;
                        $tmb->component_id = $data['component']['id'] ?? null;
                        $tmb->engine_id = $data['engine']['id'] ?? null;
                        $tmb->igte_id = $data['igte']['id'] ?? null;
                        $tmb->learning_id = $data['learning']['id'] ?? null;
                        $tmb->market_share = $data['market_share'];
                        $tmb->remarks = $data['remark'];
                        $tmb->maintenance_id = $data['maintenance_id']['id'];
                        $tmb->save();
                    }
                }
            } else if ($transaction_type == 3) {
                foreach ($request->pbth as $product) {
                    $prospect = new Prospect;
                    $prospect->prospect_type_id = $prospect_type;
                    $prospect->transaction_type_id = $transaction_type;
                    $prospect->year = $request->year;
                    $prospect->ams_customer_id = $request->ams_customer_id;
                    $prospect->strategic_initiative_id = $request->strategic_initiative_id ?? null;
                    $prospect->pm_id = $request->pm_id ?? null;
                    $prospect->save();

                    foreach ($product['target'] as $target) {
                        $pbth = new PBTH;
                        $pbth->month = Carbon::parse("1 {$target['month']}")->month;
                        $pbth->rate = $target['rate'];
                        $pbth->flight_hour = $target['flight_hour'];
                        $pbth->prospect_id = $prospect->id;
                        $pbth->product_id = $product['product_id'];
                        $pbth->ac_type_id = $product['aircraft_type_id'];
                        $pbth->market_share = $target['rate'] * $target['flight_hour'];
                        $pbth->save();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$p_type} {$t_type} Prospect created successfully",
                'data' => $prospect,
            ], 200);
        } catch (QueryException $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
		$user = auth()->user();
		$market_share = Prospect::marketShareByCustomer($id, $user);
        $total_sales = Sales::user($user)->customer($id)->sum('value');
        $data = Prospect::with(
                        'transactionType',
                        'prospectType',
                        'strategicInitiative',
                        'pm',
                        'amsCustomer',
                        'sales',
                        'tmb',
                        'pbth',
                        'amsCustomer.customer',
                        'amsCustomer.area',
                        'amsCustomer.ams',
                        'pbth.product',
                        'pbth.acType',
                        )->find($id);

        return response()->json([
            'message' => 'Success Get Prospect By Customer!',
            'data' => [
                'prospect' => $data,
				'marketShare' => $market_share,
                'salesPlan' => $total_sales,
                'deviation' => $market_share - $total_sales,
            ]
        ], 200);
    }

    public function pbth($id)
    {
        $prospect = Prospect::findOrFail($id);

        $this->authorize('pickUpSales', $prospect);

        $market_share = $prospect->market_share;
        $sales_plan = $prospect->sales_plan;
        $data = PBTH::where('prospect_id', $id)
                    ->with(['product', 'acType'])
                    ->get();

        return response()->json([
            'data' => [
                'customer' => $prospect->amsCustomer->customer,
                'registration' => $prospect->registration,
                'market_share' => $market_share,
                'sales_plan' => $sales_plan,
                'deviation' => $market_share - $sales_plan,
                'prospect' => $data
            ],
        ], 200);
    }

    public function tmb($id)
    {
        $prospect       = Prospect::findOrFail($id);

        $this->authorize('pickUpSales', $prospect);

        $market_share   = $prospect->market_share;
        $sales_plan     = $prospect->sales_plan;

        $data       = TMB::where('prospect_id', $id)
                        ->with([
                            'product',
                        ])->get();

        return response()->json([
            'data' => [
                'customer' => $prospect->amsCustomer->customer,
                'registration' => $prospect->registration,
                'market_share' => $market_share,
                'sales_plan' => $sales_plan,
                'deviation' => $market_share - $sales_plan,
                'prospect' => $data
            ],
        ], 200);
    }
}
