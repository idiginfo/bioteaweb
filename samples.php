<?php

$query = '1';
$query = $_GET['sample'];

if (is_numeric($query)) { } else { $query = '1'; } 

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
<body onload="sparql_endpoint_init()" class="sample<?php echo $query; ?>">



	<div id="header">
	<div class="container">
		<div class="sixteen columns">
		<ul>
			<li><a class="first" href="index.html">Run a Query</a></li>
			<li><a class="second" href="mirror.php">Create a Mirror</a></li>
			<li><a class="third" href="graph_loading.php">Load Graphs</a></li>
			<li><a class="first" href="contact.html">Contact</a></li>
		</ul>
		</div>
		<div class="two-thirds column space">&nbsp;</div>
		<div class="one-third column logo">
		<a href="/"><img src="images/biotea_logo.png" alt="bioteà" border="0" /></a>
		</div>
	</div>
	</div>

	<div class="container main">
		<div class="two-thirds column">
			
			
			<?php
		if ($query == 10) { 
		?>
		<p class="overview">Needs a title</p>
		<?php
		} else if ($query == 9) { 
		?>
		<p class="overview">Needs a title</p>
		<?php
		} else if ($query == 8) { 
		?>
		<p class="overview">Needs a title</p>
		<?php
		} else if ($query == 7) { 
		?>
		<p class="overview">Retrieve all papers annotated with VHL or APC genes.</p>
		<?php
		} else if ($query == 6) { 
		?>
		<p class="overview">It is also possible to use other sources such as bio2rdf or the original source (GO, CHEBI, etc) to retrieve more information about a particular annotation. for instance, more information about a particular GO terms in bio2rdf that has been selected from the set of annotations in a particular paper.</p>
		<p><em>Some graphical information will be available in the prototype we are working on.</em></p>
		<?php
		} else if ($query == 5) { 
		?>
		<p class="overview">For each paper from sample 1, retrieve the context, i.e. text, for the annotation</p>
		<?php
		} else if ($query == 4) { 
		?>
		<p class="overview">For each paper from sample 1, retrieve all annotations.</p>
		<p><em>Here we show the query for the first paper. It would be also possible to select only some of the vocabularies so only those annotations would be retrieved.</em></p>
		<?php
		} else if ($query == 3) { 
		?>
		<p class="overview">Retrieve all papers annotated with VHL or APC genes.</p>
		<?php
		} else if ($query == 2) { 
		?>
		<p class="overview">Retrieve pubmed id, doi, abstract and each GO id for all papers including the word “cancer” in the abstract and annotated with at least one GO term.</p>
		<?php
		} else { 
		?>
		<p class="overview">Retrieve pubmed id, doi, abstract, and each uniprot accession for all papers including the word “cancer” in the abstract and annotated with at least one uniprot accession.</p>
		<?php
		} 
		?>
		
		
			<form action="http://biotea.idiginfo.org/sparql" target="_blank" method="get">
	<fieldset>
		<input type="submit" value="Run Query"/>
		
		<label for="query">Query Text</label>
		<?php
		if ($query == 10) { 
		?>
		<textarea rows="12" name="query" id="query" onchange="format_select(this)" onkeyup="format_select(this)">select ?annot str(?body) AS ?term ?topic ?seeAlso str(?init) AS ?posInit str(?end) AS ?posEnd ?comment
where {  
?annot a aot:ExactQualifier ;
ao:annotatesResource <http://biotea.idiginfo.org/pubmedOpenAccess/rdf/PMC2703570.rdf> ;
ao:body ?body ;
ao:hasTopic ?topic ;  
rdfs:seeAlso ?seeAlso ;
ao:context ?context .
?context rdfs:resource ?resource .
OPTIONAL {?context aos:init ?init }.
OPTIONAL {?context aos:end ?end } .
?resource rdfs:comment ?comment . 
}</textarea>
		<?php
		} else if ($query == 9) { 
		?>
		<textarea rows="12" name="query" id="query" onchange="format_select(this)" onkeyup="format_select(this)">select *
where {  
?a a bibo:AcademicArticle ;
dcterms:title ?title ;
rdfs:seeAlso ?seeAlso ;
dcterms:publisher ?publisher ;
bibo:volume ?volume ;
bibo:issue ?issue ;
bibo:pageStart ?start ;
bibo:pageEnd ?end ;
dcterms:issued ?date ;
bibo:authorList ?authorList ;
bibo:abstract ?abstract .
?publisher dcterms:title ?journalTitle ;
bibo:issn ?issn .
?authorList rdfs:member ?member .
?member foaf:name ?author .
}</textarea>
		<?php
		} else if ($query == 8) { 
		?>
		<textarea rows="12" name="query" id="query" onchange="format_select(this)" onkeyup="format_select(this)">select *
where {  
  <http://biotea.idiginfo.org/pubmedOpenAccess/rdf/PMC2703570.rdf> dcterms:title ?title ;
    rdfs:seeAlso ?seeAlso ;
    dcterms:publisher ?publisher ;
    bibo:volume ?volume ;
    bibo:issue ?issue ;
    bibo:pageStart ?start ;
    bibo:pageEnd ?end ;
    dcterms:issued ?date ;
    bibo:authorList ?authorList ;
    bibo:abstract ?abstract .
  ?publisher dcterms:title ?journalTitle ;
    bibo:issn ?issn .
  ?authorList rdfs:member ?member .
  ?member foaf:name ?author .
}</textarea>
		<?php
		} else if ($query == 7) { 
		?>
		<textarea rows="12" name="query" id="query" onchange="format_select(this)" onkeyup="format_select(this)">PREFIX bibo:<http://purl.org/ontology/bibo/>
PREFIX aot:<http://purl.org/ao/types/>
PREFIX ao:<http://purl.org/ao/core/>
PREFIX doco:<http://purl.org/spar/doco/>
select distinct ?pmid ?doi ?abstract ?topic
where {
 ?article a bibo:AcademicArticle .
 ?article bibo:pmid ?pmid . 
 ?article bibo:doi ?doi .
 ?article bibo:abstract ?abstract.
 FILTER (regex(?abstract, "cancer", "i")) .
 ?annot a aot:ExactQualifier .
 ?annot ao:annotatesResource ?article .
 ?annot ao:hasTopic ?topic .
 FILTER (regex(str(?topic), "^http://purl.uniprot.org/core/")) .
}
LIMIT 10</textarea>
		<?php
		} else if ($query == 6) { 
		?>
		<textarea rows="12" name="query" id="query" onchange="format_select(this)" onkeyup="format_select(this)">SELECT *
WHERE {
    ?go <http://purl.org/dc/elements/1.1/identifier> ?id .
    FILTER (regex(str(?id), "go:0007049", "i")) .
    ?go rdfs:label ?Label .
    ?go rdfs:comment ?Comment .
  }</textarea>
		<?php
		} else if ($query == 5) { 
		?>
		<textarea rows="12" name="query" id="query" onchange="format_select(this)" onkeyup="format_select(this)">SELECT str(?body) as ?annotatedText ?topic ?text

WHERE {

  ?a a aot:ExactQualifier ;

    ao:annotatesResource <http://biotea.idiginfo.org/pubmedOpenAccess/rdf/PMC2971765.rdf> ;

    ao:body ?body ;

    ao:hasTopic ?topic ;

    ao:context ?context .

  ?context rdfs:resource ?resource  .

  ?resource rdfs:comment ?text .

}</textarea>
		<?php
		} else if ($query == 4) { 
		?>
		<textarea rows="12" name="query" id="query" onchange="format_select(this)" onkeyup="format_select(this)">SELECT str(?body) ?topic 

WHERE {

  ?a a aot:ExactQualifier ;

    ao:annotatesResource <http://biotea.idiginfo.org/pubmedOpenAccess/rdf/PMC3096079.rdf> ;

    ao:body ?body ;

    ao:hasTopic ?topic . 

}</textarea>
		<?php
		} else if ($query == 3) { 
		?>
		<textarea rows="12" name="query" id="query" onchange="format_select(this)" onkeyup="format_select(this)">SELECT ?article

WHERE {

  ?a a aot:ExactQualifier ;

    ao:annotatesResource ?article ;

    ao:hasTopic ?geneTopic .

  FILTER ( 

    ( str(?geneTopic) = "http://ncicb.nci.nih.gov/xml/owl/EVS/Thesaurus.owl#VHL_Gene" ) 

    OR (str(?geneTopic) = "http://ncicb.nci.nih.gov/xml/owl/EVS/Thesaurus.owl#APC_Gene" ) 

  ) .

}</textarea>
		<?php
		} else if ($query == 2) { 
		?>
		<textarea rows="12" name="query" id="query" onchange="format_select(this)" onkeyup="format_select(this)">PREFIX bibo:<http://purl.org/ontology/bibo/>
PREFIX aot:<http://purl.org/ao/types/>
PREFIX ao:<http://purl.org/ao/core/>
PREFIX doco:<http://purl.org/spar/doco/>
PREFIX fn:<http://www.w3.org/2005/xpath-functions#>
select distinct ?pmid ?doi ?abstract ?topic ( fn:concat("GO:", fn:substring(?topic, 31)) AS ?var_concat)
where {
   ?article a bibo:AcademicArticle .
   ?article bibo:pmid ?pmid .
   ?article bibo:doi ?doi .
   ?article bibo:abstract ?abstract.
   FILTER (regex(?abstract, "cancer", "i")) .
   ?annot a aot:ExactQualifier .
   ?annot ao:annotatesResource ?article .
   ?annot ao:hasTopic ?topic .
   FILTER (regex(str(?topic), "^http://purl.org/obo/owl/GO#GO_")) .
}</textarea>
		<?php
		} else { 
		?>
		<textarea rows="12" name="query" id="query" onchange="format_select(this)" onkeyup="format_select(this)">PREFIX bibo:<http://purl.org/ontology/bibo/>
PREFIX aot:<http://purl.org/ao/types/>
PREFIX ao:<http://purl.org/ao/core/>
PREFIX doco:<http://purl.org/spar/doco/>
select distinct ?pmid ?doi ?abstract ?topic
where {
 ?article a bibo:AcademicArticle .
 ?article bibo:pmid ?pmid .
 ?article bibo:doi ?doi .
 ?article bibo:abstract ?abstract.
 FILTER (regex(?abstract, "cancer", "i")) .
 ?annot a aot:ExactQualifier .
 ?annot ao:annotatesResource ?article .
 ?annot ao:hasTopic ?topic .
 FILTER (regex(str(?topic), "^http://purl.uniprot.org/core/")) .
}
LIMIT 10</textarea>
		<?php
		} 
		?>
		
		<label for="default-graph-uri">Default Data Set Name (Graph IRI)</label>
		<input type="text" name="default-graph-uri" id="default-graph-uri" value="" />
		
		<span class="info"><em>(Security restrictions of this server do not allow you to retrieve remote RDF data, see <a href="/sparql?help=enable_sponge">details</a>.)</em></span>
		<label for="format" class="n">Results Format</label>
		<select name="format" id="format" onchange="format_change(this)">
