# Core
Core

框架核心组件代码,目前处于调试中，尽可能地update最新版本代码，有任何问题请及时联系或者随时欢迎提交merge request,欢迎大家提供patch,谢谢

更新LOG

2015-07-23 RDB添加查询表字段的方法getColumns

2015-07-22 修复重要的Bug，当有语法错误的时候能捕获到错误日志并记录到各种日志里去

2015-07-17 RDB where 方法bug，array_merge会把-1给清空

2015-06-27 Api返回的时候直接输出错误码

2015-06-26 Api 接口success和error后程序直接die掉

2015-06-25 RDB添加时允许加入参数，是否要进行自动填充和自动验证

2015-06-24 NoSQL->Redis 修复register的bug,将执行时间修改为ms

2015-06-12 更新

2015-06-06 修复RDB里的BUG

2015-05-28 修复RDB里where是字符串的BUG

2015-05-25 修复Session存数据库的BUG

2015-05-17 修复循环update和delete没有清空条件的bug

2015-05-16 http加入isset方法,batchSave加入是否使用事务,Arr加入方法

2015-05-13 update不同场景设置不同的validator

2015-05-11 api默认不采用全小写的模式

2015-05-10 select默认没有条件为true

2015-05-08 修复setvalidator自动验证的bug

2015-04-19 修复httpcall POST请求时参数，可以支持数组或者字符串

2015-04-02 update方法里如果sql没有问题则返回影响行数，否则为false,自动填充也加入MODEL_INSERT和MODEL_UPDATE参数

2015-04-01 Api层方法修复，RDB里find方法修复，返回永远是对象（处于兼容考虑）,RDB层当where条件为空时返回0(为了安全考虑)

2015-03-30 修复多模型自动验证的BUG,自动验证会出现Model层数据混乱

2015-03-29 模型自动验证改为方法

2015-03-19 Model层添加自动验证和自动填充,和自动映射

2015-03-18 修复使用[]定义数组，PHP5.3不支持

2015-03-16 强化RDB功能，添加setInc和setDec功能

2015-02-08 加入Lock组件功能,默认使用Cache模式、Redis锁

2015-02-07 强化validator组件功能，加入ip,url等validator

2015-02-04 display模板时控制，display()将自动智能加载模板, display(''|null)将不加载模板

2015-02-02 修复HttpCall回调函数的BUG(多次调用，前面的回调将影响后期http回调),将Event回调放到App里

2015-01-30 Context Register 控制器(Controller)和方法(Action),修改Component/Util目录位置

2015-01-28 添加Validator{number, regex, mobile, email,express}组件功能, 添加Str Helper类,日志形式添加null无动作形式

2015-01-27 添加Validator{boolean, int, string}组件功能

2015-01-26 将Route功能开放到APP,自定义Route功能

更新版本

version: 1.1.5     2015-01-28   修复BUG + Validator功能

version: 1.1.4     2015-01-25

version: 1.1.3     2015-01-23

加入api功能


1.1.2

1.1.1

1.1

1.0
