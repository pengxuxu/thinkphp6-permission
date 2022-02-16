<?php

namespace pengxuxu\Permission\Model;

use think\Model;
use pengxuxu\Permission\Contract\UserContract;

class User extends Model implements UserContract
{
    use \pengxuxu\Permission\Traits\User;
}
