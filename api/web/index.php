<?php

/**
 * Biotea API
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @license Florida State University - All Rights Reserved
 */

/**
 * @file Web Application File
 */

// ------------------------------------------------------------------

/*
 * Uses
 */
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use FOS\Rest\Util\FormatNegotiator;
use Symfony\Component\ClassLoader\UniversalClassLoader;

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
$loader->registerPrefix('EasyRdf', BASEPATH . '/vendor/njh/easyrdf/lib');
$loader->register();

//EasyRDF Lunacy
set_include_path(get_include_path() . PATH_SEPARATOR . BASEPATH . '/vendor/njh/easyrdf/lib');

// ------------------------------------------------------------------

/*
 * Load Silex
 */
$app = new Application();

//Libraries
$app['sparql_url']        = 'http://biotea.idiginfo.org/sparql';
$app['sparql_client']     = new EasyRdf_Sparql_Client($app['sparql_url']);
$app['fosrest']           = new FormatNegotiator();

//Controllers
$app['front_controller']  = new Bioteawebapi\Controllers\Front($app);
$app['topics_controller'] = new Bioteawebapi\Controllers\Topics($app);
$app['vocabs_controller'] = new Bioteawebapi\Controllers\Vocabularies($app);
$app['terms_controller']  = new Bioteawebapi\Controllers\Terms($app);

// ------------------------------------------------------------------

/*
 * Routes
 */
$app->get('/',                     array($app['front_controller'], 'run'));
$app->get('/topics',               array($app['topics_controller'], 'run'));
$app->get('/topics/{topic}',       array($app['topics_controller'], 'run'));
$app->get('/vocabularies',         array($app['vocabs_controller'], 'run'));
$app->get('/vocabularies/{vocab}', array($app['vocabs_controller'], 'run'));
$app->get('/terms',                array($app['terms_controller'], 'run'));
$app->get('/terms/{term}',         array($app['terms_controller'], 'run'));

// ------------------------------------------------------------------

/*
 * Away we go!
 */
$app->run();

/* EOF: index.php */