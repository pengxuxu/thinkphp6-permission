<?php

return [
    // 超级管理员id
    'super_id' => 1,

    // 用户模型
    'user' => [
        'model'       => \pegnxuxu\Permission\Model\User::class,
        'froeign_key' => 'user_id',
    ],

    // 规则模型
    'permission' => [
        'model'       => \pegnxuxu\Permission\Model\Permission::class,
        'froeign_key' => 'permission_id',
    ],

    // 角色模型
    'role' => [
        'model'       => \pegnxuxu\Permission\Model\Role::class,
        'froeign_key' => 'role_id',
    ],

    // 角色与规则中间表模型
    'role_permission_access' => \pegnxuxu\Permission\Model\RolePermissionAccess::class,

    // 用户与角色中间表模型
    'user_role_access' => \pegnxuxu\Permission\Model\UserRoleAccess::class,
];
