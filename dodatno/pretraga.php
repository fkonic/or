<?php
	error_reporting (E_ALL);
	include_once ('funkcije.php');
	
	$dom = new DOMDocument();
  	$dom->load('podaci.xml');

  	$xp = new DOMXPath($dom);
	$xp->registerNamespace('php', 'http://php.net/xpath');
	$xp->registerPHPFunctions();

  	$query = createQuery();
	//print_r($query);
  	$queryResult = $xp->query($query);
?>
<!DOCTYPE html>
<html>
<head>   
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <title> UNESCO baština</title>
    <link rel="stylesheet" href="dizajn.css">
</head>

<body>
    <header>
        <div class="logo">
            <a href="index.html"><img src="images/world_heritage_logo.png" alt="UNESCO logo"></a>
        </div>
        <div class="naslov">
            <h1 class="naslov">Mjesta UNESCO-ove baštine u Hrvatskoj</h1>
        </div>      
    </header>
    
    <nav>
        <ul id="izbornik">
            <li><a href="index.html">Naslovna</a></li>       
            <li><a class="active" href="obrazac.html">Pretraživanje</a></li>
            <li><a href="podaci.xml">Mjesta baštine</a></li>     
            <li><a href="http://www.fer.unizg.hr/predmet/or">Otvoreno računarstvo</a></li>                 
            <li><a href="http://www.fer.unizg.hr" target="_blank">FER</a></li> 
            <li><a href="mailto:filip.konic@fer.hr">e-mail kontakt</a></li>
        </ul>
    </nav>
	
	<main>
        <h2>Rezultati pretrage</h2>
		
		<table>
			<tr>
				<th>Slika</th>
				<th>Naziv</th>
				<th>Kriteriji</th>
				<th>Lokacija</th>
				<th>Godina</th>
				<th>Površina</th>
				<th>Ostalo</th>
				<th>Koordinate WikiMedia</th>
				<th style="width:10em">Koordinate Nominatim</th>
				<th style="width:15em">Info</th>
			</tr>
			<?php
				foreach($queryResult as $element) {
					$json = Wikimedia($element->getElementsByTagName('wiki-title')->item(0)->nodeValue);
					
			?>
			<tr>
				<td style="text-align:center">
					<img src="<?php echo wikiImage($json);?>" width="400"/>
				</td>
				<td>
					<?php // var_dump($element);
					echo $element->getElementsByTagName('naziv')->item(0)->nodeValue; ?>
					<?php if (!empty($element->getElementsByTagName('cjelina')->item(0))){
						echo '<br />
						dio: <em>' . $element->getElementsByTagName('naziv')->item(1)->nodeValue . '</em>';
					} 
					//var_dump($element->getElementsByTagName('naziv'));?>
				</td>
				<td>
					<?php foreach ($element->getElementsByTagName('kriterij') as $kriterij){
						echo $kriterij->nodeValue. '</br>';
					} ?>
				</td>
				<td>
					<?php $adresa = $element->getElementsByTagName('adresa')->item(0); 
					if (!empty($adresa)){
						echo $adresa->getElementsByTagName('ulica')->item(0)->nodeValue . ' ' . $adresa->getElementsByTagName('kbr')->item(0)->nodeValue . '<br/>';
					}
					echo $element->getElementsByTagName('grad')->item(0)->nodeValue . ', ' . $element->getElementsByTagName('zupanija')->item(0)->nodeValue;
					?>
				</td>
				<td>
					<?php echo $element->getElementsByTagName('godina')->item(0)->nodeValue . '.'; ?>
				</td>
				<td>
					<?php $povrsina = $element->getElementsByTagName('povrsina')->item(0); 
					if (!empty($povrsina)){
						echo $povrsina->nodeValue;
					}
					?>
				</td>
				<td>
					UNESCO id: <?php echo $element->getAttribute('id'); ?> <br/>
					Vrsta: <?php echo $element->getAttribute('vrsta'); ?> <br/>
				</td>
				<td>
					<?php echo wikiCoord($json)[0] . ", " . wikiCoord($json)[1]; ?>
				</td>
				<td>
					<?php $media = MediaWiki($element->getElementsByTagName('wiki-title')->item(0)->nodeValue);
					$lokacija = getLocation($media);
					// echo $lokacija;
					$xml = nominatim($lokacija);
					$tree = simplexml_load_string($xml) or die ("Error: Cannot create object");
					if(isset($tree->place)){
						echo $tree->place["lat"] . ", " . $tree->place["lon"];
					}
					else {
						echo "Nominatim didn't return coordinates.";
					}
					?>
				</td>
				<td>
					<?php $str = wikiExtract($json);
					echo get_Type($media) . "</br>";
					$str = explode ("\n", wordwrap($str, 200, "\n"), 2);
					echo $str[0].(isset($str[1])? '…' : '');
					?>
				</td>
			</tr>
		<?php
			}
		?>
		</table>
	<?php
	
	?>
	</main>
	
	<footer>
        <p>© Filip Konić 2020. Sva prava pridržana.</p>
    </footer>
	
</body>
</html>