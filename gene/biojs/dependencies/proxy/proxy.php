<?php

function buildUrlString($url,$params) {
	
	$urlString = '';
	$queryString = '';
	$amp = '';
	
	$urlString = $url;
	
	if ( !empty($params) ) {

		reset($params);
		
		while ($element = current($params)) {
			$queryString .= $amp . key($params) .'='. $element;
			$amp = '&';
			next($params);
		}	
		
		if ( strlen($queryString) > 0 ) {
			$urlString .= ( strpos($urlString, '?') ) ? '&'.$queryString : '?'.$queryString ;
		}
		
	}

	return $urlString;
}


/* SPLIT PROXY FROM URL TO QUERY. QUERY: ALL INSIDE THE 'url' PARAMETER */
   $url = ( isset($_POST['url']) ) ? $_POST['url'] : $_GET['url'];

/* SET INTERNAL PROXY */
    $ebiDomainFlag = strpos($_SERVER['SERVER_NAME'], "ebi.ac.uk");
	$sangerDomainFlag = strpos($_SERVER['SERVER_NAME'], "sanger.ac.uk");
        
    if ( $ebiDomainFlag ) {
    	$proxy = "http://wwwcache.ebi.ac.uk:3128/";
    } elseif ( $sangerDomainFlag ) {
        $proxy = "http://wwwcache.sanger.ac.uk:3128/";
    } else {
		$proxy = "";
	}
	
	$ch = curl_init();
		
/* SET DATA */ 
	if ( $_SERVER['REQUEST_METHOD']==='POST' ) {

		$rawdata = file_get_contents('php://input');

		if ( strlen($rawdata) > 0 ) {
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $rawdata);
			
		} else {
			$data = $_POST;
			unset($data['url']);
			$url = buildUrlString($url,$data);
		}

		curl_setopt ($ch, CURLOPT_POST, true);
		
		if ( isset ($_SERVER['CONTENT_TYPE']) ) {
			curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Content-Type: " . $_SERVER['CONTENT_TYPE'] ) );
		}

 	} else {
		
 		$data = $_GET;
 		unset($data['url']);
 		$url = buildUrlString($url,$data);
		
 		curl_setopt ($ch, CURLOPT_POST, false);
 	}

/* Encoding charachters which are probelmatic for CURL */
	$url = str_replace('<','%3C',$url); 
	$url = str_replace('>','%3E',$url); 
	$url = str_replace('"','%22',$url); 
	$url = str_replace(' ','%20',$url); 


/* CURL CONFIGURARTION */     
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 50);
   curl_setopt($ch, CURLOPT_TIMEOUT, 50);
   curl_setopt($ch, CURLOPT_HEADER, 0);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_VERBOSE, 0);
		
   if( strlen($proxy) != 0 ){
      curl_setopt($ch, CURLOPT_PROXY, $proxy);
   }
   
   // Get the response from the server
   $response = curl_exec($ch);
   //$response = print_r($_SERVER, true);
   
   curl_close($ch);
		
/* WRITE HEADERS FOR THE RESPONSE */
   
	$mimeType =($_POST['mimeType']) ? $_POST['mimeType'] : $_GET['mimeType'];
	
	if ( strlen($mimeType) == 0 )
	{
		if ( strpos($_SERVER['HTTP_ACCEPT'], "xml") ) {
			$mimeType = "application/xml";
		} else if ( strpos($_SERVER['HTTP_ACCEPT'], "json") ) {
			$mimeType = "application/json";
		} else {
			$mimeType = "text/plain";
		}
	} 
	//Set the Content-Type appropriately
	header("Content-Type: ".$mimeType);
	
/* WRITE THE RESPONSE */
    echo $response;
?>
