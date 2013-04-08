<?php

namespace Bioteardf\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use ARC2;
use RuntimeException;

class Arc2ServiceProvider implements ServiceProviderInterface
{
    // --------------------------------------------------------------

    /**
     * {inherit}
     */
    public function register(Application $app)
    {
        //Store
        $app['arc2.store'] = $app->share(function() use ($app) {

            //Get config
            $config = $app['arc2.config'];
            $config['store_name'] = $app['arc2.store_name'];

            //Return a new store
            return ARC2::getStore($config);
        });

        //Endpoint
        $app['arc2.sparql'] = $app->share(function() use ($app) {

            //Get config
            $config = $app['arc2.config'];
            $config['store_name'] = $app['arc2.store_name'];            

            //Endpoint features config
            $config['endpoint_features'] = array(
                'select', 'construct', 'ask', 'describe', 
                //'load', 'insert', 'delete', 'dump' 
                /* dump is a special command for streaming SPOG export */
            );


            //Get Endpoint
            return ARC2::getStoreEndpoint($config);
        });

        //Parser
        $app['arc2.parser'] = $app->protect(function() use ($app) {
            return ARC2::getRDFParser();
        });

    }

    // --------------------------------------------------------------

    public function boot(Application $app)
    {
        /* pass */
    }    
}

/* EOF: Arc2ServiceProvider.php */