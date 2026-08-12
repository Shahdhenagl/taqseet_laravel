<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PostponementRequest extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_id',
        'installment_id',
        'requested_due_date',
        'reason',
        'status',
        'extra_interest',
    ];

    protected $casts = [
        'requested_due_date' => 'date',
        'extra_interest' => 'double',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }
}
