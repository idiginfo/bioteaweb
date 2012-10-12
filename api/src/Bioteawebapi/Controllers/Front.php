<?php

namespace Bioteawebapi\Controllers;
use Bioteawebapi\Rest\Controller;

/**
 * Front Controller
 */
class Front extends Controller
{
    protected function configure()
    {
        $this->addRoute('/');
        $this->addFormat('text/html', 'html', "HTML page showing information about the API");
        $this->addFormat('application/json', 'json', "JSON document containing information about the API");
    }

    // --------------------------------------------------------------

    protected function execute()
    {

        $tool = new \Doctrine\ORM\Tools\SchemaTool($db);
        $classes = array(
            $db->getClassMetadata('Bioteawebapi\Entities\Annotation'),
            $db->getClassMetadata('Bioteawebapi\Entities\Document'),
            $db->getClassMetadata('Bioteawebapi\Entities\Term'),
            $db->getClassMetadata('Bioteawebapi\Entities\Topic'),
            $db->getClassMetadata('Bioteawebapi\Entities\Vocabulary'),
        );
 
        $schemaObj = $tool->getSchemaFromMetadata($classes);



        switch($this->format) {

            case 'application/json':
                return $this->app->json($this->getSummary());
            case 'text/html': default:
                return 'HAI';
            break;
        }
    }
}

/* EOF: Front.php */