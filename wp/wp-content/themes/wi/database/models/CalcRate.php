<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

class CalcRate extends Model
{
    /** @var string */
    protected $table = 'wi_calc_rates';

    /** @var bool */
    public $timestamps = true;

    /** @var array<int, string> */
    protected $fillable = [
        'car_id',
        'idv',
        'month',
        'km',
        'percent',
        'fee',
        'rate',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'month'   => 'integer',
        'km'      => 'integer',
        'percent' => 'integer',
        'fee'     => 'integer',
        'rate'    => 'integer',
    ];
}
