<?php

return [
    'id' => 'ID',
    'user_id' => '发布人',
    'title' => '任务名称',
    'thumbnail' => '缩略图',
    'start_time' => '开始时间',
    'end_time' => '结束时间',
    'stock' => '任务总数',
    'currency_type_id' => '奖励类型',
    'amount' => '奖励数额',
    'content' => '详细介绍',
    'btn_text' => '任务链文字',
    'link' => '任务链接',
    'pay_status' => [
        'label' => '支付状态',
        'value' => ['奖金待托管', '奖金已托管']
    ],
    'status' => [
        'label' => '任务状态',
        'value' => ['待审核', '已审核', '已完成']
    ]
];