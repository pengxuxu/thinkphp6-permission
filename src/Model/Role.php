<?php

namespace pengxuxu\Permission\Model;

use think\Model;
use pengxuxu\Permission\Contract\RoleContract;

/**
 * 角色.
 */
class Role extends Model implements RoleContract
{
    use \pengxuxu\Permission\Traits\Role;
}
