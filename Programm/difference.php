<?php
//-----------------------------------------------------------------------------------------------------------------------------
//Short description: show database changes

//Project: Diffmaster

//Developer:Stefan Repp, Philipp Schmitt, Philipp Fuchs, Jessica Wifling, Gina Weiland, Alexander Trierweiler, Steve Bone

//Description: 
//This script shows the database changes of the choosen database with the difference table saves.
//It uses a SQL query to do so.
//-----------------------------------------------------------------------------------------------------------------------------

	//-------- variabletransfer per POST
		//Serverdaten
		$serv = $_POST['server']; 
		$name = $_POST['name'];
		$pw = $_POST['pw'];
		//Start- and end date - format: YYYY-MM-DD
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
	
	//--------Calculation of date entries in choosen time period
	$SQLString =  "use changedb";
	$Ergebnis = mysql_query($SQLString,$Connection);
	$SQLString =  "select date_change from difference where date_change between '". $start."' and '".$end."' group by date_change";
	$Ergebnis = mysql_query($SQLString,$Connection);
	$i=0;
	$rows_new = array();
	
	//--------fetch all etries of that date
	while($row = mysql_fetch_row($Ergebnis)){
		$dates[$i] = $row[0];
		//--------buffering of the data of the older date
		$rows_old = array_merge($rows_new, array());
		$rows_new = array();
		$SQLString =  "use changedb";
		$Ergebnis2 = mysql_query($SQLString,$Connection);
		$SQLString =  "select table_name, field_name, field_type, field_null, field_key, field_default, field_extra from difference where date_change= '".$dates[$i]."' order by date_change";
		$Ergebnis2 = mysql_query($SQLString,$Connection);
		//--------creation of a multidimensional array with the buffer 
		while($row = mysql_fetch_array($Ergebnis2)){
			$rows_new[] = array($row["table_name"]=>array($row["field_name"]=>array( "field_type"=>$row["field_type"],
						"field_null"=>$row["field_null"],
						"field_key"=>$row["field_key"],
						"field_default"=>$row["field_default"],
						"field_extra"=>$row["field_extra"])));
		}
		$i++;	
	}
	//MySQL-disconnect
	mysql_close($Connection);
	//--------Vergleichprozedur der Daten 
		//--------distribute arrays to columns and look for differences with buffer (using array_diff)
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
	
	//--------Output--------\\
	$diff_tables = array();
	$diff_fields = array();
	$diff_type = array();
	//--------split multidimensional result array in one dimansional arrays
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
	
	//--------fetch new table names and data
	$rows_new_tables = array();
	$rows_old_tables = array();
	for($i=0;$i<count($rows_new);$i++){
		$rows_new_tables = array_merge($rows_new_tables,array_keys($rows_new[$i]));
	}
	for($i=0;$i<count($rows_old);$i++){
		$rows_old_tables = array_merge($rows_old_tables,array_keys($rows_old[$i]));
	}
	
	//--------check if new tables were created (and assign for a later readout)
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
				//--------display date 
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
                 <!-- table name -->				
					Tabellenname   
				</th>
				<th>
				<!-- field name -->
					Feldname
				</th>
				<th>
				<!-- field type -->
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
						//--------Emit of the changed data (with table assignments)
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
