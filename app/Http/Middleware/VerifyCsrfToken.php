<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/exec' //则这个路由会跳过csrf检查
    ];

    /**
     * 指定域名跳过csrf检查
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, Closure $next)
    {
        // 如果是来自 api 域名，就跳过检查
        if ($_SERVER['SERVER_NAME'] != config('api.domain'))
        {
            return parent::handle($request, $next);
        }

        return $next($request);
    }

    // 上面两种方法都可以
}
