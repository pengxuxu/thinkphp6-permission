<?php

namespace pengxuxu\Permission;

use think\App;

class Permission
{
    private $app;

    private $config;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->config = config('permission');
    }
}
