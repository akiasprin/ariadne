## Ariadne

一只P2P交易系统(Laravel and Vue)

**需要认证**:

```
  headers: { "Authorization": "Bearer " + access_token }
```

## 用户管理API

### 获取验证码:

#### Req.:
  
  ```POST``` api/user/sendcode

  |字段|类型|必须|含义|
  |---|---|---|---|
  |name|string|√|用户店铺名|
  |phone|string|√|注册手机|

***

### 注册:

#### Req.:

  ```POST``` api/user/register

  |字段|类型|必须|含义|
  |---|---|---|---|
  |name|string|√|用户店铺名|
  |phone|string|√|注册手机|
  |code|string|√|验证码|
  |password|string|√|用户密码|

#### Resp.:

  |字段|类型|含义|
  |---|---|---|
  |access_token|string|Access Token|
  |expires_in|int|过期时间|
  |id|string|用户ID|
  |name|string|用户店铺名|
  |phone|string|用户密码|
  |created_at|string|注册时间|
  |updated_at|string|更新时间|

***

### 登录:

#### Req.:

  ```POST``` api/user/register

  |字段|类型|必须|含义|
  |---|---|---|---|
  |phone|string|√|注册手机|
  |password|string|√|用户密码|

#### Resp.:
  同注册.

***

### 注销:

#### Req.(**需要认证**):

  ```POST``` api/user/logout

