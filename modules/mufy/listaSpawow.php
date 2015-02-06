<?php
$punkt = $FP -> pobierzPunkt($FP -> db_sq('SELECT punktID FROM mufa WHERE mufaID = "'.$_R -> mufaID .'"') -> punktID);
?>
<table align="center">
<tr>
<td colspan="10"><h3>Lista spawów w mufie nr <?php echo $_R -> mufaID ?> w punkcie <?php echo $punkt ?></h3></td>
</tr>
<tr>
<td><b>Kabel</b></td>
<td><b>Tuba</b></td>
<td><b>Włókno</b></td>
<td><b>Kabel</b></td>
<td><b>Tuba</b></td>
<td><b>Włókno</b></td>
<td><b>Opis</b></td>
<td><b>Obsługiwana relacja</b></td>
<td><b><a href="?modul=mufy&co=dodajSpaw&mufaID=<?php echo $_G -> mufaID ?>" class="dodaj">Dodaj spaw</a></b></td>
</tr>

<?php
$zapytanie = $FP -> db_q('SELECT * FROM mufaSpaw WHERE mufaID = "'.$_G -> mufaID.'" ORDER BY kabelID1, kabelWloknoID1, kabelID2, kabelWloknoID2 ASC');

while($wynik = $zapytanie -> fetch_object()){

	$kabelWloknoID1 = $FP -> db_sq('SELECT * FROM kabelWlokno WHERE kabelWloknoID = "'.$wynik -> kabelWloknoID1.'"');
	$tuba1 = $FP -> kolor('tuba', $kabelWloknoID1 -> kolorTubaID);
	$wlokno1 = $FP -> kolor('wlokno', $kabelWloknoID1 -> kolorWloknoID);
	$kabelWloknoID2 = $FP -> db_sq('SELECT * FROM kabelWlokno WHERE kabelWloknoID = "'.$wynik -> kabelWloknoID2.'"');
	$tuba2 = $FP -> kolor('tuba', $kabelWloknoID2 -> kolorTubaID);
	$wlokno2 = $FP -> kolor('wlokno', $kabelWloknoID2 -> kolorWloknoID);
	echo '
	<tr>
	<td>'.$FP -> pobierzRelacjeKabla($wynik -> kabelID1,1).'</td>
	<td style="background-color: '.$tuba1 -> kolorHTML.';border: 1px solid #656565; color: '.$FP -> znajdzKolor($tuba1 -> kolorHTML).'" class="pokoloruj">'.$tuba1 -> kolor.'</td>
	<td style="background-color: '.$wlokno1 -> kolorHTML.';border: 1px solid #656565; color: '.$FP -> znajdzKolor($wlokno1 -> kolorHTML).'">'.$wlokno1 -> kolor.' ('.$wynik -> kabelWloknoID1.')</td>
	<td>'.$FP -> pobierzRelacjeKabla($wynik -> kabelID2,1).'</td>
	<td style="background-color: '.$tuba2 -> kolorHTML.';border: 1px solid #656565; color: '.$FP -> znajdzKolor($tuba2 -> kolorHTML).'">'.$tuba2 -> kolor.'</td>
	<td style="background-color: '.$wlokno2 -> kolorHTML.';border: 1px solid #656565; color: '.$FP -> znajdzKolor($wlokno2 -> kolorHTML).'">'.$wlokno2 -> kolor.' ('.$wynik -> kabelWloknoID2.')</td>
	<td>'.$wynik -> opis.'</td>
	<td>'.$FP -> pobierzRelacjeLogicznaWlokna($kabelWloknoID1 -> kabelWloknoID).'</td>
	<td><a href="?modul=mufy&co=zmienSpaw&mufaSpawID='.$wynik -> mufaSpawID.'" class="zmien">Zmień</a><br><a href="?modul=mufy&co=usunSpaw&mufaSpawID='.$wynik -> mufaSpawID.'" class="usun">Usuń</a></td>
	</tr>';
}
?>
</table>
