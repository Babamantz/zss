<?php

namespace App\Models;

use App\Traits\FilterByTenant;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    //
    use FilterByTenant;
    protected $fillable = [
        'name',
        'document_path',
    ];
}
