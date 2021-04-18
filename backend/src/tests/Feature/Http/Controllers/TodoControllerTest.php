<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Http\Controllers\TodoController;
use App\Models\Todo;

/**
 * タスクコントローラーテスト
 * ＊実際の接続先DBに読み書きを行います。
 */
class TodoControllerTest extends TestCase
{
    /**
     * 事前にテストデータを投入
     */
    protected function setUp(): void
    {
        parent::setUp();

        Todo::truncate();

        $records = [
            [
                'name' => 'タスクA',
                'hours' => 3,
                'limit' => '2021-04-01T10:20:00',
            ],
            [
                'name' => 'タスクB',
                'hours' => 12,
                'limit' => '2021-04-02T10:20:00',
            ],
            [
                'name' => 'タスクC',
                'hours' => 3,
                'limit' => '2021-04-01T10:20:00',
                'complete' => true,
            ],
        ];

        foreach ($records as $record) {
            $todo = Todo::factory()->make();
            $todo->fill($record);
            $todo->save();
        }
    }

    /**
     * タスク全件取得
     */
    public function test_index_all()
    {
        $this->get(action([TodoController::class, 'index']))
            ->assertOk()
            ->assertJsonCount(3);
    }

    /**
     * 未完了タスク取得
     *
     * @depends test_index_all
     */
    public function test_index_incomplete()
    {
        $this->get(action([TodoController::class, 'index'], ['incomplete' => true]))
            ->assertOk()
            ->assertJsonCount(2);
    }

    /**
     * 優先タスク取得
     *
     * @depends test_index_incomplete
     */
    public function test_index_top()
    {
        $this->get(action([TodoController::class, 'index'], ['top' => true]))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJson([['name' => 'タスクA']]);
    }

    /**
     * タスク登録
     *
     * @depends test_index_top
     */
    public function test_store()
    {
        $this->post(action([TodoController::class, 'store']), [
            'name' => 'タスクZ',
            'hours' => 5,
            'limit' => '2021-04-10T00:00:00',
        ])->assertOk();

        $todos = Todo::all();
        $this->assertTrue($todos->contains(fn ($v) => $v->name === 'タスクZ'));
    }

    /**
     * タスク編集
     *
     * @depends test_store
     */
    public function test_update()
    {
        $todo = Todo::where(['name' => 'タスクA'])->first();

        $this->put(action([TodoController::class, 'update'], $todo->id), [
            'name' => 'タスクZ',
        ])->assertOk();

        $todos = Todo::all();
        $this->assertFalse($todos->contains(fn ($v) => $v->name === 'タスクA'));
        $this->assertTrue($todos->contains(fn ($v) => $v->name === 'タスクZ'));
    }

    /**
     * タスク削除
     *
     * @depends test_update
     */
    public function test_destroy()
    {
        $todo = Todo::where(['name' => 'タスクA'])->first();

        $this->delete(action([TodoController::class, 'destroy'], $todo->id))
            ->assertOk();

        $todos = Todo::all();
        $this->assertFalse($todos->contains(fn ($v) => $v->name === 'タスクA'));
    }
}
