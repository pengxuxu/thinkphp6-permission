<?php

namespace pengxuxu\Permission\Model;

use think\Model;
use pengxuxu\Permission\Contract\PermissionContract;

/**
 * 权限.
 */
class Permission extends Model implements PermissionContract
{
    use \pengxuxu\Permission\Traits\Permission;
}
