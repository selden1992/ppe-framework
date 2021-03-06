<?php
/**
 * Created by PhpStorm.
 * User: 明月有色
 * Date: 2017/10/20
 * Time: 10:38
 */

namespace Framework\Core;


use Framework\Providers\ServiceProviderInterface;
use Phalcon\DiInterface;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class MicroCore implements ModuleDefinitionInterface
{

    /**
     * Registers an autoloader related to the module
     *
     * @param DiInterface $dependencyInjector
     */
    public function registerAutoloaders(DiInterface $dependencyInjector = null)
    {

    }

    /**
     * Registers services related to the module
     *
     * @param DiInterface $di
     */
    public function registerServices(DiInterface $di)
    {
        $providers = include $di->getShared('module')->modulePath . "/Config/providers.php";
        foreach ($providers as $name => $class) {
            $this->initializeService(new $class($di));
        }
    }

    /**
     * Initialize the Service in the Dependency Injector Container.
     *
     * @param ServiceProviderInterface $serviceProvider
     *
     * @return $this
     */
    protected function initializeService(ServiceProviderInterface $serviceProvider)
    {
        $serviceProvider->register();
    }
}