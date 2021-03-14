<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use MongoDB\BSON\UTCDateTime;

/**
 * タスク
 */
class Todo extends Eloquent
{
    protected $collection = 'todos';

    /**
     * [使用時注意] すべての属性を一括代入できるようにします。
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return string
     */
    public function getLimitAttribute()
    {
        return $this->attributes['limit']->toDateTime()->format('Y-m-d\TH:i');
    }

    /**
     * @param string $value
     * @return void
     */
    public function setLimitAttribute($value)
    {
        $this->attributes['limit'] = new UTCDateTime(date_create_from_format('Y-m-d\TH:i:00', $value));
    }
}
