<?php
	@$serv = $_POST['server']; 
	@$name = $_POST['name'];
	@$pw = $_POST['pw'];
	if((isset($serv))&&(isset($name))&&(isset($pw))){	
		$Connection = mysql_connect($serv,$name,$pw) or die ("Verbindung fehlgeschlagen");
		//--------Ermittlung aller Datumseintragungen im angegebenen Zeitraum
		$SQLString =  "use changedb";
		$Ergebnis = mysql_query($SQLString,$Connection);
		$SQLString =  "select date_change from difference group by date_change;";
		$Ergebnis = mysql_query($SQLString,$Connection);
		for($i=0;$row = mysql_fetch_array($Ergebnis);$i++){
			$date[$i] = $row[0];
		}
		//MySQL-Verbindung trennen
		mysql_close($Connection);
		$url = 'difference';
		$val = 'Vergleichen';
	}
	else{
		$url = 'vor_vergleich';
		$val = 'Anmelden';
	}
?>
<html>
	<head>
		<title>
			Vergleich von Daten
		</title>
		<link rel="stylesheet" type="text/css" href="formate.css">
		<!-- Weiterleitung zur Eingabe bei Klick auf Botton -->
		<script type="text/javascript">
			function vergleich(){
				window.location.href='vor_vergleich.php';
			}
		</script>
	</head>
	<body>
		<!-- Überschrift -->
		<header>
			Vergleich
		</header>
		<div id="date">
			Datenbank-Login:
		</div>
		<div id="vergleich">
			<form id="vergleich_form" name="eingabe" method="post" action=<?php echo $url.'.php';?>>
				<!-- Beschriftung der Inputfelder -->
				<div id="vergleich_datum">
					<input type="text" placeholder="Server" name="server" value='<?php echo $serv;?>'>
					<input type="text" placeholder="Benutzername" name="name" value='<?php echo $name;?>'>
					<input type="password" placeholder="Passwort" name="pw" value='<?php echo $pw;?>'>			
					<!-- Selctboxen zur Auswahl des Start- und Enddatums -->
					<select id="datum" name="start">
						<?php
							if((isset($serv))&&(isset($name))&&(isset($pw))){	
								for($i=0;$i<count($date);$i++){
									echo"<option value='".$date[$i]."'>".$date[$i]."</option>";
								}
							}	
						?>
					</select>					
					<select id="datum" name="end">
						<?php
							if((isset($serv))&&(isset($name))&&(isset($pw))){	
								for($i=0;$i<count($date);$i++){
									echo"<option value='".$date[$i]."'>".$date[$i]."</option>";
								}
							}	
						?>	
					</select>					
					<input type="submit" value="<?php echo $val;?>" name="Los">
				</div>
			</form>
		</div>		
	</body>
</html>	