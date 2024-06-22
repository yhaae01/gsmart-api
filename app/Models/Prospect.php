<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Sales;
use App\Models\AMSCustomer;
use App\Models\ProspectType;
use App\Models\TransactionType;
use App\Models\StrategicInitiatives;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Prospect extends Model
{
    use HasFactory;
    protected $fillable = [
        'year',
        'transaction_type_id',
        'prospect_type_id',
        'strategic_initiative_id',
        'pm_id',
        'ams_customer_id',
    ];

    protected $hidden = [
        'sales',
        'amsCustomer',
    ];

    protected $appends = [
        'transaction',
        'type',
        'strategic_init',
        'project_manager',
        'customer',
        'ams',
        'market_share',
        'sales_plan',
        'product',
        'registration'
    ];

    public function scopeSort($query, $order, $by)
    {
        $query->when(($order && $by), function ($query) use ($order, $by) {
            if ($order == 'year') {
                $query->orderBy('year', $by);
            } else if ($order == 'transaction_type.name') {
                $query->withAggregate('transactionType', 'name')
                    ->orderBy('transaction_type_name', $by);
            } else if ($order == 'prospect_type.name') {
                $query->withAggregate('prospectType', 'name')
                    ->orderBy('prospect_type_name', $by);
            } else if ($order == 'strategic_initiative.name') {
                $query->withAggregate('strategicInitiative', 'name')
                    ->orderBy('strategic_initiative_name', $by);
            } else if ($order == 'pm.name') {
                $query->withAggregate('pm', 'name')
                    ->orderBy('pm_name', $by);
            } else if ($order == 'customer.name') {
                $query->whereHas('amsCustomer', function ($query) use ($by) {
                    $query->withAggregate('customer', 'name')
                        ->orderBy('customer_name', $by);
                });
            } else if ($order == 'customer.code') {
                $query->whereHas('amsCustomer', function ($query) use ($by) {
                    $query->withAggregate('customer', 'code')
                        ->orderBy('customer_code', $by);
                });
            } else if ($order == 'ams.initial') {
                $query->whereHas('amsCustomer', function ($query) use ($by) {
                    $query->withAggregate('ams', 'initial')
                        ->orderBy('ams_initial', $by);
                });
            } else {
                $query->orderBy('id', 'desc');
            }
        });
    }

    public function scopeSearch($query, $search)
    {
        $query->when($search, function ($query) use ($search) {
            $query->where('year', 'LIKE', "%$search%")
                ->orWhereRelation('transactionType', 'name', 'LIKE', "%$search%")
                ->orWhereRelation('prospectType', 'name', 'LIKE', "%$search%")
                ->orWhereRelation('strategicInitiative', 'name', 'LIKE', "%$search%")
                ->orWhereRelation('pm', 'name', 'LIKE', "%$search%")
                ->orWhereRelation('prospectType', 'name', 'LIKE', "%$search%")
                ->orWhereRelation('tmb', 'market_share', 'LIKE', "%$search%")
                ->orWhereRelation('pbth', 'market_share', 'LIKE', "%$search%")
                ->orWhereRelation('sales', 'value', 'LIKE', "%$search%")
                ->orWhereHas('amsCustomer', function ($query) use ($search) {
                    $query->whereRelation('customer', 'name', 'LIKE', "%$search%")
                        ->orWhereRelation('customer', 'code', 'LIKE', "%$search%")
                        ->orWhereRelation('ams', 'initial', 'LIKE', "%$search%");
                });
        });
    }

    public function scopeFilter($query, $filter)
    {
        $query->when($filter, function ($query) use ($filter) {
            $query->where('year', $filter);
        });
    }

    public function scopeUser($query, $user)
    {
        $query->when($user->hasRole('AMS'), function ($query) use ($user) {   
            $query->whereHas('amsCustomer', function ($query) use ($user) {
                $query->where('ams_id', $user->ams->id);
            });
        });
    }

    public function getRegistrationAttribute()
    {
        if ($this->tmb) {
            $tmb = $this->tmb;

            $ac_type = $tmb->acType->name ?? null;
            $engine = $tmb->engine->name ?? null;
            $apu = $tmb->apu->name ?? null;
            $component = $tmb->component->name ?? null;
            $igte = $tmb->igte->name ?? null;
            $learning = $tmb->learning->name ?? null;

            $regs = [$ac_type, $igte, $engine, $apu, $component, $learning];
            $regs = current(array_filter($regs));

            $registration = !empty($regs) ? $regs : '-';
        } else if ($this->pbth) {
            $registration = $this->pbth->first()?->acType->name ?? '-';
        }

        return $registration;
    }

    public function getProductAttribute()
    {
        return $this->tmb?->product->name ?? ($this->pbth->first()?->product->name ?? '-');
    }

    public function getAmsAttribute()
    {
        return $this->amsCustomer->ams->initial;
    }

    public function getCustomerAttribute()
    {
        return $this->amsCustomer->customer->only('id', 'code', 'name');
    }

    public function getTransactionAttribute()
    {
        return $this->transactionType->name;
    }

    public function getTypeAttribute()
    {
        return $this->prospectType->name;
    }

    public function getStrategicInitAttribute()
    {
        return $this->strategicInitiative->name ?? null;
    }

    public function getProjectManagerAttribute()
    {
        return $this->pm->name ?? null;
    }

    public function getMarketShareAttribute()
    {
        if ($this->tmb) {
            return $this->tmb->market_share;
        } else if ($this->pbth) {
            return $this->pbth->sum('market_share');
        } else {
            return null;
        }
    }

    public function getSalesPlanAttribute()
    {
        return $this->sales->sum('value') ?? null;
    }

    public function scopeMarketShareByCustomer($query, $id, $user)
    {
        $data = $query->whereHas('amsCustomer', function ($query) use ($id) {
                    $query->where('customer_id', $id);
                })->when($user->hasRole('AMS'), function ($query) use ($user) {
                    $query->whereHas('amsCustomer', function ($query) use ($user) {
                        $query->whereRelation('ams', 'user_id', '=', $user->id);
                    });
                })->get();

        return $data->sum('market_share');
    }

    public function scopeMarketShareThisYear($query)
    {
        $data = $query->where('year', Carbon::now()->format('Y'))->get();

        return $data->sum('market_share');
    }

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id');
    }

    public function prospectType()
    {
        return $this->belongsTo(ProspectType::class, 'prospect_type_id');
    }

    public function strategicInitiative()
    {
        return $this->belongsTo(StrategicInitiatives::class, 'strategic_initiative_id');
    }

    public function pm()
    {
        return $this->belongsTo(User::class, 'pm_id');
    }

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }

    public function amsCustomer()
    {
        return $this->belongsTo(AMSCustomer::class, 'ams_customer_id');
    }

    public function tmb()
    {
        return $this->hasOne(TMB::class);
    }

    public function pbth()
    {
        return $this->hasMany(PBTH::class);
    }

    public function firstPbth()
    {
        return $this->hasOne(PBTH::class);
    }
}
