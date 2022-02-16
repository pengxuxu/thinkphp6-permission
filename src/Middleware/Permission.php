<?php

namespace pengxuxu\Permission\Middleware;

use think\Request;
use think\Response;
use pengxuxu\Permission\Contract\PermissionMiddlewareContract;
use pengxuxu\Permission\Contract\UserContract;

/**
 * 权限中间件.
 */
class Permission implements PermissionMiddlewareContract
{
    public function handle($request, \Closure $next, $permission)
    {
        // 暂时修复 6.0.3 options 问题
        if ($request->isOptions()) {
            return $next($request);
        }
        
        if (!$request->user) {
            return $this->handleNotLoggedIn($request);
        }

        if (false === $this->requestHasPermission($request, $request->user, $permission)) {
            return $this->handleNoAuthority($request);
        }

        return $next($request);
    }

    /**
     * 检查是否有权限.
     *
     * @param Request      $request
     * @param UserContract $user
     * @param [type]       $permission
     *
     * @return bool
     */
    public function requestHasPermission(Request $request, UserContract $user, $permission)
    {
        if (!$user->can($permission)) {
            return false;
        }

        return true;
    }

    /**
     * 用户未登录.
     *
     * @param Request $request
     *
     * @return void
     */
    public function handleNotLoggedIn(Request $request): Response
    {
        return Response::create(['message' => '用户未登录', 'code' => '50000'], 'json', 401);
    }

    /**
     * 没有权限.
     *
     * @param Request $request
     *
     * @return void
     */
    public function handleNoAuthority(Request $request): Response
    {
        return Response::create(['message' => '没有权限', 'code' => '50001'], 'json', 401);
    }
}
