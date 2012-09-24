<?php

namespace Bioteawebapi\Controllers;
use EasyRdf_Sparql_Client as SparqlClient;

/**
 * URL is: /
 */
class Front extends Abstracts\SparqlClientController
{
    // --------------------------------------------------------------

    public function run()
    {
        //For now, we'll just be a-testin
        return $this->test();
    }

    // --------------------------------------------------------------

    public function test()
    {
        $config = array(
            'adapteroptions' => array(
                'host' => '127.0.0.1',
                'port' => 8080,
                'path' => '/solr/',
            )
        );

        $client = new \Solarium_Client($config);


        //This does a DELETE on SOLR
        // $update = $client->createUpdate();
        // $update->addDeleteQuery('*:*');
        // $update->addCommit();
        // $result = $client->update($update);
        // var_dump($result);

        //Test Update using actual Class
        // $update = $client->createUpdate();
        // $doc = new \Bioteawebapi\Models\SolrIndexDocument();
        // $doc->setRdfFilePath('/tmp/test');
        // $doc->setRdfAnnotationFilePaths(array('/tmp/test/1', '/tmp/test/2'));
        // $doc->setTerms(array('abc', 'def'));
        // $doc->setVocabularies(array('asasd', 'asdfasdf'));
        // $doc->setTopics(array('topic1', 'topics2'));
        // $doc1 = $update->createDocument($doc->params());
        // $update->addDocuments(array($doc1));
        // $update->addCommit();
        // $result = $client->update($update);

        //Test Retrieve Documents
        $query = $client->createSelect();
//        $query->setResultClass('Bioteawebapi\Models\SolrIndexDocument');
        $resultset = $client->select($query);
        echo 'NumFound: '.$resultset->getNumFound();

          // show documents using the resultset iterator
        foreach ($resultset as $document) {

            echo '<hr/><table>';

            // the documents are also iterable, to get all fields
            foreach($document AS $field => $value)
            {
                // this converts multivalue fields to a comma-separated string
                if(is_array($value)) $value = implode(', ', $value);
                
                echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
            }

            echo '</table>';
        }
  

    }

}

/* EOF: Front.php */