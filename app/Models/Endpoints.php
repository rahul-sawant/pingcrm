<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endpoints extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'title', 'location', 'stream_key', 'ip_addr', 'port', 'type'];

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->firstOrFail();
    }

    public function accounts()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function streams()
    {
        return $this->hasMany(Streams::class, 'endpoint_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
        });
    }
}
