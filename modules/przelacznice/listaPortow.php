<?php
$punkt = $FP -> pobierzPunkt($FP -> db_sq('SELECT punktID FROM przelacznica WHERE przelacznicaID = "'.$_R -> przelacznicaID .'"') -> punktID);
?>
<table align="center">
<tr>
<td colspan="10"><h3>Lista portów w przełącznicy nr <?php echo $_R -> przelacznicaID ?></h3></td>
</tr>
<tr>
<td><b>Port</b></td>
<td><b>Typ</b></td>
<td><b>Kabel</b></td>
<td><b>Tuba</b></td>
<td><b>Włókno</b></td>
<td><b>Obsługiwana relacja</b></td>
<td><b><a href="?modul=przelacznice&co=dodajPort&przelacznicaID=<?php echo $_G -> przelacznicaID ?>" class="dodaj">Dodaj port</a></b></td>
</tr>

<?php
$zapytanie = $FP -> db_q('SELECT * FROM przelacznicaPort WHERE przelacznicaID = "'.$_G -> przelacznicaID.'" ORDER BY LPAD(lower(port), 10,0) ASC');

while($wynik = $zapytanie -> fetch_object()){
	$kabelID = $FP -> pobierzIDKabla($wynik -> kabelWloknoID);
	$kabelWloknoID = $FP -> db_sq('SELECT * FROM kabelWlokno WHERE kabelWloknoID = "'.$wynik -> kabelWloknoID.'"');
	$tuba = $FP -> kolor('tuba', $kabelWloknoID -> kolorTubaID);
	$wlokno = $FP -> kolor('wlokno', $kabelWloknoID -> kolorWloknoID);
	echo '
	<tr>
	<td>'.$wynik -> port.'</td>
	<td>'.$wynik -> typ.'</td>
	<td>'.$FP -> pobierzRelacjeKabla($kabelID,1).'</td>
	<td style="background-color: '.$tuba -> kolorHTML.';border: 1px solid #656565; color: '.$FP -> znajdzKolor($tuba -> kolorHTML).'" class="pokoloruj">'.$tuba -> kolor.'</td>
	<td style="background-color: '.$wlokno -> kolorHTML.';border: 1px solid #656565; color: '.$FP -> znajdzKolor($wlokno -> kolorHTML).'">'.$wlokno -> kolor.' ('.$wynik -> kabelWloknoID.')</td>
	<td>'.$FP -> pobierzRelacjeLogicznaWlokna($kabelWloknoID -> kabelWloknoID).'</td>
	<td><a href="?modul=przelacznice&co=zmienPort&przelacznicaPortID='.$wynik -> przelacznicaPortID.'" class="zmien">Zmień</a><br><a href="?modul=przelacznice&co=usunPort&przelacznicaPortID='.$wynik -> przelacznicaPortID.'" class="usun">Usuń</a></td>
	</tr>';
}
?>
</table>
