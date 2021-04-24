<?php

namespace Tests\Unit\Models;

use App\Models\Todo;
use Tests\TestCase;
use MongoDB\BSON\UTCDateTime;

/**
 * タスクテスト
 */
class TodoTest extends TestCase
{
    /**
     * limit プロパティ
     */
    public function test_getLimitAttribute()
    {
        $limit = '2021-04-01T10:20';

        $todo = Todo::factory()->make();
        $todo->limit = $limit . ':00';

        $this->assertEquals($todo->limit, $limit);
    }

    /**
     * limitDate プロパティ
     */
    public function test_getLimitDateAttribute()
    {
        $limit = '2021-04-01T10:20';

        $todo = Todo::factory()->make();
        $todo->limit = $limit . ':00';

        $this->assertEquals($todo->limitDate->format('Y-m-d\TH:i'), $limit);
    }
}
