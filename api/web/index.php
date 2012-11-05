<?php

/**
 * Bioteaweb API
 *
 * A rest API frontend and indexer for the Biotea RDF project
 *
 * @link    http://biotea.idiginfo.org/api
 * @author  Casey McLaughlin <caseyamcl@gmail.com>
 * @license Copyright (c) Florida State University - All Rights Reserved
 */

// ------------------------------------------------------------------

/**
 * @file Web Application File
 */

// ------------------------------------------------------------------

/*
 * Uses
 */
use Bioteawebapi\Rest\Application as RestApp;
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
$app['fosrest']     = new FormatNegotiator();

$app['viewfactory'] = new Bioteawebapi\Rest\ViewFactory('Bioteawebapi\Views');
$app['viewfactory']->setDefaultTemplate(file_get_contents(BASEPATH . '/src/Bioteawebapi/Files/html_template.html'));

//Controllers
$app['webapp'] = new RestApp($app);
$app['webapp']->add(new Bioteawebapi\Controllers\Front($app, $app['db.orm.em']));
$app['webapp']->add(new Bioteawebapi\Controllers\TermsList($app, $app['db.orm.em']));
$app['webapp']->add(new Bioteawebapi\Controllers\TermsSingle($app, $app['db.orm.em']));
$app['webapp']->add(new Bioteawebapi\Controllers\TopicsList($app));
$app['webapp']->add(new Bioteawebapi\Controllers\TopicsSingle($app));
// $app['webapp']->add(new Bioteawebapi\Controllers\DocumentsList($app));
// $app['webapp']->add(new Bioteawebapi\Controllers\DocumentsSingle($app));
// $app['webapp']->add(new Bioteawebapi\Controllers\VocabulariesList($app));
// $app['webapp']->add(new Bioteawebapi\Controllers\VocabulariesSingle($app));

// ------------------------------------------------------------------

/*
 * Away we go!
 */
$app->run();

/* EOF: index.php */