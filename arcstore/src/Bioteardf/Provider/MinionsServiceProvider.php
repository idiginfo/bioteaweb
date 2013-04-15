<?php

namespace Bioteardf\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Pimple;
use Minions;

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
        //Config passed in includes:
        // $app['minions.driver'] = DRIVER_CLASS
        // $app['minions.tasks'] = PIMPLE_CLASS

        //Minions TaskBag
        if ( ! in_array('minions.tasks', $app->keys())) {
            $app['minions.tasks'] = $app->share(function() use ($app) {
                return new Pimple();
            });
        }       

        //Minions Client
        $app['minions.client'] = $app->share(function() use ($app) {
            return new Minions\Client($app['minions.driver']);
        });

        //Minions Workers
        $app['minions.cmd.workers'] = $app->share(function() use ($app) {
            return new Minions\Command\Workers($app['minions.driver'], $app['minions.tasks'], $app['dispatcher']);
        });
    }

    // --------------------------------------------------------------

    public function boot(Application $app)
    {
        /* pass */
    }
}


/* EOF: MinionsProvider.php */