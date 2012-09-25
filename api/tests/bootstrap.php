<?php

//Check for required files
$checkFiles['autoload'] = __DIR__.'/../vendor/autoload.php';
foreach($checkFiles as $file) {

    if ( ! file_exists($file)) {
        throw new RuntimeException('Install dependencies to run test suite.');
    }
}

//Vendor Autoload
$autoload = require_once $checkFiles['autoload'];

//Basepath
define('BASEPATH', realpath(__DIR__ . '/../'));

//Setup Classloader
$loader = new Symfony\Component\ClassLoader\UniversalClassLoader();
$loader->registerNamespace('Bioteawebapi', BASEPATH . '/src');
$loader->registerPrefix('EasyRdf', BASEPATH . '/vendor/njh/easyrdf/lib');
$loader->register();

/* EOF: bootstrap.php */