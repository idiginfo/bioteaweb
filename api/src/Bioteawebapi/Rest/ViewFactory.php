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

namespace Bioteawebapi\Rest;
use ReflectionClass;

/**
 * Factory for views
 */
class ViewFactory
{
    /**
     * @var string
     */
    private $defaultTemplate = "<!DOCTYPE html><html><head><meta charset='utf-8'/>
                               <title>API Interface</title></head>
                               <body><h1>API Output</h1>%content%</body>
                               </html>";

    /**
     * @var string
     */
    private $defaultNamespace;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string $defaultNamespace
     */
    public function __construct($defaultNamespace = null)
    {
        $this->setViewsDefaultNamespace($defaultNamespace);
    }

    // --------------------------------------------------------------

    /**
     * Set a default HTML template to use
     *
     * @param string $template
     */
    public function setDefaultTemplate($template)
    {
        $this->defaultTemplate = $template;
    }

    // --------------------------------------------------------------

    /**
     * Set a default namespace to construct views from
     *
     * @param string  Fully-qualified namespace
     */
    public function setViewsDefaultNamespace($namespace)
    {
        if (substr($namespace, -1) != "\\") {
            $namespace .= "\\";
        }

        $this->defaultNamespace = $namespace;
    }

    // --------------------------------------------------------------

    /**
     * Build a view
     *
     * @param string $className  Either fully qualified or just a classname
     * @param array  $args        Constructor arguments for the class, if any
     * @return View  View Object
     */
    public function build($className = '', Array $args = array())
    {
        //Check errors
        if ( ! class_exists($className) && $this->defaultNamespace) {
            $className = $this->defaultNamespace . $className;
        }

        if ( ! class_exists($className)) {
            throw new \Exception("Cannot find view class: " . $className);
        }

        //Build a new view object
        $reflectionObj = new ReflectionClass($className);
        $obj = $reflectionObj->newInstanceArgs($args);

        //Set a default template if there is one
        $obj->setTemplate($this->defaultTemplate);

        //Return it
        return $obj;
    }

}

/* EOF: ViewFactory.php */