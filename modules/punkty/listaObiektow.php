<?php

if(isset($_G -> punktID)){
$punktID = $_G -> punktID;
$punkt = $FP -> pobierzPunkt($punktID);
?>
<script>
$(document).ready(function() 
    { 
        $("#sortuj").tablesorter({sortList: [[0,0]], headers: { 3: {sorter: false} }}).bind("sortEnd",function() { 
		pokoloruj_wiersze()
		});
    } 
); 
   
</script>
<table align="center" id="sortuj">
<thead>
<tr>
<td colspan="5"><h3>Lista obiektów w punkcie <?php echo $punkt ?></h3></td>
</tr>
<tr style="cursor: pointer">
<th><b>Typ</b></th>
<th><b>ID</b></th>
<th><b>Opis</b></th>
<th></th>
</tr>
</thead>
<tbody>
<?php
$znalezioneKable = array();

$zapytanieGPS = $FP -> db_sq('SELECT gps FROM punkt WHERE punktID = "'.$punktID.'"') -> gps;

$zapytaniePunkt = $FP -> db_q('SELECT punktID FROM punkt WHERE gps = "'.$zapytanieGPS.'" ORDER BY punktID ASC');
while($wynikPunkty = $zapytaniePunkt -> fetch_object()){
$punktID = $wynikPunkty -> punktID;
//tabela kabel
$zapytanie = $FP -> db_q('SELECT kabelID FROM kabel WHERE punktIDStart = "'.$punktID.'" OR punktIDKoniec = "'.$punktID.'" ORDER BY kabelID ASC');
while($wynik = $zapytanie -> fetch_object()){
	if(!isset($znalezioneKable[$wynik -> kabelID])){
		echo '
		<tr>
		<td>Kabel</td>
		<td>Nr kabla: '.$wynik -> kabelID.'</td>
		<td>Punkt początkowy lub końcowy<br>'.$FP -> pobierzRelacjeKabla($wynik -> kabelID).'</td>
		<td><a href="?modul=kable&co=listaWlokien&kabelID='. $wynik -> kabelID .'" class="zmien">Włókna</a> | <a href="?modul=kable&co=listaPunktow&kabelID='. $wynik -> kabelID .'" class="zmien">Przebieg</a></td>

		</tr>';
		$znalezioneKable[$wynik -> kabelID] = true;
	}
}

//tabela kabelPunkt
$zapytanie = $FP -> db_q('SELECT kabelID FROM kabelPunkt WHERE punktID = "'.$punktID.'" ORDER BY kabelID ASC');
while($wynik = $zapytanie -> fetch_object()){
	if(!isset($znalezioneKable[$wynik -> kabelID])){
		echo '
		<tr>
		<td>Kabel</td>
		<td>Nr kabla: '.$wynik -> kabelID.'</td>
		<td>Punkt początkowy lub końcowy<br>'.$FP -> pobierzRelacjeKabla($wynik -> kabelID).'</td>
		<td><a href="?modul=kable&co=listaWlokien&kabelID='. $wynik -> kabelID .'" class="zmien">Włókna</a> | <a href="?modul=kable&co=listaPunktow&kabelID='. $wynik -> kabelID .'" class="zmien">Przebieg</a></td>

		</tr>';
		$znalezioneKable[$wynik -> kabelID] = true;
	}
}

//tabela mufa
$zapytanie = $FP -> db_q('SELECT mufaID, opis FROM mufa WHERE punktID = "'.$punktID.'" ORDER BY mufaID ASC');
while($wynik = $zapytanie -> fetch_object()){
echo '
<tr>
<td>Mufa</td>
<td>Nr mufy: '.$wynik -> mufaID.'</td>
<td>'.$wynik -> opis.'</td>
<td><a href="?modul=mufy&co=listaSpawow&mufaID='.$wynik -> mufaID.'" class="zmien">Spawy</a></td>
</tr>';
}

//tabela przełącznica
$zapytanie = $FP -> db_q('SELECT przelacznicaID, opis FROM przelacznica WHERE punktID = "'.$punktID.'" ORDER BY przelacznicaID ASC');
while($wynik = $zapytanie -> fetch_object()){
echo '
<tr>
<td>Przełącznica</td>
<td>Nr przełącznicy: '.$wynik -> przelacznicaID.'</td>
<td>'.$wynik -> opis.'</td>
<td><a href="?modul=przelacznice&co=listaPortow&przelacznicaID='.$wynik -> przelacznicaID.'" class="zmien">Porty</a></td>
</tr>';
}

//tabela relacja
$zapytanie = $FP -> db_q('SELECT relacjaID, opis, punktIDStart, punktIDKoniec FROM relacja WHERE punktIDStart = "'.$punktID.'" OR punktIDKoniec = "'.$punktID.'" ORDER BY relacjaID ASC');
while($wynik = $zapytanie -> fetch_object()){
echo '
<tr>
<td>Relacja</td>
<td>Nr relacji: '.$wynik -> relacjaID.'</td>
<td>Punkt początkowy lub końcowy relacji:<br>'.$FP -> pobierzPunkt($wynik -> punktIDStart).' - '.$FP -> pobierzPunkt($wynik -> punktIDKoniec).'<br>'.$wynik -> opis.'</td>
<td><a href="?modul=relacje&co=listaWlokien&relacjaID='. $wynik -> relacjaID .'" class="zmien">Włókna</a> | <a href="?modul=relacje&co=listaPunktow&relacjaID='. $wynik -> relacjaID .'" class="zmien">Przebieg</a></td>

</tr>';
}
}
?>
</tbody>
</table>
<?php
}
?>