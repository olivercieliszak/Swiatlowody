<table align="center">
<tr>
<td colspan="6"><h3>Lista włókien w kablu <?php echo $FP -> pobierzRelacjeKabla($_G -> kabelID) ?></h3></td>
</tr>
<tr>
<td><b>ID</b></td>
<td><b>Tuba</b></td>
<td><b>Włókno</b></td>
<td><b>Zaspawane</b></td>
<td><b>Obsługiwana relacja</b></td>
<td><b><a href="?modul=kable&co=dodajWlokno&kabelID=<?php echo $_G -> kabelID ?>" class="dodaj">Dodaj włókno</a></b></td>
</tr>

<?php
$zapytanie = $FP -> db_q('SELECT * FROM kabelWlokno WHERE kabelID = "'.$_G -> kabelID.'" ORDER BY kolorTubaID, kolorWloknoID, kabelWloknoID ASC');
while($wynik = $zapytanie -> fetch_object()){
	$tuba = $FP -> kolor('tuba', $wynik -> kolorTubaID);
	$wlokno = $FP -> kolor('wlokno', $wynik -> kolorWloknoID);
	echo '
	<tr>
	<td>'.$wynik -> kabelWloknoID.'</td>
	<td style="background-color: '.$tuba -> kolorHTML.';border: 1px solid #656565; color: '.$FP -> znajdzKolor($tuba -> kolorHTML).'">'.$tuba -> kolor.'</td>
	<td style="background-color: '.$wlokno -> kolorHTML.';border: 1px solid #656565; color: '.$FP -> znajdzKolor($wlokno -> kolorHTML).'">'.$wlokno -> kolor.'</td>
	<td>';
	$zapytanie2 = $FP -> db_q('SELECT mufaID from mufa NATURAL RIGHT JOIN mufaSpaw WHERE kabelWloknoID1 = "'.$wynik -> kabelWloknoID.'" OR kabelWloknoID2 = "'.$wynik -> kabelWloknoID.'"');
	while($wynik2 = $zapytanie2 -> fetch_object()){
	
		echo '<a href="?modul=mufy&co=listaSpawow&mufaID='.$wynik2 -> mufaID.'">Mufa nr '.$wynik2 -> mufaID.'</a><br>';
		
	}
	echo '</td>
	<td>'.$FP -> pobierzRelacjeLogicznaWlokna($wynik -> kabelWloknoID).'</td>
	<td><a href="?modul=kable&co=usunWlokno&kabelWloknoID='.$wynik -> kabelWloknoID.'" class="usun">Usuń</a></td>
	</tr>';
}
?>
</table>
