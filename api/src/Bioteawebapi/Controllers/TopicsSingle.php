<?php

namespace Bioteawebapi\Controllers;
use Bioteawebapi\Rest\Format;
use Bioteawebapi\Rest\Route;
use Bioteawebapi\Rest\Parameter;

/**
 * Single topic info Controller
 */
class TopicsSingle extends Abstracts\SingleEntityController
{
    /** @inherit */
    protected function configure()
    {
        $this->add(new Route('/topics/{topic}'));
        $this->add(new Format('text/html', 'html', "Returns information about a single topic in HTML"));
        $this->add(new Format('application/json', 'json', "Returns information about a single topic in JSON"));
    }


    // --------------------------------------------------------------

    /** @inherit */
    protected function execute()
    {
        //Get the topic by its ID
        $topicId  = $this->getPathSegment(2);
        $topicObj = array_shift($this->app['dbclient']->getTopics($topicId));

        if ( ! $topicObj) {
            $this->app->abort(404, sprintf("Topic with ID %d not Found", $topicId));
        }

        //LEFT OFF HERE
        echo "FOUND TOPIC: " . $topicId;
    }    
}

/* EOF: TopicsSingle.php */