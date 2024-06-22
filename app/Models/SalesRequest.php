<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesRequest extends Model
{
    const REQUEST_UPGRADE_LEVEL = 1;
    const REQUEST_HANGAR_SLOT = 2;
    const REQUEST_RESCHEDULE_SALES = 3;
    const REQUEST_CANCEL_SALES = 4;
    const REQUEST_CLOSED_SALES = 5;

    const STATUS_NO_REQUEST = 1;
    const STATUS_REQUESTED = 2;
    const STATUS_APPROVED = 3;
    const STATUS_REJECTED = 4;

    const CATEGORIES = [
        self::REQUEST_UPGRADE_LEVEL => 'Upgrade Level',
        self::REQUEST_HANGAR_SLOT => 'Hangar Slot',
        self::REQUEST_RESCHEDULE_SALES => 'Reschedule Sales',
        self::REQUEST_CANCEL_SALES => 'Cancel Sales',
        self::REQUEST_CLOSED_SALES => 'Closed Sales'
    ];

    const STATUS = [
        self::STATUS_NO_REQUEST => 'No request',
        self::STATUS_REQUESTED => 'Requested, waiting for response',
        self::STATUS_APPROVED => 'Request has been approved',
        self::STATUS_REJECTED => 'Request has been rejected',
    ];

    protected $fillable = [
        'sales_id',
        'reviewer_id',
        'category',
        'status',
        'reject_reason',
        'commit'
    ];

    protected $appends = [
        'request_category',
        'request_status',
        'reviewer_name'
    ];

    public function getReviewerNameAttribute()
    {
        return $this->reviewer->name;
    }

    public function getRequestCategoryAttribute()
    {
        return self::CATEGORIES[$this->category];
    }

    public function getRequestStatusAttribute()
    {
        return self::STATUS[$this->status];
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id', 'id');
    }
}
