<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * タスクコントローラー
 */
class TodoController extends Controller
{
    /**
     * タスクの一覧を返します。
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->query('top')) {
            // より優先度の高いタスク上位に絞り込んで返す
            $todos = Todo::where('complete', '!=', true)
                ->oldest('limit')
                ->orderBy('hours', config('todo.sortHoursDesc') ? 'desc' : 'asc')
                ->get();

            $thresholds = config('todo.priorityThresholds');
            $riskParameter = config('todo.riskParameter');
            $capacity = config('todo.capacity');
            $sum = 0;

            $todos = $todos
                // 優先度スコアを付与
                ->map(function ($todo) {
                    $todo->score = $this->calcPriority($todo);
                    return $todo;
                })

                // 優先度スコアでソート
                ->sort(function ($a, $b) {
                    return $a->score - $b->score;
                })
                ->reverse()

                // 優先度レベル別に仕分ける
                ->map(function ($todo) use ($thresholds) {
                    foreach ($thresholds as $index => $threshold) {
                        if ($todo->score < $threshold) {
                            $todo->priority = $index + 1;
                            break;
                        }
                    }
                    if (!isset($todo->priority)) {
                        $todo->priority = count($thresholds) + 1;
                    }
                    return $todo;
                });

            // 1日のキャパシティ内に収める
            $filtered = $todos->takewhile(function ($todo) use (&$sum, $riskParameter, $capacity) {
                $withinCapacity = $sum + $todo->hours * $riskParameter <= $capacity;
                if ($withinCapacity) {
                    $sum += $todo->hours * $riskParameter;
                }
                return $withinCapacity;
            });

            // 最優先のタスクがキャパシティを超えてしまった場合は救出
            if ($filtered->count() === 0) {
                $filtered = $todos->take(1);
            }

            // キャパシティにまだ余裕がある場合に低優先度のタスクで埋め合わせる
            $countBaseline = config('todo.countBaseline');
            $todos->each(function ($next) use ($countBaseline, $filtered, $todos, &$sum, $riskParameter, $capacity) {
                if ($filtered->count() >= $countBaseline || $filtered->count() === $todos->count()) {
                    return false;
                }

                // 既に追加済みの項目はスキップ
                if ($filtered->contains(function ($t) use ($next) {
                    return $next->id === $t->id;
                })) {
                    return true;
                }

                // 潜在的なリスクを加味した所要時間
                $nextHoursWithRisk = $next->hours * $riskParameter;

                // キャパシティ内に収まれば追加
                if ($sum + $nextHoursWithRisk <= $capacity) {
                    $sum += $nextHoursWithRisk;
                    $filtered->concat([$next]);
                }
            });

            return $filtered;
        }

        // 未完了のタスクを返す
        if ($request->query('incomplete')) {
            return Todo::where('complete', '!=', true)->get();
        }

        // デフォルトで全件返す
        return Todo::all();
    }

    /**
     * タスクを追加します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $todo = new Todo();
        $todo->fill($request->all())->save();
    }

    /**
     * タスクを更新します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);
        $todo->fill($request->all())->save();
    }

    /**
     * タスクを削除します。
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Todo::destroy($id);
    }

    /**
     * タスクの優先度を計算します。
     *
     * @param Todo $todo
     * @return int
     */
    private function calcPriority($todo)
    {
        // 潜在的なリスクを加味した所要時間
        $riskParameter = config('todo.riskParameter');
        $hoursWithRisk = $todo->hours * $riskParameter;

        // 期限日時から逆算した残り時間を日あたりの活動可能時間のスケールに変換
        $now = now();
        $idleHoursOfDay = config('todo.idleHoursOfDay');
        $limitDate = Carbon::instance($todo->limitDate);
        $remainingHours = $limitDate->diffInHours($now);
        $remainingHours -= (24 - $idleHoursOfDay) * $limitDate->diffInDays($now);
        if ($limitDate < $now) {
            $remainingHours = -$remainingHours;
        }

        // 残り時間を正負反転したものをスコアとする
        $score = -$remainingHours;
        info([$todo->name, $hoursWithRisk, $remainingHours, $score]);

        return $score;
    }
}
