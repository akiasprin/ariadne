## Ariadne

一只P2P交易系统(Laravel and Vue)

## API

### 获取验证码:

Req.:
  
  POST api/user/sendcode

  |字段|类型|必须|含义|
  |---|---|---|---|
  |name|string|True|用户店铺名|
  |phone|string|True|注册手机|

Resp.:

  |字段|类型|含义|
  |---|---|---|
  |msg|string|结果信息|
  |code|int|结果代码|

### 注册:

Req.:

  POST api/user/register

  |字段|类型|必须|含义|
  |---|---|---|---|
  |name|string|True|用户店铺名|
  |phone|string|True|注册手机|
  |code|string|True|验证码|
  |password|string|True|用户密码|

 Resp.:

  |字段|类型|含义|
  |---|---|---|
  |access_token|string|Access Token|
  |expires_in|int|过期时间|
  |id|string|用户ID|
  |name|string|用户店铺名|
  |phone|string|用户密码|
  |created_at|string|注册时间|
  |updated_at|string|更新时间|
