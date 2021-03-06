<?php
/**
 * Created by PhpStorm.
 * User: 明月有色
 * Date: 2017/11/29
 * Time: 20:40
 */

namespace Framework\Support;

use Framework\App;
use Framework\Providers\ViewServiceProvider;
use Phalcon\Di;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt;

abstract class Handler extends \Whoops\Handler\Handler
{
    /**
     * 获取路径是Common路径的视图对象
     *
     * @return View
     */
    protected function getView()
    {
        /**
         * 为了跨模块显示错误页面，这里重新设置了路径
         */
        $di              = Di::getDefault();
        $applicationPath = App::getRootPath();
        if( !$di->has('view') ){
            // 没有注册业务视图,注册一个用来显示错误页面
            (new ViewServiceProvider($di))->register();
        }
        $view            = $di->getShared('view');
        $viewDir         = $applicationPath . '/apps/Http/Common/Views/';
        $view->setViewsDir($viewDir);
        $view->registerEngines([
            ".html" => function ($view, Di $di) {
                $volt = new Volt($view, $di);

                $volt->setOptions([
                    // 编译目录
                    'compiledPath' => App::getRootPath() . '/storage/cache/view',
                ]);

                return $volt;
            }
        ]);

        return $view;
    }
}