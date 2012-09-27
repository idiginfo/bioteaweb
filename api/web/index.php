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
use FOS\Rest\Util\FormatNegotiator;

// ------------------------------------------------------------------

/*
 * Fix trailing slash issue with URI
 * @see https://github.com/fabpot/Silex/issues/149
 */
$_SERVER['REQUEST_URI'] = rtrim($_SERVER['REQUEST_URI'], '/');

// ------------------------------------------------------------------

/*
 * Bootstrap
 */
require(__DIR__ . '/../src/bootstrap.php');

// ------------------------------------------------------------------

/*
 * Web Specific Libraries
 */

//Libraries
$app['fosrest'] = new FormatNegotiator();

//Controllers
$app['front_controller']  = new Bioteawebapi\Controllers\Front($app);
$app['terms_controller']  = new Bioteawebapi\Controllers\Terms($app, $app['solr_client']);
$app['docs_controller']   = new Bioteawebapi\Controllers\Documents($app, $app['solr_client']);

// ------------------------------------------------------------------

/*
 * Routes
 */

//Front
$app->get('/',                array($app['front_controller'], 'run'));

//Terms
$app->get('/terms',         array($app['terms_controller'], 'run'));
$app->get('/terms/{value}', array($app['docs_controller'],  'run'));

//Topics
$app->get('/topics',         array($app['terms_controller'], 'run'));
$app->get('/topics/{value}', array($app['docs_controller'],  'run'));

//Vocabularies
$app->get('/vocabularies',         array($app['terms_controller'], 'run'));
$app->get('/vocabularies/{value}', array($app['docs_controller'],  'run'));

// ------------------------------------------------------------------

/*
 * Away we go!
 */
$app->run();

/* EOF: index.php */