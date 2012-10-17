<?php
$vars = array();
$vars[0]['sparql'] = TRUE;
include_once('_controller.php');
doHeader($vars);

?>



	<div class="container main">
		<div class="two-thirds column">
			<h3>SPARQL Query Editor</h3>
			<form action="http://biotea.idiginfo.org/sparql" method="get">
	<fieldset>
		<label for="default-graph-uri">Default Data Set Name (Graph IRI)</label>
		<input type="text" name="default-graph-uri" id="default-graph-uri" value="" />
		<label for="query">Query Text</label>
		<textarea rows="6" name="query" id="query" onchange="format_select(this)" onkeyup="format_select(this)">select distinct ?Concept where {[] a ?Concept} LIMIT 100</textarea>
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
			<h3>About this query</h3>
			<p>This SPARQL form will query a sample set of records (not the entire set), and is intended only as an example of what information is available in the data.</p>

            <p>The sample set in the SPARQL endpoint contains only 10,000 records. The full set of records can be <a href="mirror.php">downloaded as a set of ZIP files</a> or queried through our <a href="api.php">Web Services API</a>.</p>

		</div>

	</div><!-- container -->

<?php doFooter(); ?>