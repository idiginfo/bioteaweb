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

<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>

	<!-- Basic Page Needs
  ================================================== -->
	<meta charset="utf-8">
	<title>bioteà - biomedical annotation : Create a bioteà mirror</title>
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
	
	
	
    

</head>
<body>



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
			<h3>Virtuoso Database Creation from RDF</h3>
			
			<br />
			 

<p><strong>1: Configure virtuoso.ini as appropriate for OS, file system & directory environment.</strong>
<br />Note url & port to be used for web access, and location of database directory.</p>
<p>Ubuntu specific example: </p>

<pre>Primary .ini configuration file: /etc/virtuoso-opensource-6.1/virtuoso.ini
Debian-style parameter file: /etc/default/virtuoso-opensource-6.1 
Default database directory: /var/lib/virtuoso-opensource-6.1/db/</pre>


<p><strong>2: Begin with an non-existant virtuoso.db, start the server</strong>
<br />(Ubuntu example: "/etc/init.d/virtuoso-opensource-6.1 start")
and allow it to generate an empty virtuoso.db database;
<br /><em>or</em>
<br />place an archived copy of an empty database in the .ini referenced database directory, and start the server.</p>

<p><strong>3: Validate the virtuoso server</strong>
<br />verify running process, and
<br />review virtuoso.log (in database directory) to ensure it is online without errors.</p>

<p><strong>4: Launch a web browser & navigate to URL/conductor page.</strong>
<br />Log in as "dba" (data base administrator) - default password is "dba" for new instance.
<br />Verify configuration: Conductor/System Admin/Parameters
<br />- Database File locations: /Database
<br />- Temp Database location: /Tempdatabase
<br />- Runtime parameters: /Parameters (note value of ServerPort for programmatic URL access)
<br />- Web access: /HTTPServer (note value of ServerPort for Browser access)</p>

<p>Implement Security model:
<br />Conductor/System Admin/User Accounts
<br />- Edit users "dba" and "dav", changing each default password ("dba" & "dav") to a strong value.
<br />- Use "Create New Account" to generate additional Users (if needed)</p>

<p><em>Note: Administrative capable accounts require:
<br />User type = "SQL/ODBC and WebDAV"
<br />Primary Role = "dba"
<br />Quota = "unlimited"</em></p>

<p><strong>5: Namespace Configuration:</strong>
<br />While logged in as "dba" - Conductor/Linked Data/Namespaces
<br />Verify existing entries against following list.
<br />Insert or replace the following required additional Namespace Prefix/URI pairs:
<br />New: enter values, click "Add New" button
<br />Replace: Click Action/Delete button to the right of existing entry, then Add New.</p>
<pre>   Prefix:        URI:
   ao             http://purl.org/ao/core/
   aoa            http://purl.org/ao/annotea/
   aof            http://purl.org/ao/foaf/
   aold           http://biotea.ws/ontologies/aold/
   aos            http://purl.org/ao/selectors/
   aot            http://purl.org/ao/types/
   bibo           http://purl.org/ontology/bibo/
   bio2rdf_mesh   http://bio2rdf.org/ns/mesh#
   bio2rdf_ns     http://bio2rdf.org/ns/bio2rdf#
   chebi          http://purl.obolibrary.org/obo/CHEBI_
   dc             http://purl.org/dc/elements/1.1/
   dcterms        http://purl.org/dc/terms/
   doco           http://purl.org/spar/doco/
   fma            http://purl.org/obo/owl/FMA#FMA_
   foaf           http://xmlns.com/foaf/0.1/
   go             http://purl.org/obo/owl/GO#GO_
   gw_property    http://genewikiplus.org/wiki/Special:URIResolver/Property-3A
   gw_wiki        http://genewikiplus.org/wiki/Special:URIResolver/
   icd9           http://purl.bioontology.org/ontology/ICD9-9/
   mddb           http://purl.bioontology.org/ontology/MDDB/
   meddra         http://purl.bioontology.org/ontology/MDR/
   medline        http://purl.bioontology.org/ontology/MEDLINEPLUS/
   mged           http://mged.sourceforge.net/ontologies/MGEDOntology.owl#
   ncbitaxon      http://purl.org/obo/owl/NCBITaxon#NCBITaxon_
   ncithesaurus   http://ncicb.nci.nih.gov/xml/owl/EVS/Thesaurus.owl#
   nddf           http://purl.bioontology.org/ontology/NDDF/
   ndfrt          http://purl.bioontology.org/ontology/NDFRT/
   obi            http://purl.obolibrary.org/obo/OBI_
   omim           http://purl.bioontology.org/ontology/OMIM/
   owl            http://www.w3.org/2002/07/owl#
   pav            http://purl.org/swan/pav/provenance/
   po             http://purl.bioontology.org/ontology/PO/
   pw             http://purl.org/obo/owl/PW#PW_
   rdf            http://www.w3.org/1999/02/22-rdf-syntax-ns#
   rdfs           http://www.w3.org/2000/01/rdf-schema#
   sioc           http://rdfs.org/sioc/ns#
   snomed         http://purl.bioontology.org/ontology/SNOMEDCT/
   symptom        http://purl.org/obo/owl/SYMP#SYMP_
   taxonomy       http://www.uniprot.org/taxonomy/
   umls           http://berkeleybop.org/obo/UMLS:
   uniprot        http://purl.uniprot.org/core/
   xsp            http://www.owl-ontologies.com/2005/08/07/xsp.owl#</pre>

