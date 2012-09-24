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