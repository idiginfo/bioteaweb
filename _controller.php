<?php

function doHeader($vars='') {

?>
<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>

	<!-- Basic Page Needs
  ================================================== -->
	<meta charset="utf-8">
	<title>bioteà - biomedical annotation</title>
	<meta name="description" content="">
	<meta name="author" content="iDigInfo at Florida State University">

	<!-- Mobile Specific Metas
  ================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- CSS
  ================================================== -->
	<link href='http://fonts.googleapis.com/css?family=Oswald:400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="stylesheets/base.css">
	<link rel="stylesheet" href="stylesheets/skeleton.css">
	<link rel="stylesheet" href="stylesheets/layout.css">

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	


	<!-- Favicons
	================================================== -->
	<link rel="shortcut icon" href="images/favicon.ico">
	<link rel="apple-touch-icon" href="images/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
	
	<?php
	if ($vars['sparql']) {
	?>

	<script type="text/javascript">
    /*<![CDATA[*/
	var last_format = 1;
	function format_select(query_obg)
	{
		var query = query_obg.value; 
		var format = query_obg.form.format;

		if ((query.match(/\bconstruct\b/i) || query.match(/\bdescribe\b/i)) && last_format == 1) {
			for(var i = format.options.length; i > 0; i--)
				format.options[i] = null;
			format.options[1] = new Option('N3/Turtle','text/rdf+n3');
			format.options[2] = new Option('RDF/JSON','application/rdf+json');
			format.options[3] = new Option('RDF/XML','application/rdf+xml');
			format.options[4] = new Option('N-Triples','text/plain');
			format.options[5] = new Option('XHTML+RDFa','application/xhtml+xml');
			format.options[6] = new Option('ATOM+XML','application/atom+xml');
			format.options[7] = new Option('ODATA/JSON','application/odata+json');
			format.options[8] = new Option('JSON-LD','application/x-json+ld');
			format.options[9] = new Option('HTML+Microdata','text/html');
			format.options[10] = new Option('Microdata/JSON','application/microdata+json');
			format.options[11] = new Option('CSV','text/csv');
			format.selectedIndex = 1;
			last_format = 2;
		}

		if (!(query.match(/\bconstruct\b/i) || query.match(/\bdescribe\b/i)) && last_format == 2) {
			for(var i = format.options.length; i > 0; i--)
				format.options[i] = null;
			format.options[1] = new Option('HTML','text/html');
			format.options[2] = new Option('Spreadsheet','application/vnd.ms-excel');
			format.options[3] = new Option('XML','application/sparql-results+xml');
			format.options[4] = new Option('JSON','application/sparql-results+json');
			format.options[5] = new Option('Javascript','application/javascript');
			format.options[6] = new Option('N3/Turtle','text/rdf+n3');
			format.options[7] = new Option('RDF/XML','application/rdf+xml');
			format.options[8] = new Option('N-Triples','text/plain');
			format.options[9] = new Option('CSV','text/csv');
			format.selectedIndex = 1;
			last_format = 1;
		}
	}

function format_change(e)
{
var format = e.value;
var cxml = document.getElementById("cxml");
if (!cxml) return;
if ((format.match (/\bCXML\b/i)))
{
cxml.style.display="block";
} else {
cxml.style.display="none";
}
}
function savedav_change(e)
{
var savefs = document.getElementById("savefs");
if (!savefs) return;
if (e.checked)
{
savefs.style.display = "block";
}
else
{
savefs.style.display = "none";
}
}
function sparql_endpoint_init()
{
var cxml = document.getElementById("cxml");
if (cxml) cxml.style.display="none";
var savefs = document.getElementById("savefs");
if (savefs) savefs.style.display="none";
}
    /*]]>*/
    </script>
    

</head>
<body onload="sparql_endpoint_init()">


    <?php } else { ?>
    
    </head>
    <body>
    
    <?php } ?>

<div id="header">
	<div class="container">
		<div class="sixteen columns">
		<ul>
			<li><a class="first" href="index.php">Home</a></li>
			<li><a class="second" href="api.php">API</a></li>
			<li><a class="third" href="query.php">Run a Query</a></li>
			<li><a class="first" href="mirror.php">Get the Data</a></li>
			<li><a class="second" href="contact.php">Contact</a></li>
		</ul>
		</div>
		<div class="two-thirds column space">&nbsp;</div>
		<div class="one-third column logo">
		<a href="index.php"><img src="images/biotea_logo.png" alt="bioteà" border="0" /></a>
		</div>
	</div>
	</div>

	

<?php } 

function doFooter() {

?>
<div id="footer">
	<div class="container">
		<a href="https://www.idiginfo.org" target="_blank"><img src="images/idiginfo.png" alt="iDigInfo" border="0" /></a>
	</div>
	</div>


<!-- End Document
================================================== -->
</body>
</html>
<?php

}