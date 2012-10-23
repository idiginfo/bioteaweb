<?php 

$filename1 = '/data/diskarray2/pubmedOpenAccess/mirror/rdf_files.tar.gz';
$filename2 = '/data/diskarray2/pubmedOpenAccess/mirror/virtuoso_graphs.tar.gz';

function _format_bytes($a_bytes)
{
    if ($a_bytes < 1024) {
        return $a_bytes .' B';
    } elseif ($a_bytes < 1048576) {
        return round($a_bytes / 1024, 2) .' KiB';
    } elseif ($a_bytes < 1073741824) {
        return round($a_bytes / 1048576, 2) . ' MiB';
    } elseif ($a_bytes < 1099511627776) {
        return round($a_bytes / 1073741824, 2) . ' GiB';
    } elseif ($a_bytes < 1125899906842624) {
        return round($a_bytes / 1099511627776, 2) .' TiB';
    } elseif ($a_bytes < 1152921504606846976) {
        return round($a_bytes / 1125899906842624, 2) .' PiB';
    } elseif ($a_bytes < 1180591620717411303424) {
        return round($a_bytes / 1152921504606846976, 2) .' EiB';
    } elseif ($a_bytes < 1208925819614629174706176) {
        return round($a_bytes / 1180591620717411303424, 2) .' ZiB';
    } else {
        return round($a_bytes / 1208925819614629174706176, 2) .' YiB';
    }
}


?>

<?php

include_once('_controller.php');
doHeader();

?>



	<div class="container main">
		<div class="two-thirds column">
			<h3>Get the RDF Collection</h3>
			
			<br />
			<p>
                The entire collection of RDF documents is available for download
                as a collection of ZIP files.  Each document consists of three
                RDF files serialised as XML:
            </p>

            <ul>
                <li>
                    <strong>The main RDF file - </strong> Contains RDF representation
                    of the PMC document.
                    <em>(e.g. PMC15342.rdf)</em>
                </li>
                <li>
                    <strong>NCBO Annotator file - </strong> Contains NCBO annotations
                    for the document.
                    <em>(e.g. AO_annotations/PMC15342_ncboAnnotator.rdf)</em>
                </li>
                <li>
                    <strong>WhatIzIt Annotator file - </strong> Contains WhatIzIt annotations
                    for the document.
                    <em>(e.g. Bio2RDF/MC15342_whatizitUkPmcAll.rdf)</em>
                </li>
            </ul>
			
            <h4>How to Download</h4>

            <p>
                <strong>FTP Access</strong>
                <br />
                Connect your FTP client to
                <a href="ftp://biotea.idiginfo.org">ftp://biotea.idiginfo.org</a>.  
                There is no login.
            </p>
            
            <p><strong>HTTP</strong>
            <br />The HTTP address is: <a href="http://biotea.idiginfo.org/files">http://biotea.idiginfo.org/files</a>.</p>
            
            
			<div style="height: 300px;"></div>
			
	
			</div>
		<div class="one-third column">
			
			<h3 class="space">&nbsp;</h3>
			<br />
			<h5>Build a Virtuoso RDF/SPARQL database with our Collection</h5>
            
            <p>Follow <a href="graph_loading.php">these instructions</a> for creating a Virtuoso database.</p>
			
			<?php 
			/* 
			<div id="download">
			<a class="file" href="http://biotea.idiginfo.org/pubmedOpenAccess/mirror/rdf_files.tar.gz">rdf_files.tar.gz
			<br /><br /><em><?php echo _format_bytes(filesize($filename1)); ?> 
			<br /><?php echo "Updated: " . date ("F d Y H:i:s", filemtime($filename1)); ?> </em></a>
			</div>

			<div id="download2">
			<a class="file" href="http://biotea.idiginfo.org/pubmedOpenAccess/mirror/virtuoso_graphs.tar.gz">virtuoso_graphs.tar.gz 
			<br /><br /><em><?php echo _format_bytes(filesize($filename2)); ?> 
			<br /><?php echo "Updated: " . date ("F d Y H:i:s", filemtime($filename2)); ?> </em></a>
			</div>
			*/
			?>

		</div>

	</div><!-- container -->

<?php doFooter(); ?>
