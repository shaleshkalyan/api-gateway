<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UrlMapping extends Model
{
    use SoftDeletes;

    protected $table = 'url_mapping';

    protected $fillable = [
        'tenant_id',
        'original_url',
        'short_url',
        'short_code',
        'is_active',
        'created_by',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
