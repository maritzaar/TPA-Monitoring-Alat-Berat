<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    protected $table = 'import_logs';

    protected $fillable = [
        'filename',
        'sumber',
        'rows_count',
    ];
}
