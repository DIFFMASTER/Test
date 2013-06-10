<?php
	//-------- Variablenübertragung per POST
		//Serverdaten
		$serv = $_POST['server']; 
		$name = $_POST['name'];
		$pw = $_POST['pw'];
		//Start- und Enddatum  - Format: YYYY-MM-DD
		$start = $_POST['start'];
		$end = $_POST['end'];
	
	//--------Fixe Variablen
	if(!(isset($serv))){
		$serv='localhost';
	}
	if(!(isset($name))){
		$name='root';
	}
	if(!(isset($pw))){
		$pw='';
	}
	if(!(isset($start))){
		$start="2013-05-01";
	}
	if(!(isset($end))){
		$end="2013-05-03";
	}
	
	//--------MySQL-Connect
	$Connection = mysql_connect($serv,$name,$pw) or die ("Verbindung fehlgeschlagen");
	
	//--------Ermittlung aller Datumseintragungen im angegebenen Zeitraum
	$SQLString =  "use changedb";
	$Ergebnis = mysql_query($SQLString,$Connection);
	$SQLString =  "select date_change from difference where date_change between '". $start."' and '".$end."' group by date_change";
	$Ergebnis = mysql_query($SQLString,$Connection);
	$i=0;
	$rows_new = array();
	
	//--------Ermittlung aller Einträge des Datums
	while($row = mysql_fetch_row($Ergebnis)){
		$dates[$i] = $row[0];
		//--------Zwischenspeichern der Daten des älteren Datums
		$rows_old = array_merge($rows_new, array());
		$rows_new = array();
		$SQLString =  "use changedb";
		$Ergebnis2 = mysql_query($SQLString,$Connection);
		$SQLString =  "select table_name, field_name, field_type, field_null, field_key, field_default, field_extra from difference where date_change= '".$dates[$i]."' order by date_change";
		$Ergebnis2 = mysql_query($SQLString,$Connection);
		//--------Erzeugung eines mehrdimensionalen Arrays mit den Daten der Sicherung 
		while($row = mysql_fetch_array($Ergebnis2)){
			$rows_new[] = array($row["table_name"]=>array($row["field_name"]=>array( "field_type"=>$row["field_type"],
						"field_null"=>$row["field_null"],
						"field_key"=>$row["field_key"],
						"field_default"=>$row["field_default"],
						"field_extra"=>$row["field_extra"])));
		}
		$i++;	
	}
	//MySQL-Verbindung trennen
	mysql_close($Connection);
	//--------Vergleichprozedur der Daten 
		//--------Die Arrays werden in Spalten ausgeteilt und mit der PHP-Funktion array_diff auf Unterschiede überprüft
	foreach($rows_new as $key => $value){
		$rows_new_s[$key] = serialize($value);
	}
	foreach($rows_old as $key => $value){
		$rows_old_s[$key] = serialize($value);
	}
	$diff_s = array_diff($rows_new_s, $rows_old_s);
	foreach($diff_s as $key => $value){
		$diff[] = unserialize($diff_s[$key]);
	}
	
	//--------Ausgabe--------\\
	$diff_tables = array();
	$diff_fields = array();
	$diff_type = array();
	//--------Das mehrdimensionale Ergebnisarray wird in eindimensionale Arrays aufgespalten
	for($i=0;$i<count($diff);$i++){
		$diff_tables = array_merge($diff_tables,array_keys($diff[$i]));
	}
	for($i=0;$i<count($diff_tables);$i++){
		$diff_fields = array_merge($diff_fields,array_keys($diff[$i][$diff_tables[$i]]));
	}
	for($i=0;$i<count($diff_tables);$i++){
		$diff_type[$i] = $diff[$i][$diff_tables[$i]][$diff_fields[$i]]['field_type'];
	}
	for($i=0;$i<count($diff_tables);$i++){
		$diff_null[$i] = $diff[$i][$diff_tables[$i]][$diff_fields[$i]]['field_null'];
	}
	for($i=0;$i<count($diff_tables);$i++){
		$diff_key[$i] = $diff[$i][$diff_tables[$i]][$diff_fields[$i]]['field_key'];
	}
	for($i=0;$i<count($diff_tables);$i++){
		$diff_default[$i] = $diff[$i][$diff_tables[$i]][$diff_fields[$i]]['field_default'];
	}
	for($i=0;$i<count($diff_tables);$i++){
		$diff_extra[$i] = $diff[$i][$diff_tables[$i]][$diff_fields[$i]]['field_extra'];
	}
	
	//--------Die Tabellennamen der alten und neuen Daten werden ermittelt
	$rows_new_tables = array();
	$rows_old_tables = array();
	for($i=0;$i<count($rows_new);$i++){
		$rows_new_tables = array_merge($rows_new_tables,array_keys($rows_new[$i]));
	}
	for($i=0;$i<count($rows_old);$i++){
		$rows_old_tables = array_merge($rows_old_tables,array_keys($rows_old[$i]));
	}
	
	//--------Überprüfung ob neue Tabellen angelegt wurden (Markierung für spätere Ausgabe)
	for($i=0;$i<count($diff_tables);$i++){
		if (!(in_array($diff_tables[$i],$rows_new_tables))){
			$class[$i]=0;	
		}
		elseif(!(in_array($diff_tables[$i],$rows_old_tables))){
			$class[$i]=1;
		}
		elseif(!isset($class[$i])){
			$class[$i]=0;
		}
	}
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="formate.css">
	</head>
	<body>
		<header>
			Auswertung
		</header>
		<div id="date">
			<?php
				//--------Ausgabe des Datums
				echo "Datum:<br>";
				for($i=0;$i<(count($dates)/2);$i++){
					echo $dates[$i]." - ".$dates[$i+1];
				}
			?>
		</div>	
		<div id='anzeige'>
		<table id="anzeige_tab">
			<tr>
				<th>
					Tabellenname
				</th>
				<th>
					Feldname
				</th>
				<th>
					Feldtyp
				</th>
				<th>
					Null	
				</th>
				<th>
					Key
				</th>
				<th>
					Default
				</th>
				<th>
					Extra
				</th>
			</tr>
				<?php
					for($i=0;$i<count($diff);$i++){
						//--------Ausgabe der geänderten Daten (mit Markierung neuer Tabellen)
						echo "<tr ><td class='show".$class[$i]."'>".$diff_tables[$i]."</td>
						<td class='show".$class[$i]."'>".$diff_fields[$i]."</td>
						<td class='show".$class[$i]."'>".$diff_type[$i]."</td>
						<td class='show".$class[$i]."'>".$diff_null[$i]."</td>
						<td class='show".$class[$i]."'>".$diff_key[$i]."</td>
						<td class='show".$class[$i]."'>".$diff_default[$i]."</td>
						<td class='show".$class[$i]."'>".$diff_extra[$i]."</td></tr>";
					}
				?>
		</table>
		</div>
	</body>
</html>