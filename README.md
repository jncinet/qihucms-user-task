<h1 align="center">会员任务</h1>

## 说明
任务主发布任务，其它会员完成任务，并提交任务凭证，任务主审核通过后，会员获取任务奖励

## 安装
```shell
$ composer require jncinet/qihucms-user-task
```

## 路由
### admin
在后台管理菜单中添加管理菜单
- 任务管理：task/tasks
- 完成记录：task/orders

### api
#### 会员任务
- 简单任务列表分页
    - 请求方法：GET
    - 链接地址：task/select-tasks
    - 请求参数：
        - 关键词 | q
- 任务分页列表
    - 请求方法：GET
    - 链接地址：task/tasks
    - 请求参数：
        - 分页显示条数 | limit | int | 可选
        - 会员ID | user_id | int | 可选
- 我的任务分页列表
    - 请求方法：GET
    - 链接地址：task/tasks/user
    - 请求参数：
        - 分页显示条数 | limit | int | 可选
        - 任务状态 | status | int | 可选
        - 奖励托管状态 | pay_status | 可选
- 任务详细
    - 请求方法：GET
    - 链接地址：task/tasks/{id}
- 我的任务详细（返回任务说细及任务完成记录）
    - 请求方法：GET
    - 链接地址：task/tasks/user/{id}
- 发布任务
    - 请求方法：POST
    - 链接地址：task/tasks
    - 请求参数：
        - 任务标题 | title | string
        - 缩略图 | thumbnail | string
        - 开始时间 | start_time | datetime | 默认为发布时间
        - 结束时间 | end_time | datetime | 默认发布时间后三天
        - 任务总数 | stock | int
        - 奖励类型 | currency_type_id | int
        - 奖励数额 | amount | decimal
        - 详细介绍 | content | longtext
        - 链接按钮文字 | btn_text | string|null
        - 任务链接 | link | string|null
- 提前结束任务或延长时间
    - 请求方法：PUT | PATCH
    - 链接地址：task/tasks/{id}
    - 请求参数：
        - 结束时间 | end_time | datetime | 可选，当end_time存在则必须是一个大于当前时间的时间
        - 任务状态 | status=2 | 可选，当status参数存在时，即结束任务其它值无效
- 删除任务（有完成记录的任务不可删除）
    - 请求方法：DELETE
    - 链接地址：task/tasks/{id}
#### 任务完成记录
- 任务订单记录（任务发布者可查看）
    - 请求方法：GET
    - 链接地址：task/orders
    - 请求参数：
        - 显示条数 | limit | 可选 | 默认每页15条
        - 任务状态 | status | 可选值`[0=>'待审核', 1=>'已审核', 2=>'已完成']` | 可选
- 任务订单详细
    - 请求方法：GET
    - 链接地址：task/orders/{id=任务ID}
- 领取任务
    - 请求方法：POST
    - 链接地址：task/orders
    - 请求参数：
        - 任务ID | user_task_id | 必须是有效的任务ID
- 完成任务提交凭证
    - 请求方法：PUT|PATCH
    - 链接地址：task/orders/{id=任务ID}
    - 请求参数：  
        - 任务ID | user_task_id | int | 必填
        - 根据任务要求完的任务的图片记录 | files | array | 可选
        - 根据任务要求填写的说明 | remark | string ｜ 可选
- 任务主审核完成记录
    - 请求方法：POST
    - 链接地址：task/orders/audit/{id=任务ID}
- 会员删除任务记录（只能删除未完成的记录）
    - 请求方法：DELETE
    - 链接地址：task/orders/{id=任务ID}

> task前缀可通过在/config/qihu.php中添加或修改：  
 `'user_task_prefix' => 'task'` 

## 事件

## 通知

## 数据库
| 左对齐 | 右对齐 | 居中对齐 |
| :-----| ----: | :----: |
| 单元格 | 单元格 | 单元格 |
| 单元格 | 单元格 | 单元格 |