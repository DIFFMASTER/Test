<?php
//--------Variablen die Aus dem Formular: eingabe.html übertragen werden
	$serv = $_POST['Server'];
	$name = $_POST['Name'];
	$pw = $_POST['PW'];
	$db = $_POST['DB'];
	
//MySQL-Verbindung
	$Connection = mysql_connect($serv,$name,$pw) or die ("Verbindung fehlgeschlagen");
	
//Eingegebene Datenbank und Tabellen werden ermittelt
	$SQLStringDB =  "use ".$db.";";
	$ErgebnisDB = mysql_query($SQLStringDB,$Connection);
	$SQLStringFelder =  "show tables from ".$db.";";
	$ErgebnisFelder = mysql_query($SQLStringFelder,$Connection);

//Eingegebene Datenbank wird übergeben, Felder werden übermittelt	
	while($tab = mysql_fetch_row($ErgebnisFelder)){
		$SQLStringDB =  "use ".$db.";";
		$ErgebnisDB = mysql_query($SQLStringDB,$Connection);
		$SQLStringTabelle = "show fields from ".$tab[0].";";
		$ErgebnisTabelle = mysql_query($SQLStringTabelle,$Connection);

	
//Datenbank-Wechsel, Daten werden in Gesamttabelle schreiben	
			while($row = mysql_fetch_row($ErgebnisTabelle)){
				$SQLStringDB =  "use changedb;";
				$ErgebnisDB = mysql_query($SQLStringDB,$Connection);
				$SQLStringEinfuegen = "INSERT INTO difference VALUES ('',CURDATE(), '".$tab[0]."', '".$row[0]."', '".$row[1]."', '".$row[2]."', '".$row[3]."', '".$row[4]."', '".$row[5]."');";
				$ErgebnisEinfuegen = mysql_query($SQLStringEinfuegen,$Connection);
			}
		
	}	
//MySQL-Verbindung trennen
	mysql_close($Connection);
?>

<html>
	<head>
		<title>
			DB Sicherung
		</title>
		<!-- Weiterleitung zur Eingabe bei Klick auf Botton -->
		<script type="text/javascript">
			function erneut(){
				window.location.href='eingabe.html';
			}
		</script>
		<link rel="stylesheet" type="text/css" href="formate.css">
	</head>
	<body>	
		<input type="button" value="Andere Datenbank sichern?" name="erneut" onClick="erneut()">	
	</body>
</html>	




