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

//SOLR Client
$app['solr_config'] = array('adapteroptions' => $app['config']->solr['document']);
$app['solr_client'] = new SolrClient(new Solarium_Client($app['solr_config']));

// ------------------------------------------------------------------

/*
 * Return it
 */
return $app;

/* EOF: bootstrap.php */