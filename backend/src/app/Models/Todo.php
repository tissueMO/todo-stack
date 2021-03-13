<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

/**
 * タスク
 */
class Todo extends Eloquent
{
    protected $collection = 'todos';

    protected $fillable = [
        'name',
        'limit',
        'hours',
        'priority',
    ];
}
