<?php
/**
 * Created by PhpStorm.
 * User: 明月有色
 * Date: 2017/10/20
 * Time: 14:07
 */

namespace Framework\Providers;


use Framework\App;
use Phalcon\Config;
use Phalcon\Mvc\Router;

class ModulesRouteServiceProvider extends ServiceProvider
{

    /**
     * Register application service.
     *
     * @return mixed
     */
    public function register()
    {
        if (IS_CLI) {
            $this->registerConsole();
        } else {
            $this->registerWeb();
        }
    }

    private function registerConsole()
    {
        $this->di->set('module',function (){
            return new Config([
                'modulePath'=>App::getRootPath().'/apps/Console',
                'defaultNamespace'=>"\\Apps\\Console\\Tasks",
            ]);
        });

        $this->di->set( "router",function (){
            $router = new \Phalcon\Cli\Router();
            $router->setDefaultModule('cli');
            return $router;
        });
    }

    private function registerWeb()
    {
        $router     = new Router();
        $config     = $this->di->getShared('config');
        $modules    = $config->modules;
        $router->setDefaultModule($config->default_module);
        foreach ($modules as $moduleName => $module) {
            $module['domain'] = $module['domain'] ?? $moduleName;

            $router->add(
                "{$module['domain']}",
                [
                    "module"     => $moduleName,
                    "controller" => 'index',
                    "action"     => 'index',
                ]
            );

            $router->add(
                "{$module['domain']}/",
                [
                    "module"     => $moduleName,
                    "controller" => 'index',
                    "action"     => 'index',
                ]
            );

            $router->add(
                "{$module['domain']}/:controller",
                [
                    "module"     => $moduleName,
                    "controller" => 1,
                    "action"     => 'index',
                ]
            );

            $router->add(
                "{$module['domain']}/:controller/:action",
                [
                    "module"     => $moduleName,
                    "controller" => 1,
                    "action"     => 2,
                ]
            );

            $router->add(
                "{$module['domain']}/:controller/:action/:params",
                [
                    "module"     => $moduleName,
                    "controller" => 1,
                    "action"     => 2,
                    "params"     => 3
                ]
            );
        }
        $uri = $_SERVER['HTTP_HOST'].($_GET['_url']??'/');
        $router->handle($uri);

        $applicationPath = App::getRootPath();
        $moduleName      = $router->getModuleName();
        $nameSpace       = $modules[$moduleName]['nameSpace'];

        $module = new Config([
            'modulePath'=>$applicationPath.'/apps/Http/'.$nameSpace,
            'defaultNamespace'=>"\\Apps\\Http\\{$nameSpace}\\Controllers",
        ]);

        $this->di->set('module',function ()use ($module){
            return $module;
        });

        $this->di->set( "router",function ()use ($router){
            return $router;
        });
    }
}