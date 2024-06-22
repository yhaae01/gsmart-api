<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'group_type',
        'country_id',
    ];

    protected $appends = [
        'status',
        'group',
        'label',
    ];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const GROUPTYPE_GA = 0;
    const GROUPTYPE_NGA = 1;

    const STATUS_ARRAY = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    const GROUP_ARRAY = [
        self::GROUPTYPE_GA => 'GA',
        self::GROUPTYPE_NGA => 'NGA',
    ];

    public function getLabelAttribute()
    {
        return "{$this->code} - {$this->name}";
    }

    public function scopeOnlyProspect($query)
    {
        $query->whereHas('amsCustomers', function ($query) {
            $query->whereHas('prospects');
        });
    }

    public function scopeSearchProspect($query, $search)
    {
        $query->when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', "%$search%")
                ->orWhere('code', 'LIKE', "%$search%")
                ->orWhereHas('amsCustomers', function ($query) use ($search) {
                    $query->whereRelation('ams', 'initial', 'LIKE', "%$search%")
                        ->orWhereHas('prospects', function ($query) use ($search) {
                            $query->where('year', 'LIKE', "%$search%")
                                ->orWhereRelation('transactionType', 'name', 'LIKE', "%$search%")
                                ->orWhereRelation('prospectType', 'name', 'LIKE', "%$search%")
                                ->orWhereRelation('strategicInitiative', 'name', 'LIKE', "%$search%")
                                ->orWhereRelation('pm', 'name', 'LIKE', "%$search%")
                                ->orWhereRelation('prospectType', 'name', 'LIKE', "%$search%");
                        });
                });
        });
    }

    public function scopeFilterProspect($query, $filter)
    {
        $query->when($filter, function ($query) use ($filter) {
            $query->whereHas('amsCustomers', function ($query) use ($filter) {
                $query->whereHas('prospects', function ($query) use ($filter) {
                    $query->where('year', $filter);
                });
            });
        });
    }

    public function scopeUserProspect($query, $user)
    {
        $query->when($user->hasRole('AMS'), function ($query) use ($user) {
            $query->whereHas('amsCustomers', function ($query) use ($user) {
                $query->where('ams_id', $user->ams->id);
            });
        });
    }

    public function scopeSortProspect($query, $order, $by)
    {
        $p_sorts = [
            'year',
            'transaction',
            'type',
            'strategicInitiative',
            'projectManager',
            'marketShare',
            'salesPlan'
        ];

        $query->when(($order && $by), function ($query) use ($p_sorts, $order, $by) {
            if (in_array($order, $p_sorts)) {
                $query->whereHas('amsCustomers', function ($query) use ($by) {
                    $query->whereHas('prospects', function ($query) use ($by) {
                        $query->withAggregate('transactionType', 'name')
                            ->withAggregate('prospectType', 'name')
                            ->withAggregate('strategicInitiative', 'name')
                            ->withAggregate('pm', 'name')
                            ->withSum('sales', 'value')
                            ->orderBy('transaction_type_name', $by)
                            ->orderBy('prospect_type_name', $by)
                            ->orderBy('strategic_initiative_name', $by)
                            ->orderBy('pm_name', $by)
                            ->orderBy('sales_sum_value', $by);
                    });
                });
            } else if ($order == 'ams') {
                $query->whereHas('amsCustomers', function ($query) use ($by) {
                    $query->whereHas('ams', function ($query) use ($by) {
                        $query->orderBy('initial', $by);
                    });
                });
            } else if ($order == 'customer.name') {
                $query->orderBy('name', $by);
            } else if ($order == 'customer.code') {
                $query->orderBy('code', $by);
            } else {
                $query->orderBy($order, $by);
            }
        });
    }


    public function getStatusAttribute()
    {
        return self::STATUS_ARRAY[$this->is_active];
    }

    public function getGroupAttribute()
    {
        return self::GROUP_ARRAY[$this->group_type];
    }

    public function scopeSearch($query, $search)
    {
        $query->when($search, function ($query) use ($search) {
            if (strtolower($search) == 'active') {
                $query->where('is_active', 1);
            } else if (strtolower($search) == 'inactive') {
                $query->where('is_active', 0);
            } else if (($search) == 'GA') {
                $query->where('group_type', 0);
            } else if (($search) == 'NGA') {
                $query->where('group_type', 1);
            } else {
                $query->where('code', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhereHas('country', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%")
                            ->orWhereRelation('region', 'name', 'LIKE', "%{$search}%");
                    });
            }
        });
    }

    public function latestCP()
    {
        return $this->hasOne(ContactPerson::class)->latest('updated_at');
    }

    public function country()
    {
        return $this->belongsTo(Countries::class, 'country_id');
    }

    public function amsCustomers()
    {
        return $this->hasMany(AMSCustomer::class, 'customer_id', 'id');
    }

    public function contactPersons()
    {
        return $this->hasMany(ContactPerson::class);
    }

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }

    public function customers()
    {
        return $this->hasMany(CustomerSwift::class, 'customer_group_id', 'id');
    }
}
