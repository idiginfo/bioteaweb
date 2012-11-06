<?php

include_once('_controller.php');
doHeader();

?>

 

	<div class="container main">
		<div class="two-thirds column">
			<h3>RDF as an Interface to the Web of Data</h3>
			<p>The Web has succeeded as a dissemination platform for scientific and non-scientific papers, news, and communication in general. However, most of that information remains locked up in discrete documents, which are poorly interconnected to one another and to the Web itself. The connectivity tissue provided by RDF technology and the Social Web have barely made an impact on scientific communication. </p>

			<p>Our RDF model makes extensive reuse of existing ontologies and semantic enrichment services. We expose the model over our SPARQL endpoint, which you can query from using the tools on this site. Here you can get a diagram for the <a href="diagram_meta.php">meta-data</a> related to articles from PMC as well as the <a href="diagram_annotation.php">annotations</a> on them.</p>
			
			<div class="three columns " style="border-right: 1px solid #ccc;">
			<h5>API</h5>
			<p style="padding-right: 5px;"><a href="api.php">Our Web Services API will let you query the RDF dataset.</a></p>
			</div>
			
			<div class="three columns" style="border-right: 1px solid #ccc;">
			<h5>SPARQL Query</h5>
			<p style="padding-right: 5px;"><a href="query.php">Run a query on a sample dataset of 10,000 records.</a></p>
			</div>
			
			<div class="three columns">
			<h5>Get the Data</h5>
			<p style="padding-right: 5px;"><a href="mirror.php">Download the RDF files and run a local Virtuoso database.</a></p>
			</div>

	
			</div>
		<div class="one-third column">
			<h3 class="about">About biote&agrave;</h3>
			

            <h5>Working Models</h5>
            <p><em>See examples of how our dataset is being used to create interesting web apps for scientists.</em></p>
            
            <div id="download2">
	        <a class="external" href="http://199.102.237.69:8888/sparql/beta7/" target="_blank">Gene-based search and retrieval,<br />a first prototype</a>
            </div>


            
            <h5>Publications</h5>
			<p>In our upcoming paper, we present our approach to scholarly communication; it entails understanding of the research paper as an interface to the web of data.</p>
    <p style="padding: 10px; border: 1px solid #ccc; ">Biote&agrave;: RDFizing PubMed Central in Support for the Paper as an Interface to the Web of Data
	<br /><em>Leyla Jael Garcia Castro, Casey McLaughlin, Alexander Garcia</em>
    </p>




		</div>

	</div><!-- container -->

<?php doFooter(); ?>