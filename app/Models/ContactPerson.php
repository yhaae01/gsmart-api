<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContactPerson extends Model
{
    use HasFactory;

    protected $table = 'contact_persons';
    protected $guarded = ['id'];

    protected $appends = [
        'firstalphabet',
    ];

    public function getFirstAlphabetAttribute()
    {
        $firstalphabet = Str::of($this->name)->substr(0, 1);
        return $firstalphabet;
    }

    public function scopeByCustomer($query, $customer)
    {
        $query->when($customer, function ($query) use ($customer) {
            $query->where('customer_id', $customer);
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
