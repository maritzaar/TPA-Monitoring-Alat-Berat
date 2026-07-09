<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterAset extends Model
{
    protected $table = 'master_asets';

    protected $fillable = [
        'unit_code',
        'nomor_seri',
        'model',
        'group_aset',
        'area',
        'internal_order',
        'group_internal_order',
        'pt',
        'company_code'
    ];
}