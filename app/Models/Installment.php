<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Installment extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'amount',
        'due_date',
        'is_paid',
        'paid_date',
        'plan_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'datetime',
        'is_paid' => 'boolean',
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

    public function plan()
    {
        return $this->belongsTo(InstallmentPlan::class, 'plan_id');
    }
}
