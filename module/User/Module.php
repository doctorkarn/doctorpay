<?php

namespace User;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

// Add these import statements:
use User\Model\User;
use User\Model\UserTable;
use User\Model\Profile;
use User\Model\ProfileTable;
use User\Model\Trans;
use User\Model\TransTable;
use User\Model\Pay;
use User\Model\PayTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    // Add this method:
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'User\Model\UserTable' =>  function($sm) {
                    $tableGateway = $sm->get('UserTableGateway');
                    $table = new UserTable($tableGateway);
                    return $table;
                },
                'UserTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
                },
                        
                'User\Model\ProfileTable' =>  function($sm) {
                    $tableGateway = $sm->get('ProfileTableGateway');
                    $table = new ProfileTable($tableGateway);
                    return $table;
                },
                'ProfileTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Profile());
                    return new TableGateway('profiles', $dbAdapter, null, $resultSetPrototype);
                },
                        
                'User\Model\TransTable' =>  function($sm) {
                    $tableGateway = $sm->get('TransTableGateway');
                    $table = new TransTable($tableGateway);
                    return $table;
                },
                'TransTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Trans());
                    return new TableGateway('transactions', $dbAdapter, null, $resultSetPrototype);
                },
                        
                'User\Model\PayTable' =>  function($sm) {
                    $tableGateway = $sm->get('PayTableGateway');
                    $table = new PayTable($tableGateway);
                    return $table;
                },
                'PayTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Pay());
                    return new TableGateway('pays', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}