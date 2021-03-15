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
        $date = $this->attributes['limit']->toDateTime();
        date_timezone_set($date, timezone_open('Asia/Tokyo'));
        return $date->format('Y-m-d\TH:i');
    }

    /**
     * @return DateTime
     */
    public function getLimitDateAttribute()
    {
        $date = $this->attributes['limit']->toDateTime();
        date_timezone_set($date, timezone_open('Asia/Tokyo'));
        return $date;
    }

    /**
     * @param string $value
     * @return void
     */
    public function setLimitAttribute($value)
    {
        $this->attributes['limit'] = new UTCDateTime(date_create_from_format('Y-m-d\TH:i:00', $value) ?: now());
    }
}
