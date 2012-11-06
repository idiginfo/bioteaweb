<?php

include_once('_controller.php');
doHeader();

?>

	<div class="container main">
		<div class="sixteen columns">
			
			<h5>Annotations Diagram</h5>
			<p>Our RDF model makes extensive reuse of existing ontologies and semantic enrichment services. We expose the model over our SPARQL endpoint, which you can query from using the tools on this site. Here you can get a diagram for the <a href="diagram_meta.php">meta-data</a> related to articles from PMC as well as the <a href="diagram_annotation.php">annotations</a> on them</p>
			
			<img src="images/annotations.png" alt="Annotations" style="background: #fff; padding: 20px; border: 1px solid #000;" />
			
			

	<div id="spacer"></div>
			
			
	
			</div>
		

	</div><!-- container -->

<?php doFooter(); ?>