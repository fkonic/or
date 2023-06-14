<?php
function createQuery(){
	$query = array();
	
	if (!empty($_GET['UNESCO_ID']))
		$query[] = "@id='" . $_GET['UNESCO_ID'] . "'";
	
	if (!empty($_GET['naziv']))
		$query[] = "contains(php:functionString('mb_strtolower', naziv ), '" . mb_strtolower($_GET['naziv'], "UTF-8") . "')";
	
	if (!empty($_GET['wikipedia_title']))
		$query[] = "contains(php:functionString('mb_strtolower', wiki-title ), '" . mb_strtolower($_GET['wikipedia_title'], "UTF-8") . "')";
	
	if (!empty($_GET['dio_cjeline']))
		$query[] = ($_GET['dio_cjeline'] == 'Da') ? "cjelina" : "not(cjelina)";

	if (!empty($_GET['naziv_cjeline']))
		$query[] = "contains(php:functionString('mb_strtolower', cjelina/naziv ), '" . mb_strtolower($_GET['naziv_cjeline'], "UTF-8") . "')";
	
	if (!empty($_GET['vrsta']))
		$query[] = "@vrsta='" . $_GET['vrsta'] . "'";
	
	if (!empty($_GET['kriteriji'])){
		$fragment = array();
		foreach($_GET['kriteriji'] as $dio){
			$fragment[] = "kriterij='" . $dio . "'";
		}
		$query[] = "(" . implode (" or ", $fragment) . ")";
	}
	
	if (!empty($_GET['zupanija'])){
		$fragment = array();
		foreach($_GET['zupanija'] as $dio){
			$fragment[] = "lokacija/zupanija='" . $dio . "'";
		}
		$query[] = "(" . implode (" or ", $fragment) . ")";
	}
	
	if (!empty($_GET['grad']))
		$query[] = "contains(php:functionString('mb_strtolower', lokacija/grad ), '" . mb_strtolower($_GET['grad'], "UTF-8") . "')";
	
	if (!empty($_GET['ulica']))
		$query[] = "contains(php:functionString('mb_strtolower', lokacija/adresa/ulica ), '" . mb_strtolower($_GET['ulica'], "UTF-8") . "')";
	
	if (!empty($_GET['kucni_broj']))
		$query[] = "lokacija/adresa/kbr=" . $_GET['kucni_broj'];
	
	if (!empty($_GET['GPS']))
		$query[] = "contains(php:functionString('mb_strtolower', lokacija/GPS_koor ), '" . mb_strtolower($_GET['GPS'], "UTF-8") . "')";
	
	if (!empty($_GET['ugrozenost']))
		$query[] = "@ugrozenost='" . $_GET['ugrozenost'] . "'";
	
	if (!empty($_GET['godina']))
		$query[] = "godina=" . $_GET['godina'];
	
	if (!empty($_GET['povrsina']))
		$query[] = "contains(php:functionString('mb_strtolower', povrsina ), '" . mb_strtolower($_GET['povrsina'], "UTF-8") . "')";

	$xpathQuery = implode(" and ", $query);
	

	if(!empty($xpathQuery))
		return "/mjesta-bastine/bastina[" . $xpathQuery . "]";

	else
		return "/mjesta-bastine/bastina";
}

function Wikimedia($wikiID){
	$options = array('http' => array('user_agent' => 'filip.konic@fer.hr'));
	$context = stream_context_create($options);
	$wikiREST = file_get_contents("https://en.wikipedia.org/api/rest_v1/page/summary/" . $wikiID, false, $context);
	return $decode = json_decode($wikiREST, true);
}

function wikiImage($json){
	if (isset($json["originalimage"]["source"])){
		return $json["originalimage"]["source"];
	}
}

function wikiCoord($json){
	if (isset($json["coordinates"])){
		$coord = array();
		$coord[] = $json["coordinates"]["lat"];
		$coord[] = $json["coordinates"]["lon"];
		return $coord;
	}
}

function wikiExtract($json){
	if (isset($json["extract"])){
		return $json["extract"];
	}
}

function MediaWiki($wikiID){
	// $mediaJSON = json_decode(file_get_contents("https://en.wikipedia.org/w/api.php?action=query&prop=revisions&rvprop=content&rvsection=0&format=json&titles=" . urlencode($wikiID)), true);
	// return $mediaJSON ["query"]["pages"];
	$mediaJSON = file_get_contents("https://en.wikipedia.org/w/api.php?action=query&prop=revisions&rvprop=content&rvsection=0&format=json&titles=" . urlencode($wikiID));
	return $mediaJSON;
}

function getLocation($media){
	preg_match_all("/location\s?=\s?((?:\w+\s+)*\d)?(?:,<br>)?((?:\[\[(?:.*?)\]\](?:,\s)?)*)/", $media, $out);
	// preg_match_all("/location\s*=\s*((?:\w+\s+)*\d)?(?:,<br>)?((?:\[\[.*?\\n))/", $media, $out);
	if(isset($out[0][0])){
		$adresa = $out[1][0];
		$lokacija = explode(", ",  json_decode('"'. $out[2][0] .'"'));
		foreach ($lokacija as $str){
			$lokacija = preg_replace("/\[|\]|\\n/", "", $lokacija);
		}
		$lokacija = implode(", ", array($adresa, implode(", ", $lokacija)));
		return $lokacija;
	}
	else {
		echo "MediaWiki Action API doesn't have anything more specific than a wide area (county) as the location.\n";
	}
}

function nominatim($lokacija){
	$options = array('http' => array('user_agent' => 'OR'));
	$context = stream_context_create($options);
	// echo "https://nominatim.openstreetmap.org/search?q=" . urlencode($lokacija) . "&format=xml";
	return $xml = file_get_contents("https://nominatim.openstreetmap.org/search?q=" . urlencode($lokacija) . "&format=xml", false, $context);
}
?>