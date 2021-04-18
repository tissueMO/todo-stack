<?php

return [
    // 日あたりの活動できる時間 (時間単位)
    'idleHoursOfDay' => 10,

    // 日あたりのキャパシティ (時間単位)
    'capacity' => 8,

    // 見積もり上の所要時間に掛ける潜在的リスク係数
    'riskParameter' => 1.3,

    // 同じ優先度における所要時間のソートを逆順にするかどうか
    'sortHoursDesc' => true,

    // スコア閾値
    'priorityThresholds' => [20, 50, 75, 100],

    // 基本表示件数
    'countBaseline' => 4,
];
