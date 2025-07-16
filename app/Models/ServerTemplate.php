<?php

namespace Pterodactyl\Models;

use Illuminate\Database\Eloquent\Model;

class ServerTemplate extends Model
{
    protected $table = 'server_templates';

    protected $fillable = [
        'name',
        'description',
        'egg_id',
        'nest_id',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
    ];
}
