<?php

/**
 * Biotea API
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @license Florida State University - All Rights Reserved
 */

/**
 * @file Bootstrap File
 */

// ------------------------------------------------------------------

/*
 * Uses
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Bioteawebapi\Services\SolrClient;
use Bioteawebapi\Services\MySQLClient;
use Silex\Application;

// ------------------------------------------------------------------

/*
 * Bootstrap
 */

//BASEPATH
define('BASEPATH', realpath(__DIR__ . '/../'));

//Autoloader
require(BASEPATH . '/vendor/autoload.php');

//Setup Classloader
$loader = new UniversalClassLoader();
$loader->registerNamespace('Bioteawebapi', BASEPATH . '/src');
$loader->registerNamespace('TaskTracker', BASEPATH . '/src');

$loader->registerPrefix('EasyRdf', BASEPATH . '/vendor/njh/easyrdf/lib');
$loader->register();

//EasyRDF Lunacy
set_include_path(get_include_path() . PATH_SEPARATOR . BASEPATH . '/vendor/njh/easyrdf/lib');

//Solarium 

// ------------------------------------------------------------------

/*
 * Load Silex
 */

//Silex
$app = new Application();

/*
 * Common Libraries
 */

//Configuration
$app['config'] = new Configula\Config(BASEPATH . '/config/');

//SOLR Client for Docs
$app['solr_config'] = array('adapteroptions' => $app['config']->solr['terms']);
$app['solr_client'] = $app->share(function($app) {
    return new SolrClient(new Solarium_Client($app['solr_config']));
});

//MySQL ($app['db'])
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array (
        'driver'    => 'pdo_mysql',
        'host'      => $app['config']->mysql['host'],
        'dbname'    => $app['config']->mysql['name'],
        'user'      => $app['config']->mysql['user'],
        'password'  => $app['config']->mysql['pass'],
    )
));

//MySQL Client for Docs
$app['mysql_client'] = $app->share(function($app) { 
    return new MySQLClient($app['db']);
});

//Doc Builder
$app['builder'] = $app->share(function($app) {
    return new Bioteawebapi\Services\DocSetBuilder($app['config']->vocabularies);
});

//Doc Indexer
$app['indexer'] = $app->share(function($app) {
    return new Bioteawebapi\Services\DocSetBuilder($app['builder'], $app['solr_client'], $app['mysql_client']);
});

// ------------------------------------------------------------------

/*
 * Return it
 */
return $app;

/* EOF: bootstrap.php */