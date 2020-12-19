<h1 align="center">会员任务</h1>

## 说明
- 丰富的事件系统
- 命名不那么乱七八糟

## 安装
```shell
$ composer require jncinet/qihucms-user-task
```

## 使用
```php
//使用说明
```

## 路由
### wap
- 无

### admin
- 任务管理：task/tasks
- 完成记录：task/orders

### api
- task/select-tasks
- task/tasks
- task/tasks/user
- task/tasks/user/{id}
- 任务订单记录（任务发布者可查看）
    - 请求方法：GET
    - 链接地址：task/orders
    - 请求参数：
        - 显示条数 | limit | 可选 | 默认每页15条
        - 任务状态 | status | `[0=>'待审核', 1=>'已审核', 2=>'已完成']` | 可选
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

> {id}是变量参数  
task前缀可通过在/config/qihu.php中设置user_task_prefix修改


## 事件

## 通知

## 数据库
| 左对齐 | 右对齐 | 居中对齐 |
| :-----| ----: | :----: |
| 单元格 | 单元格 | 单元格 |
| 单元格 | 单元格 | 单元格 |