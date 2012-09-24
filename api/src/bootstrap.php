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

//Common Libraries
$app['sparql_url']        = 'http://biotea.idiginfo.org/sparql';
$app['sparql_client']     = new EasyRdf_Sparql_Client($app['sparql_url']);

// ------------------------------------------------------------------

/*
 * Return it
 */
return $app;

/* EOF: bootstrap.php */