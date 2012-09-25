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
$app['solrq_controller']  = new Bioteawebapi\Controllers\SolrQuery($app, $app['solr_client']);

// ------------------------------------------------------------------

/*
 * Routes
 */
$app->get('/',                array($app['front_controller'], 'run'));
$app->get('/{field}',         array($app['solrq_controller'], 'run'));
$app->get('/{field}/{value}', array($app['solrq_controller'], 'run'));

// ------------------------------------------------------------------

/*
 * Away we go!
 */
$app->run();

/* EOF: index.php */