<p><strong>6: Graph Loading from pre-prepared .rdf files:</strong>
<br />Utilizing ld2rdf application:
<br />- Configure ld2rdf application environment processing directories & files:</p>
<pre>   - Parent directory:
     - biotea-ld2rdf.jar    -> executable java library
     - config.properties    -> runtime configuration file
            - whatizit.wsdl.dir=&lt;full path to whatizit.wsdl file&gt;
            - biotea.base=&lt;URI of virtuoso server&gt;
            - ld.rdf.graph.pubmedOpenAccess=&lt;URL to primary graph&gt;
            - ld.rdf_ao.graph.pubmedOpenAccess=&lt;URL to annotations graph&gt;
            - ld.bio2rdf.graph.pubmedOpenAccess=&lt;URL to bio2rdf graph&gt;
            - virtuoso.sparql=&lt;URL of SPARQL endpoint&gt;
            - virtuoso.url=&lt;URL of Server Port&gt;
            - virtuoso.user=&lt;username&gt;
            - virtuoso.passwd=&lt;password&gt;</pre>
<p><em>Ubuntu example:</em></p>
<pre>whatizit.wsdl.dir=/data/diskarray2/MSRC_RDF-PubMed/process/
biotea.base=biotea.idiginfo.org
ld.rdf.graph.pubmedOpenAccess=http://biotea.idiginfo.org/pmc/rdf/rdfPubmedOA
ld.rdf_ao.graph.pubmedOpenAccess=http://biotea.idifinfo.org/pmc_AO/rdf/rdfPubmedOAAO
ld.bio2rdf.graph.pubmedOpenAccess=http://biotea.idiginfo.org/pmc_bio2rdf/rdf/rdfPubmedOABio2RDF
virtuoso.sparql=http://virtuoso.idiginfo.org:8890/sparql
virtuoso.url=jdbc:virtuoso://virtuoso.idiginfo.org::1111/charset=UTF-8/log_enable=2
virtuoso.user=dba
virtuoso.passwd=.........</pre>

<pre>     - log4j.properties     -> logging configuration file
                               - log4j.appender.logfile.File=&lt;local path to and name of 
                                 log file&gt;
     - server-bindings.xml  -> bindings configuration file for whatizitws.client
     - whatizit.wsdl        -> wsdl file for whatizit client
   - Subdirectories:
     - inputdir             -> Directory of .nxml files if generating .rdf, and Support 
                               files & directories
     - logs                 -> Directory of log file(s)
     - outputdir            -> Directory of Primary .rdf files
       AO_annotations       -> Sub-directory of Annotations .rdf files
       Bio2RDF              -> Sub-directory of Bio2rdf .rdf files
   - Support files & dirs   -> Required by ld2rdf process to be in the inputdir:
                               annotation3.ent
                               archivearticle.dtd
                               archivearticle3.dtd
                               archivecustom-classes3.ent
                               archivecustom-mixes3.ent
                               archivecustom-models3.ent
                               archivecustom-modules3.ent
                               articlemeta3.ent
                               backmatter3.ent
                               base-test3.dtd
                               catalog-v3.xml
                               catalog.ent
                               chars3.ent
                               common3.ent
                               default-classes3.ent
                               default-mixes3.ent
                               display3.ent
                               format3.ent
                               funding3.ent
                               htmltable.dtd
                               iso8879               &lt;DIR&gt;
                               iso9573-13            &lt;DIR&gt;
                               journalmeta3.ent
                               link3.ent
                               list3.ent
                               math3.ent
                               mathml                &lt;DIR&gt;
                               mathml2-qname-1.mod
                               mathml2.dtd
                               mathmlsetup3.ent
                               modules3.ent
                               nlmcitation3.ent
                               notat3.ent
                               para3.ent
                               phrase3.ent
                               references3.ent
                               related-object3.ent
                               section3.ent
                               Smallsamples          &lt;DIR&gt;
                               xhtml-inlstyle-1.mod
                               xhtml-table-1.mod
                               XHTMLtablesetup3.ent
                               xmlchars              &lt;DIR&gt;
                               xmlspecchars3.ent</pre>
<p>- Place .rdf files in <output dir name> directory
<br />- From the Parent directory, execute: 
<pre>java -jar biotea_ld2rdf.jar -in &lt;input dir name&gt; -out &lt;output dir name&gt; -onlyUpload 
-initPool 1 -maxPool 1</pre>
<br />as an independent process (screen / nohup / etc).</p>

<p><em>Ubuntu example:</em></p>
<pre>java -jar biotea_ld2rdf.jar -in ./inputdir -out ./outputdir -onlyUpload -initPool 1 
-maxPool 1</pre>
<p>- Monitor the progress via the log file (location defined in log4j.properties file)</p>

<p><em>or</em></p>

<p>Refer to <a href="http://docs.openlinksw.com/virtuoso/rdfinsertmethods.html" target="_blank">http://docs.openlinksw.com/virtuoso/rdfinsertmethods.html</a> for additional methods.

	
			</div>
		<div class="one-third column">
			
			<h3 class="space"><br />&nbsp;</h3>
			<br />
			

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