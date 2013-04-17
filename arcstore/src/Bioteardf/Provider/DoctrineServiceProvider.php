<?php

namespace BioteaRdf\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\Provider\DoctrineServiceProvider as OriginalDoctrineServiceProvider;

/**
 * Override the built-in Silex Doctrine Service Loader by loading configuration
 * the way we want to
 */
class DoctrineServiceProvider extends OriginalDoctrineServiceProvider implements ServiceProviderInterface
{
    
    public function register(Application $app)
    {
        //Get the db config from the config class
        $otherDbConfig = $app['config']->trackingdb;

        //If otherDbConfig is '@rdfstore', use same settings as that
        if ($otherDbConfig == '@rdfstore') {
            $otherDbConfig = $app['config']->rdfstore;
        }
        
        //Set the options
        $app['db.options'] = array(
            'dbname'   => $otherDbConfig['db_name'],
            'host'     => $otherDbConfig['db_host'],
            'user'     => $otherDbConfig['db_user'],
            'password' => $otherDbConfig['db_pwd'],
            'driver'   => isset($otherDbConfig['db_driver']) ? $otherDbConfig['db_driver'] : 'pdo_mysql',
            'charset'  => isset($otherDbConfig['db_charset']) ? $otherDbConfig['db_charset'] : 'utf8',
        );

        //Load the parent
        return parent::register($app);
    }

    // --------------------------------------------------------------

    public function boot(Application $app)
    {
        return parent::boot($app);
    }
}


/* EOF: DoctrineServiceProvider.php */