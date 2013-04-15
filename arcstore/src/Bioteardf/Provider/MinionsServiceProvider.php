<?php

namespace Bioteardf\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Minions Service Provider -- TODO TODO TODO - Move this into Minions!
 */
class MinionsServiceProvider implements ServiceProviderInterface
{
    /**
     * {inherit}
     */
    public function register(Application $app)
    {
        //LEFT OFF HERE LEFT OFF HERE LEFT OFF HERE

        //Config passed in includes:
        // $app['minions.driver'] = DRIVER_CLASS
        // $app['minions.tasks'] = PIMPLE_CLASS
        //
        // Automatically hook up to $app['dispatcher']

        $app['minions.taskbag'] = $app->share(function() use ($app) {
        });        

        $app['minions.client'] = $app->share(function() use ($app) {
        });
    }
}


/* EOF: MinionsProvider.php */