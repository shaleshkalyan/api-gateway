<?php

namespace Core\DataContracts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrlAssociation extends Model
{
    use HasFactory;

    protected $table = 'url_associations';

    protected $fillable = [
        'tenant_id',
        'short_code',
        'original_url',
        'is_active',
    ];
}