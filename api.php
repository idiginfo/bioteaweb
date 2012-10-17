<?php

#header("Location: ./api");


include_once('./_controller.php');
doHeader();

?>



	<div class="container main">
		<div class="two-thirds column">
			<h3>API</h3>
			
			<p>The Biotea API is a service that provides access to the collection through a REST Web Services interface.  The interface provides speedy access to the collection through common queries that have been indexed in MySQL and abstracted behind the REST endpoints.</p>

			<p>Clients can access the API at <a href="http://biotea.idiginfo.org/api">http://biotea.idiginfo.org/api</a>.</p>

<p>Currently, the following use cases are supported by the API:</p>
<ol>
	<li>Retrieve list of RDF documents, and their annotation files</li>
	<li>Retrieve RDF for a single document or annotation file</li>
	<li>Retrieve metadata about each document:
	<ol type="a">
	    <li>Included terms</li>
	    <li>Ontological vocabularies used to identify terms</li>
	    <li>Ontological topics used to identify terms</li>
	</ol></li>
	<li>Retrieve aggregate data for terms, vocabularies, and topics:
	<ol type="a">
	    <li>Documents associated with term, vocabulary, or topic</li>
	    <li>Terms associated with topics or vocabularies</li>
	    <li>Topics associated with terms or vocabularies</li>
	    <li>Vocabularies associated with terms or topics</li>
	</ol></li>
	<li>Retrieve meta-data about the collection</li>
	<ol type="a">
	    <li>Number of documents, terms, topics, and vocabularies indexed in the collection.</li>
	</ol></li>
</ol>


<h5>Example Queries:</h5>

<p><span class="api-fixed"><a href="http://biotea.idiginfo.org/api/stats" target="_blank">http://biotea.idiginfo.org/api/stats</a> </span>			-- Get stats about a document</p>
<p><span class="api-fixed"><a href="http://biotea.idiginfo.org/api/terms?topics=1" target="_blank">http://biotea.idiginfo.org/api/terms?topics=1</a></span>			-- Get a list of terms and their topics</p>
<p><span class="api-fixed"><a href="http://biotea.idiginfo.org/api/topics?page=2" target="_blank">http://biotea.idiginfo.org/api/topics?page=2</a></span>			-- Get a list of topics</p>
<p><span class="api-fixed"><a href="http://biotea.idiginfo.org/api/documents/PMC_____" target="_blank">http://biotea.idiginfo.org/api/documents/PMC_____</a></span>		-- Get information about a document</p>

<h5>Basic usage:</h5>

<p><span class="api-code">/	</span>				Get information about the API</p>

<p><span class="api-code">/stats	</span>				Get statistics about the API</p>

<p><span class="api-code">/documents	</span>				Retrieve a paginated list of documents</p>
<ul><li class="options">Optional Parameters			
    <ul>
    <li><span class="api-code">page</span>			Select a page in the resultset</li>
    </ul>
    </li>
</ul>

<p><span class="api-code">/documents/{docFileName}</span>		Retrieve all information about a single </p>document

<p><span class="api-code">/terms 		</span>			Retrieve a paginated list of terms</p>
<ul><li class="options">Optional Parameters			
    <ul>
    <li><span class="api-code">page</span>			Select a page in the resultset</li>
    <li><span class="api-code">prefix</span>				Only return terms that start with a specified prefix</li>
    <li><span class="api-code">topics</span>				Set to ‘1’ to also include list of topics per term</li>
    </ul>
    </li>
</ul>

<p><span class="api-code">/terms/{term}</span>				Retrieve all information about a single term</p>

<p><span class="api-code">/topics </span>					Retrieve a paginated list of topics</p>
<ul><li class="options">Optional Parameters			
    <ul>
    <li><span class="api-code">page</span>			Select a page in the resultset</li>
    <li><span class="api-code">vocabulary</span>		Only return topics related to a specific vocabulary</li>
    <li><span class="api-code">terms</span>			Set to ‘1’ to also include list of associated terms per topic</li>
    </ul>
    </li>
</ul>

<p><span class="api-code">/topics/{uri or id}	</span>		Retrieve information about a topic (id refers to index id)</p>

<p><span class="api-code">/vocabularies</span>				Retrieve a paginated list of vocabularies</p>
<ul><li class="options">Optional Parameters			
    <ul>
    <li><span class="api-code">page</span>			Select a page in the resultset</li>
    <li><span class="api-code">topics</span>			Set to ‘1’ to also include list of topics per vocabulary</li>
    </ul>
    </li>
</ul>

<p><span class="api-code" style="width: 300px;">/vocabularies/{uri or shortName}</span>		Retrieve information about a vocabulary</p>

<p><strong>Parameters Available to All Requests</strong></p>
<p><span class="api-code">format</span>
Can be “json”, “html”, or “xml”.</p>
<p>The preferred way to specify the content format that you wish to receive is in the Content-Type HTTP header.  If you add the “format” parameter to the query string, it will override that negotiation, and can be used to force the server to return a specific content type regardless of HTTP headers.</p>

			
	
			</div>
		<div class="one-third column">
			<h3 class="about">About biote&agrave;</h3>
			
			
		</div>

	</div><!-- container -->

<?php doFooter(); ?>