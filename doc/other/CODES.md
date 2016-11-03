# 代码出现的code码

#### 全局code码
- -400 表单数据有误
    > json.data格式为： {inputName1 : '错误信息', inputName2 : '错误信息'}
- -302 url跳转
    > json.data格式为：{redirectUri :'跳转的地址'}
- -403 禁止访问
    > json.data无数据
- -404 访问地址不存在
    > json.data无数据
- -502 系统出错
    > json.data无格式


#### 发送短信
- 1000 操作成功
- 1001 同一个ip发送太频繁
- 1002 同一个频道发送太频繁
- 1003 短信频道不存在
- 1004 频道检查不通过