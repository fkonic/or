<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title> UNESCO baština</title>
	<link rel="stylesheet" href="dizajn.css" />
</head>

<body>
	<header>
        <div class="logo">
            <a href="index.html"><img src="images/world_heritage_logo.png" alt="UNESCO logo" /></a>
        </div>
        <div class="naslov">
            <h1 class="naslov">Mjesta UNESCO-ove baštine u Hrvatskoj</h1>
        </div>      
    </header>

	<nav>
        <ul id="izbornik">
            <li><a href="index.html">Naslovna</a></li>       
            <li><a href="obrazac.html">Pretraživanje</a></li>
            <li><a class="active" href="podaci.xml">Mjesta baštine</a></li>    
            <li><a href="http://www.fer.unizg.hr/predmet/or">Otvoreno računarstvo</a></li>                 
            <li><a href="http://www.fer.unizg.hr" target="_blank">FER</a></li> 
            <li><a href="mailto:filip.konic@fer.hr">e-mail kontakt</a></li>
        </ul>
    </nav>

	<main>
        <h2>Lista baštine u RH</h2>
		<table>
			<tr>
				<th>Naziv</th>
				<th>Kriteriji</th>
				<th>Lokacija</th>
				<th>Godina</th>
				<th>Površina</th>
				<th>Ostalo</th>
			</tr>
			<xsl:for-each select="/mjesta-bastine/bastina">
				<xsl:sort select="godina" />
				<tr>
					<td><xsl:value-of select="naziv" />
						<xsl:if test="cjelina "> <br />
							dio: <em><xsl:value-of select="cjelina/naziv" /> </em>
						</xsl:if>
					</td>
					<td><xsl:for-each select="kriterij">
							<xsl:value-of select="." /><br />
						</xsl:for-each>
					</td>
					<td>
						<xsl:if test="lokacija/adresa">
							<xsl:value-of select="lokacija/adresa/ulica" />&#160;
							<xsl:value-of select="lokacija/adresa/kbr" /><br />
						</xsl:if> 
						<xsl:value-of select="lokacija/grad" />, 
						<xsl:value-of select="lokacija/zupanija" /><br />
					</td>
					<td>
						<xsl:value-of select="godina" />.<br />
					</td>
					<td>
						<xsl:value-of select="povrsina" /><br />
					</td>
					<td>
						UNESCO id: <xsl:value-of select="@id" /> <br/>
						Vrsta: <xsl:value-of select="@vrsta" /><br />
					</td>
				</tr>
			</xsl:for-each>
		</table>
	</main>
	<footer>
		<p>© Filip Konić 2020. Sva prava pridržana.</p>
	</footer>
</body>

</html>
</xsl:template>
</xsl:stylesheet>