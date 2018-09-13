<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        //没找到对应的model
        if ($exception instanceof ModelNotFoundException) {
            $exception = new NotFoundHttpException($exception->getMessage(), $exception);
        }

        $debug = config('app.debug', false);  // 判断debug是否开启
        if (empty($debug)) {  // 如果debug关闭
            $result = method_exists($exception, 'getStatusCode');
            if (!empty($result)) {
                // 404友情提示
                $statusCode = $exception->getStatusCode();
                if ($statusCode == 404) {
                    return response()->view('error.404', [
                        'info' => '抱歉,指定的页面不存在.',
                        'url' => '/',
                        'code' => 404,
                        'msg' => 'Sorry, page not found.'
                    ]);
                }
            } else {
                // 出现错误提示
                return response()->view('error.503', [
                    'info' => '抱歉,好像出错了.',
                    'url'  => '/',
                    'code' => 503,
                    'msg'  => 'Error,It have been wrong.'
                ]);
            }
        } else {
            // 如果开启debug模式
            return parent::render($request, $exception);
        }
    }
}
