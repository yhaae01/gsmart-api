<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Area;
use App\Models\Line;
use App\Models\SalesLevel;
use App\Models\SalesRequirement;
use App\Models\Requirement;
use Illuminate\Support\Collection;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sales extends Model
{
    use HasFactory, LogsActivity;

    const STATUS_OPEN = 1;
    const STATUS_CLOSED_SALES = 2;
    const STATUS_CLOSED_IN = 3;
    const STATUS_CANCEL = 4;
    const IS_RKAP = 1;
    const ADDITIONAL = 0;

    const STATUS_ARRAY = [
        self::STATUS_OPEN => 'Open',
        self::STATUS_CLOSED_SALES => 'Closed Sales',
        self::STATUS_CLOSED_IN => 'Closed In',
        self::STATUS_CANCEL => 'Cancel',
    ];

    const RKAP_ARRAY = [
        self::IS_RKAP => 'RKAP',
        self::ADDITIONAL => 'Additional',
    ];

    protected $fillable = [
        'customer_id',
        'product_id',
        'ams_id',
        'prospect_id',
        'maintenance_id',
        'ac_reg',
        'value',
        'tat',
        'start_date',
        'so_number',
        'is_rkap',
    ];

    protected $hidden = [
        'prospect',
        'salesRequirements',
        'salesLevel',
    ];

    protected $appends = [
        'status',
        'other',
        'registration',
        'type',
        'level',
        'progress',
        'contact_persons',
        'level4',
        'level3',
        'level2',
        'level1',
        'upgrade_level',
        'month_sales',
        'market_share',
        'year',
        'line_name',
        'hangar_name',
        'maintenance_name',
        'ams_info',
        'ams_initial',
        'area',
        'active_request'
    ];

    public function getActiveRequestAttribute()
    {
        $active_request = $this->requests->firstWhere('status', SalesRequest::STATUS_REQUESTED);

        return $active_request ?? null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['product_id', 'maintenance_id', 'ac_reg', 'value', 'tat', 'start_date', 'end_date'])
            ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName}")
            ->useLogName('Sales');
    }

    public function getAreaAttribute()
    {
        if ($this->prospect) {
            return $this->prospect->amsCustomer->area?->name;
        }

        $customer = $this->customer->id;
        $area = Area::whereHas('amsCustomer', function ($query) use ($customer) {
                    $query->whereHas('customer', function ($query) use ($customer) {
                        $query->where('id', $customer);
                    });
                })->first();

        return $area?->name;
    }

    public function getAmsInitialAttribute()
    {
        return $this->ams?->initial ?? null;
    }

    public function getAmsInfoAttribute()
    {
        return "{$this->ams->initial} - {$this->ams->user->name}";
    }

    public function scopeCancelCategory($query, $category)
    {
        return $query->whereHas('salesReject', function ($query) use ($category) {
            $query->whereRelation('category', 'name', $category);
        });
    }

    public function scopeCustomerName($query, $customer)
    {
        $query->whereRelation('customer', 'name', 'LIKE', "%$customer%");
    }

    public function scopeMonth($query, $month)
    {
        $query->whereMonth('start_date', $month)
            ->whereYear('start_date', Carbon::now()->format('Y'));
    }

    public function scopeAms($query, $ams)
    {
        $query->whereRelation('ams', 'initial', $ams);
    }

    public function scopeAreaByAms($query, $area, $ams)
    {
        $query->whereHas('ams', function ($query) use ($area, $ams) {
            $query->where('initial', $ams)
                ->whereHas('amsCustomers', function ($query) use ($area) {
                $query->whereRelation('area', 'name', $area);
            });
        });
    }

    public function scopeAreaKAM($query, $ams)
    {
        $query->whereHas('ams', function ($query) use ($ams) {
            $query->where('initial', $ams)
                ->whereHas('amsCustomers', function ($query) {
                $query->whereRelation('area', 'name', 'LIKE', 'KAM%');
            });
        });
    }

    public function scopeArea($query, $area)
    {
        $query->whereHas('customer', function ($query) use ($area) {
            $query->whereHas('amsCustomers', function ($query) use ($area) {
                $query->whereRelation('area', 'name', $area);
            });
        });
    }

    public function scopeGroupType($query, $groupType)
    {
        $query->whereRelation('customer', 'group_type', $groupType);
    }

    public function scopeProduct($query, $product)
    {
        $query->whereRelation('product', 'name', $product);
    }

    public function scopeClean($query)
    {
        $query->whereRelation('salesLevel', 'status', '!=', 4);
    }

    public function scopeLevel($query, $level)
    {
        $query->whereRelation('salesLevel', 'level_id', $level);
    }

    public function scopeOpen($query)
    {
        $query->whereRelation('salesLevel', 'status', 1);
    }

    public function scopeClosedSales($query)
    {
        $query->whereRelation('salesLevel', 'status', 2);
    }

    public function scopeClosedIn($query)
    {
        $query->whereRelation('salesLevel', 'status', 3);
    }

    public function scopeCancel($query)
    {
        $query->whereRelation('salesLevel', 'status', 4);
    }

    public function scopeAdditional($query)
    {
        $query->where('is_rkap', 0);
    }

    public function scopeRkap($query)
    {
        $query->where('is_rkap', 1);
    }

    public function scopeYear($query, $year)
    {
        $query->when($year, function ($query) use ($year) {
            $this->whereYearArrayFilter($query, 'start_date', $year);
        });
    }

    public function scopeUser($query, $user)
    {
        $query->when($user->hasRole('AMS'), function ($query) use ($user) {
            $query->where('ams_id', $user->ams->id);
        });
    }

    public function scopeCustomer($query, $customer)
    {
        $query->where('customer_id', $customer);
    }

    public function scopeSearch($query, $search)
    {
        $query->when($search, function ($query) use ($search) {
            if (str_contains(strtolower($search), 'rkap')) {
                $query->where('is_rkap', 1);
            } else if (str_contains(strtolower($search), 'additional')) {
                $query->where('is_rkap', 0);
            } else if (!strcasecmp($search, 'open')) {
                $query->whereRelation('salesLevel', 'status', 1);
            } else if (!strcasecmp($search, 'cancel')) {
                $query->whereRelation('salesLevel', 'status', 4);
            } else if (str_contains(strtolower($search), 'closed')) {
                if (!strcasecmp($search, 'closed sales')) {
                    $query->whereRelation('salesLevel', 'status', 2);
                } else if (!strcasecmp($search, 'closed in')) {
                    $query->whereRelation('salesLevel', 'status', 3);
                } else {
                    $query->whereRelation('salesLevel', 'status', 2)
                        ->orWhereRelation('salesLevel', 'status', 3);
                }
            } else if (str_contains(strtolower($search), 'level')) {
                $query->whereRelation('salesLevel', 'level_id', substr($search, -1));
            } else {
                $query->whereRelation('customer', 'name', 'LIKE', "%$search%")
                    ->orWhereRelation('product', 'name', 'LIKE', "%$search%")
                    ->orWhereRelation('acType', 'name', 'LIKE', "%$search%")
                    ->orWhereRelation('engine', 'name', 'LIKE', "%$search%")
                    ->orWhereRelation('apu', 'name', 'LIKE', "%$search%")
                    ->orWhereRelation('component', 'name', 'LIKE', "%$search%")
                    ->orWhereRelation('hangar', 'name', 'LIKE', "%$search%")
                    ->orWhereRelation('salesLevel', 'level_id', 'LIKE', "%$search%")
                    ->orWhereRelation('transactionType', 'name', 'LIKE', "%$search%")
                    ->orWhereRelation('ams', 'initial', 'LIKE', "%$search%")
                    ->orWhere('ac_reg', 'LIKE', "%$search%")
                    ->orWhere('value', 'LIKE', "%$search%")
                    ->orWhere('value', 'LIKE', "%$search%")
                    ->orWhere('tat', 'LIKE', "%$search%")
                    ->orWhere('start_date', 'LIKE', "%$search%")
                    ->orWhere('end_date', 'LIKE', "%$search%")
                    ->orWhereHas('prospect', function ($query) use ($search) {
                        $query->whereHas('transactionType', function ($query) use ($search) {
                            $query->where('name', 'LIKE', "%$search%");
                        })
                        ->orWhereHas('amsCustomer', function ($query) use ($search) {
                            $query->whereRelation('area', 'name', 'LIKE', "%$search%");
                        });
                    });
            }
        });
    }

    public function scopeProductId($query, $product) {
        $query->when($product, function ($query) use ($product) {
            $this->whereArrayFilter($query, 'product_id', $product);
        });
    }

    public function scopeCustomerId($query, $customer) {
        $query->when($customer, function ($query) use ($customer) {
            $this->whereArrayFilter($query, 'customer_id', $customer);
        });
    }

    public function scopeAmsInitial($query, $ams) {
        $query->when($ams, function ($query) use ($ams) {
            $this->whereArrayFilter($query, 'ams_id', $ams);
        });
    }

    public function scopeThisYear($query, $date_range)
    {
        $no_filter = (count($date_range) == 0);

        $query->when($no_filter, function ($query) {
            $query->whereYear('start_date', Carbon::now()->format('Y'));
        });
    }

    public function scopeFilter($query, array $filters)
    {
        $start_date = $filters['start_date'] ?? false;
        $end_date = $filters['end_date'] ?? false;
        $type = $filters['type'] ?? false;
        $customer = $filters['customer'] ?? false;
        $product = $filters['product'] ?? false;
        $ac_type = $filters['ac_type'] ?? false;
        $component = $filters['component'] ?? false;
        $engine = $filters['engine'] ?? false;
        $apu = $filters['apu'] ?? false;
        $ac_reg = $filters['ac_reg'] ?? false;
        $other = $filters['other'] ?? null;
        $level = $filters['level'] ?? false;
        $progress = $filters['progress'] ?? false;
        $status = $filters['status'] ?? false;
        $year = $filters['year'] ?? false;
        $ams = $filters['ams'] ?? false;
        $area = $filters['area'] ?? false;

        $query->when($area, function ($query) use ($area) {
            $query->whereHas('customer', function ($query) use ($area) {
                $query->whereHas('amsCustomers', function ($query) use ($area) {
                    $this->whereRelationArrayFilter($query, 'name', 'area', $area);
                });
            });
        });

        $query->when($ams, function ($query) use ($ams) {
            $this->whereRelationArrayFilter($query, 'initial', 'ams', $ams);
        });

        $query->when(($start_date && $end_date), function ($query) use ($start_date, $end_date) {
            $query->where('start_date', '>=', Carbon::parse($start_date)->format('Y-m-d'))
                ->where('start_date', '<=', Carbon::parse($end_date)->format('Y-m-d'));
        });

        $query->when($type, function ($query) use ($type) {
            $this->whereArrayFilter($query, 'transaction_type_id', $type);
        });

        $query->when($customer, function ($query) use ($customer) {
            $this->whereArrayFilter($query, 'customer_id', $customer);
        });

        $query->when($product, function ($query) use ($product) {
            $this->whereArrayFilter($query, 'product_id', $product);
        });

        $query->when($ac_type, function ($query) use ($ac_type) {
            $this->whereArrayFilter($query, 'ac_type_id', $ac_type);
        });

        $query->when($component, function ($query) use ($component) {
            $this->whereArrayFilter($query, 'component_id', $component);
        });

        $query->when($engine, function ($query) use ($engine) {
            $this->whereArrayFilter($query, 'engine_id', $engine);
        });

        $query->when($apu, function ($query) use ($apu) {
            $this->whereArrayFilter($query, 'apu_id', $apu);
        });

        $query->when($ac_reg, function ($query) use ($ac_reg) {
            $this->whereArrayFilter($query, 'ac_reg', $ac_reg);
        });

        $query->when(isset($other), function ($query) use ($other) {
            $this->whereArrayFilter($query, 'is_rkap', $other);
        });

        $query->when($level, function ($query) use ($level) {
            $this->whereRelationArrayFilter($query, 'level_id', 'salesLevel', $level);
        });

        $query->when($progress, function ($query) use ($progress) {
            $query->withSum('requirementDone', 'value')
                ->having('requirement_done_sum_value', '=', $progress);
        });

        $query->when($status, function ($query) use ($status) {
            $this->whereRelationArrayFilter($query, 'status', 'salesLevel', $status);
        });

        $query->when($year, function ($query) use ($year) {
            $this->whereYearArrayFilter($query, 'start_date', $year);
        });
    }

    private function whereArrayFilter($query, $key, $value) 
    {
        if (is_array($value)) {
            $flattened = collect($value)->flatten()->all();
            return $query->whereIn($key, $flattened);
        } else {
            return $query->where($key, $value);
        }
    }

    private function whereYearArrayFilter($query, $key, $value) 
    {
        if (is_array($value)) {
            $flattened = collect($value)->flatten()->all();
            return $query->whereIn(\DB::raw('YEAR('.$key.')'), $flattened);
        } else {
            return $query->whereYear($key, $value);
        }
    }

    private function whereWithCountAndHavingArrayFilter($query, $withCountKey, $havingKey, $havingOperator, $value) 
    {
        if (is_array($value)) {
            
            $valueArray = [];
            foreach ($value as $key => $v) {
                $flattened = collect($v)->flatten()->all();
                $valueArray[$key] = $query;
            }
            return $valueArray;
        } else {
            return $query->withCount($withCountKey)
                ->having($havingKey, $havingOperator, $value);
        }
    }

    private function whereRelationArrayFilter($query, $primaryKey, $foreignKey, $value) 
    {
        if (is_array($value)) {
            $flattened = collect($value)->flatten()->all();
            return $query->whereRelation($foreignKey, fn($q) => $q->whereIn($primaryKey, $flattened));
        } else {
            return $query->whereRelation($foreignKey, $primaryKey, $value);
        }
    }

    // TODO: Sort data by AMS & Area
    public function scopeSort($query, $order, $by)
    {
        $query->when(($order && $by), function ($query) use ($order, $by) {
            if ($order == 'customer') {
                $query->withAggregate('customer', 'name')
                    ->orderBy('customer_name', $by);
            } else if ($order == 'product') {
                $query->withAggregate('product', 'name')
                    ->orderBy('product_name', $by);
            } else if ($order == 'registration') {
                $query->withAggregate('acType', 'name')
                    ->withAggregate('engine', 'name')
                    ->withAggregate('apu', 'name')
                    ->withAggregate('component', 'name')
                    ->orderBy('ac_type_name', $by)
                    ->orderBy('engine_name', $by)
                    ->orderBy('apu_name', $by)
                    ->orderBy('component_name', $by);
            } else if ($order == 'acReg') {
                $query->orderBy('ac_reg', $by);
            } else if ($order == 'other') {
                $query->orderBy('is_rkap', $by);
            } else if ($order == 'type') {
                $query->withAggregate('transactionType', 'name')
                    ->orderBy('transaction_type_name', $by);
            } else if ($order == 'level') {
                $query->withAggregate('salesLevel', 'level_id')
                    ->orderBy('sales_level_level_id', $by);
            } else if ($order == 'status') {
                $query->withAggregate('salesLevel', 'status')
                    ->orderBy('sales_level_status', $by);
            } else if ($order == 'progress') {
                $query->withSum('requirementDone', 'value')
                    ->orderBy('requirement_done_sum_value', $by);
            } else if ($order == 'ams') {
                $query->withAggregate('ams', 'initial')
                    ->orderBy('ams_initial', $by);
            // } else if ($order == 'area') {
            //     $query->withAggregate('prospect', 'ams_customer_id')
            //         ->orderBy('prospect_ams_customer_id', $by);
            } else if ($order == 'id') {
                $query->orderBy('id', $by);
            }
        });
        $query->orderBy('start_date', 'desc');
    }

    public function setRequirement($requirement_id)
    {
        $requirement = $this->salesRequirements->where('requirement_id', $requirement_id);
        $value = Requirement::find($requirement_id)->value;

        if ($requirement->isEmpty()) {
            $requirement = new SalesRequirement;
            $requirement->sales_id = $this->id;
            $requirement->requirement_id = $requirement_id;
            $requirement->value = $value;
            $requirement->status = 1;
            $requirement->save();
        } else {
            if ($requirement->count() > 1) {
                foreach ($requirement as $item) {
                    if ($requirement->count() > 1) {
                        $item->delete();
                    }
                }
            }
            $requirement = $requirement->first();
            $requirement->status = 1;
            $requirement->push();
        }

        return $requirement;
    }

    // TODO: Deleted soon
    public function getLineNameAttribute()
    {
        return $this->line?->name ?? '-';
    }

    public function getHangarNameAttribute()
    {
        return $this->hangar?->name ?? '-';
    }

    public function getMaintenanceNameAttribute()
    {
        $maintenances = $this->maintenance;

        if ($maintenances->isEmpty()) {
            return '-';
        }

        if (count($maintenances) == 1) {
            return $maintenances->first()->maintenance->name;
        }

        return implode("; ", $maintenances->pluck('maintenance.name')->toArray());
    }

    public function getYearAttribute()
    {
        return Carbon::parse($this->start_date)->format('Y');
    }

    public function getMarketShareAttribute()
    {
        return $this->prospect->market_share ?? null;
    }

    public function getMonthSalesAttribute()
    {
        return Carbon::parse($this->start_date)->format('F');
    }

    public function getStatusAttribute()
    {
        return self::STATUS_ARRAY[$this->salesLevel->status];
    }

    public function getOtherAttribute()
    {
        return self::RKAP_ARRAY[$this->is_rkap ?? 0];
    }

    public function getRegistrationAttribute()
    {
        $ac_type = $this->acType?->name ?? null;
        $engine = $this->engine?->name ?? null;
        $apu = $this->apu?->name ?? null;
        $component = $this->component?->name ?? null;

        $regs = [$ac_type, $engine, $apu, $component];
        $regs = implode('/', array_filter($regs));

        if (in_array($this->transaction_type_id, [1,2])) {
            $registration = !empty($regs) ? $regs : '-';
        } else {
            $registration = $ac_type ?? '-';
        }
        return $registration;
    }

    public function getTypeAttribute()
    {
        return $this->transactionType?->name ?? '-';
    }

    public function getLevelAttribute()
    {
        return $this->salesLevel->level_id;
    }

    public function getProgressAttribute()
    {
        $progress = 0;
        $requirements = $this->requirementDone()->get();

        foreach ($requirements as $req) {
            $progress += $req->value;
        }

        return $progress;
    }

    public function getContactPersonsAttribute()
    {
        return $this->customer->contactPersons;
    }

    public function getLevel4Attribute()
    {
        $requirements = $this->salesRequirements->whereIn('requirement_id', [1, 2, 3, 4]);
        $level4 = new Collection();
        
        foreach ($requirements as $item) {
            if ($item->requirement_id == 1) {
                $data = $this->contact_persons->sortByDesc('updated_at')->take(10)->values();
                if ($data->isNotEmpty()) {
                    $last_update = Carbon::parse($this->customer->latestCP->updated_at)->format('Y-m-d H:i');
                } else {
                    $last_update = null;
                }
            } else if ($item->requirement_id == 4) {
                $data = $this->hangar;
                if ($data) {
                    $data = [
                        'hangar' => $this->hangar_name,
                        'line' => $this->line_name,
                        'tat' => $this->tat,
                        'registration' => $this->registration,
                        'startDate' => Carbon::parse($this->start_date)->format('d-m-Y'),
                        'endDate' => Carbon::parse($this->end_date)->format('d-m-Y'),
                    ];
                    $last_update = Carbon::parse($this->updated_at)->format('Y-m-d H:i');
                } else {
                    $last_update = null;
                }
            } else {
                $data = $item->files;
                if ($data->isNotEmpty()) {
                    $last_update = Carbon::parse($item->latestFile->updated_at)->format('Y-m-d H:i');
                } else {
                    $last_update = null;
                }
            }

            $level4->push((object)[
                'sequence' => $item->requirement_id,
                'name' => $item->requirement->requirement,
                'status' => $item->status,
                'lastUpdate' => $last_update,
                'data' => $data,
            ]);
        }

        return collect($level4)->sortBy('sequence')->values();
    }

    public function getLevel3Attribute()
    {
        $requirements = $this->salesRequirements->whereIn('requirement_id', [5, 6, 7]);
        $level3 = new Collection();
        
        foreach ($requirements as $item) {;
            $data = $item->files;
            if ($data->isNotEmpty()) {
                $last_update = Carbon::parse($item->latestFile->updated_at)->format('Y-m-d H:i');
            } else {
                $last_update = null;
            }

            $level3->push((object)[
                'sequence' => $item->requirement_id,
                'name' => $item->requirement->requirement,
                'status' => $item->status,
                'lastUpdate' => $last_update,
                'data' => $data,
            ]);
        }

        return collect($level3)->sortBy('sequence')->values();
    }

    public function getLevel2Attribute()
    {
        $requirement = $this->salesRequirements->where('requirement_id', 8)->first();
        $level2 = new Collection();
        
        $data = $requirement->files;
        if ($data->isNotEmpty()) {
            $last_update = Carbon::parse($requirement->latestFile->updated_at)->format('Y-m-d H:i');
        } else {
            $last_update = null;
        }

        $level2->push((object)[
            'sequence' => $requirement->requirement_id,
            'name' => $requirement->requirement->requirement,
            'status' => $requirement->status,
            'lastUpdate' => $last_update,
            'data' => $data,
        ]);

        return collect($level2)->sortBy('sequence')->values();
    }

    public function getLevel1Attribute()
    {
        $requirements = $this->salesRequirements->whereIn('requirement_id', [9, 10]);
        $level1 = new Collection();

        foreach ($requirements as $item) {;
            if ($item->requirement_id == 10) {
                $data = $this->so_number;
                if ($data) {
                    $last_update = Carbon::parse($this->updated_at)->format('Y-m-d H:i');
                } else {
                    $last_update = null;
                }
            } else {
                $data = $item->files;
                if ($data->isNotEmpty()) {
                    $last_update = Carbon::parse($item->latestFile->updated_at)->format('Y-m-d H:i');
                } else {
                    $last_update = null;
                }
            }

            $level1->push((object)[
                'sequence' => $item->requirement_id,
                'name' => $item->requirement->requirement,
                'status' => $item->status,
                'lastUpdate' => $last_update,
                'data' => $data,
            ]);
        }

        return collect($level1)->sortBy('sequence')->values();
    }

    public function getUpgradeLevelAttribute()
    {
        $requirements = Requirement::where('level_id', $this->level)->pluck('id');
        $requirement_done = $this->requirementDone->whereIn('requirement_id', $requirements);

        return ($requirement_done->count() == $requirements->count());
    }

    public function requestClosed()
    {
        return $this->hasOne(SalesRequest::class)
                    ->where('category', SalesRequest::REQUEST_CLOSED_SALES);
    }

    public function requestCancel()
    {
        return $this->hasOne(SalesRequest::class)
                    ->where('category', SalesRequest::REQUEST_CANCEL_SALES);
    }

    public function requestReschedule()
    {
        return $this->hasOne(SalesRequest::class)
                    ->where('category', SalesRequest::REQUEST_RESCHEDULE_SALES);
    }

    public function requestHangar()
    {
        return $this->hasOne(SalesRequest::class)
                    ->where('category', SalesRequest::REQUEST_HANGAR_SLOT);
    }

    public function requestUpgrade()
    {
        return $this->hasOne(SalesRequest::class)
                    ->where('category', SalesRequest::REQUEST_UPGRADE_LEVEL);
    }

    public function requests()
    {
        return $this->hasMany(SalesRequest::class);
    }

    public function requirementDone()
    {
        return $this->hasMany(SalesRequirement::class)->where('status', 1);
    }

    public function salesReschedule()
    {
        return $this->hasOne(SalesReschedule::class);
    }

    public function salesRequirements()
    {
        return $this->hasMany(SalesRequirement::class);
    }

    public function salesUpdates()
    {
        return $this->hasMany(SalesUpdate::class);
    }

    public function salesReject()
    {
        return $this->hasOne(SalesReject::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function prospect()
    {
        return $this->belongsTo(Prospect::class, 'prospect_id');
    }

    public function maintenance()
    {
        return $this->hasMany(SalesMaintenance::class);
    }

    public function salesHistories()
    {
        return $this->hasMany(SalesHistory::class);
    }

    public function hangar()
    {
        return $this->belongsTo(Hangar::class, 'hangar_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function acType()
    {
        return $this->belongsTo(AircraftType::class, 'ac_type_id');
    }

    public function component()
    {
        return $this->belongsTo(Component::class, 'component_id');
    }

    public function engine()
    {
        return $this->belongsTo(Engine::class, 'engine_id');
    }

    public function igte()
    {
        return $this->belongsTo(IGTE::class, 'igte_id');
    }

    public function learning()
    {
        return $this->belongsTo(Learning::class, 'learning_id');
    }

    public function apu()
    {
        return $this->belongsTo(Apu::class, 'apu_id');
    }

    public function salesLevel()
    {
        return $this->hasOne(SalesLevel::class);
    }

    public function ams()
    {
        return $this->belongsTo(AMS::class, 'ams_id');
    }

    public function line()
    {
        return $this->belongsTo(Line::class, 'line_id');
    }

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id');
    }
}
