<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

class CalcUpload extends Model
{
    /** @var string */
    protected $table = 'wi_calc_uploads';

    /** @var bool */
    public $timestamps = true;

    /** @var array<int, string> */
    protected $fillable = [
        'filename',
        'original_name',
        'rows_imported',
        'cars_affected',
        'status',
        'error_message',
        'uploaded_by',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'rows_imported'  => 'integer',
        'cars_affected'  => 'integer',
        'uploaded_by'    => 'integer',
    ];
}