<option value="auto" >Auto</option>
<option value="text/html" selected="selected">HTML</option>
<option value="application/vnd.ms-excel" >Spreadsheet</option>
<option value="application/sparql-results+xml" >XML</option>
<option value="application/sparql-results+json" >JSON</option>
<option value="application/javascript" >Javascript</option>
<option value="text/plain" >NTriples</option>
<option value="application/rdf+xml" >RDF/XML</option>
			    <option value="text/csv">CSV</option>
		</select>
		<label for="timeout" class="n">Execution timeout</label>
		<input name="timeout" id="timeout" type="text" value="0" /> milliseconds
		<span class="info"><em>(values less than 1000 are ignored)</em></span>
		<fieldset id="options">
		<input name="debug" id="debug" type="checkbox" checked="checked"/>
		<label for="debug" class="ckb">Strict checking of void variables</label>
		</fieldset>
		<span class="info"><em>(The result can only be sent back to browser, not saved on the server, see <a href="/sparql?help=enable_det">details</a>)</em></span>
		<br /><input type="submit" value="Run Query"/>
		<input type="reset" value="Reset"/>
	</fieldset>
	</form>
	
			</div>
		<div class="one-third column">
			<h3>Sample Queries</h3>
			<ul class="samples">
			<li><a id="sample1" href="samples.php?sample=1">Sample 1</a></li>
			<li><a id="sample2" href="samples.php?sample=2">Sample 2</a></li>
			</ul>
			<p>Some other queries that would be integrated so a compiled summary would be displayed by a webapp (we are working on a prototype for visualizing this kind of queries).</p>
			<ul class="samples">
			<li><a id="sample3" href="samples.php?sample=3">Sample 3</a></li>
			<li><a id="sample4" href="samples.php?sample=4">Sample 4</a></li>
			<li><a id="sample5" href="samples.php?sample=5">Sample 5</a></li>
			<li><a id="sample6" href="samples.php?sample=6">Sample 6</a></li>
			<li><a id="sample7" href="samples.php?sample=7">Sample 7</a></li>
			<li><a id="sample8" href="samples.php?sample=8">Sample 8</a></li>
			<li><a id="sample9" href="samples.php?sample=9">Sample 9</a></li>
			<li><a id="sample10" href="samples.php?sample=10">Sample 10</a></li>
			</ul>
		</div>

	</div><!-- container -->

	<div id="footer">
	<div class="container">
		<a href="https://www.idiginfo.org" target="_blank"><img src="images/idiginfo.png" alt="iDigInfo" border="0" /></a>
	</div>
	</div>


<!-- End Document
================================================== -->
</body>
</html>