# thinkphp6-permission

ThinkPHP6 权限包


### 安装
```
composer require pengxuxu/thinkphp6-permission
```

### 使用
* [创建必要数据](#创建必要数据)
* * [规则](#规则)
* * [角色](#角色)
* * [用户](#用户)
* [分配关系](#分配关系)
* * [规则与角色](#规则与角色)
* * [用户与角色](#用户与角色)
* [解除关系](#解除关系)
* * [规则与角色](#解除规则与角色)
* * [用户与角色](#解除用户与角色)
* [权限判断](#权限判断)
* * [判断用户是否有权限](#判断用户是否有权限)
* * [用户模型](#用户模型)
* * [注入用户信息](#注入用户信息)
* [路由守护](#路由守护)
* [数据表](#数据表)

### 创建必要数据
#### 规则
```php
use pengxuxu\Permission\Model\Permission;
// 创建一条可查看首页的权限 
Permission::create(['name' => 'home']);
```

#### 角色
```php
use pengxuxu\Permission\Model\Role;
// 创建一个名为编辑的角色
Role::create(['name' => 'writer']);
```

#### 用户
```php
use pengxuxu\Permission\Model\User;
// 创建一个名为pengxuxu的用户
User::create(['name' => 'pengxuxu']);
```

### 分配关系
#### 规则与角色
```php

use pengxuxu\Permission\Model\Permission;
use pengxuxu\Permission\Model\Role;
// 将home规则分配到writer角色 
$permission = Permission::findByName('home');
$role = Permission::findByName('writer');
$permission->assignRole($role);

// 将home规则分配到writer角色 (跟上面效果一样)
$permission = Permission::findByName('home');
$role = Permission::findByName('writer');
$role->assignPermission($permission);
```

#### 用户与角色
```php

use pengxuxu\Permission\Model\User;
use pengxuxu\Permission\Model\Role;

// 为用户pengxuxu分配 writer角色 
$user = User::findByName('pengxuxu');
$role = Permission::findByName('writer');
$user->assignRole($role);

// 为用户pengxuxu分配 writer角色 (跟上面效果一样)
$user = User::findByName('pengxuxu');
$role = Permission::findByName('writer');
$role->assignUser($user);

```

### 解除关系
#### 解除规则与角色
```php
use pengxuxu\Permission\Model\Permission;
use pengxuxu\Permission\Model\Role;

// home规则与writer角色 解除关系
$permission = Permission::findByName('home');
$role = Permission::findByName('writer');
$permission->removeRole($role);

// writer角色与home规则 解除关系(跟上面效果一样)
$permission = Permission::findByName('home');
$role = Permission::findByName('writer');
$role->removePermission($permission);
```

#### 解除用户与角色
```php

use pengxuxu\Permission\Model\User;
use pengxuxu\Permission\Model\Role;

// 用户pengxuxu与writer角色 解除关系
$user = User::findByName('pengxuxu');
$role = Permission::findByName('writer');
$user->removeRole($role);

// writer角色与用户pengxuxu 解除关系 (跟上面效果一样)
$user = User::findByName('pengxuxu');
$role = Permission::findByName('writer');
$role->removeUser($user);

```

### 权限判断
#### 判断用户是否有权限
```php
use pengxuxu\Permission\Model\User;

$user = User::findByName('pengxuxu');
if ($user->can('home')) {
    // 有 `home`权限
} else {
    // 无 `home`权限
}
```

#### 用户模型
用户模型使用 `\pengxuxu\Permission\Contract\UserContract` 接口。
```php
<?php

namespace app\model;

use think\Request;
use pengxuxu\Permission\Contract\UserContract;

class User implements UserContract
{
    use \pengxuxu\Permission\Traits\User;
}

```


#### 注入用户信息
新建Auth中间件,在中间件里手动注入用户信息到`$request->user`上。
```php
<?php

namespace app\middleware;

use think\Request;
use app\model\User;

class Auth
{
    public function handle($request, \Closure $next)
    {
        $uid = 1;
        $user = User::find($uid);

        $request->user = $user;
        return $next($request);
    }
}

```

### 路由守护
#### 在中间件配置里注册中间件，并定义中间件别名
```php
<?php
// 中间件配置
return [
    // 别名或分组
    'alias' => [
        'auth' => \app\middleware\Auth::class,
        'permission' => \pengxuxu\Permission\Middleware\Permission::class,
        'role' => \pengxuxu\Permission\Middleware\Role::class
    ],

    // 优先级设置，此数组中的中间件会按照数组中的顺序优先执行
    'priority' => [
        \think\middleware\SessionInit::class,
        \app\middleware\Auth::class,
        \pengxuxu\Permission\Middleware\Permission::class,
        \pengxuxu\Permission\Middleware\Role::class
    ],
];

```
#### 路由使用中间件
- 规则中间件

`/index`路由添加一条权限控制，访问者有`home`权限才能允许访问
```php
Route::post('/index', 'index/index')->middleware('permission', 'home');
```

- 角色中间件

`/home`路由添加一条权限控制，访问者是`writer`角色才能允许访问
```php
Route::post('/home', 'home/index')->middleware('role', 'writer');
```

### 数据表
* `permission`
```mysql
CREATE TABLE `permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '规则唯一标识',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;
```

* `role`
```mysql
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '角色唯一标识',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
```

* `role-permission-access`
```mysql
CREATE TABLE `role_permission_access` (
  `role_id` int(11) NOT NULL COMMENT '角色主键',
  `permission_id` int(11) NOT NULL COMMENT '规则主键',
  PRIMARY KEY (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

* `user`
```mysql
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '用户唯一标识',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
```

* `user_role_access`
```mysql
CREATE TABLE `user_role_access` (
  `user_id` int(11) NOT NULL COMMENT '用户主键',
  `role_id` int(11) NOT NULL COMMENT '角色主键',
  PRIMARY KEY (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```
