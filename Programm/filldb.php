<?php
//-----------------------------------------------------------------------------------------------------------------------------
//Short description: safe database changes

//Project: Diffmaster

//Developer: Philipp Schmitt, Philipp Fuchs, Jessica Wifling, Gina Weiland, Alexander Trierweiler, Steve Bone

//Description: 
//This script saves the database changes of the choosen database in teh difference table.
//It uses a SQL query to do so.
//-----------------------------------------------------------------------------------------------------------------------------

//--------Variables of the POST transmission from eingabe.html
	$serv = $_POST['Server'];
	$name = $_POST['Name'];
	$pw = $_POST['PW'];
	$db = $_POST['DB'];
	
//MySQL-connection
	$Connection = mysql_connect($serv,$name,$pw) or die ("Verbindung fehlgeschlagen");
	
//fetch choosen database and tables
	$SQLStringDB =  "use ".$db.";";
	$ErgebnisDB = mysql_query($SQLStringDB,$Connection);
	$SQLStringFelder =  "show tables from ".$db.";";
	$ErgebnisFelder = mysql_query($SQLStringFelder,$Connection);

//get fields of the table in the database	
	while($tab = mysql_fetch_row($ErgebnisFelder)){
		$SQLStringDB =  "use ".$db.";";
		$ErgebnisDB = mysql_query($SQLStringDB,$Connection);
		$SQLStringTabelle = "show fields from ".$tab[0].";";
		$ErgebnisTabelle = mysql_query($SQLStringTabelle,$Connection);

	
//switch database, write data to the table	''difference''
			while($row = mysql_fetch_row($ErgebnisTabelle)){
				$SQLStringDB =  "use changedb;";
				$ErgebnisDB = mysql_query($SQLStringDB,$Connection);
				$SQLStringEinfuegen = "INSERT INTO difference VALUES ('',CURDATE(), '".$tab[0]."', '".$row[0]."', '".$row[1]."', '".$row[2]."', '".$row[3]."', '".$row[4]."', '".$row[5]."');";
				$ErgebnisEinfuegen = mysql_query($SQLStringEinfuegen,$Connection);
			}
		
	}	
//MySQL-disconnect
	mysql_close($Connection);
?>

<html>
	<head>
		<title>
			DB Sicherung
		</title>
		<!-- Forwarding to eingabe.html (on click on the button)-->
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




