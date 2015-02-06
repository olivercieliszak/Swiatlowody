<?php
$iloscWlokien = $FP -> db_sq('SELECT COUNT(*) AS ilosc FROM relacjaWlokno WHERE relacjaID = "'.$_G -> relacjaID.'"') -> ilosc;
$wynik = $FP -> db_sq('SELECT * FROM relacja WHERE relacjaID = "'.$_G -> relacjaID.'"');

?>
<table align="center">
<tr>
<td colspan="6"><h3>Lista włókien w relacji nr <?php echo $wynik -> relacjaID.':<br>'.$FP -> pobierzPunkt($wynik -> punktIDStart).' - '.$FP -> pobierzPunkt($wynik -> punktIDKoniec).'<br><br>'.$wynik -> opis ?></h3></td>
</tr>
<?php
if($iloscWlokien == 0)
	echo '<tr><td colspan="3"><b><a href="?modul=relacje&co=dodajWlokno&relacjaID='.$_G -> relacjaID.'" class="dodaj">Wyszukaj włókna</a></b></td></tr>';
?>
<tr>
<td><b>Kabel</b></td>
<td><b>Tuba</b></td>
<td><b>Włókno</b></td>
</tr>

<?php
$zapytanie = $FP -> db_q('SELECT * FROM relacjaWlokno LEFT JOIN kabelWlokno ON relacjaWlokno.kabelWloknoID = kabelWlokno.kabelWloknoID WHERE relacjaID = "'.$_G -> relacjaID.'" ORDER BY kolejnosc ASC');
while($wynik = $zapytanie -> fetch_object()){
	$tuba = $FP -> kolor('tuba', $wynik -> kolorTubaID);
	$wlokno = $FP -> kolor('wlokno', $wynik -> kolorWloknoID);
	echo '
	<tr>
	<td><b>'.$FP -> pobierzRelacjeKabla($wynik -> kabelID).'</b></td>
	<td style="background-color: '.$tuba -> kolorHTML.';border: 1px solid #656565;; color: '.$FP -> znajdzKolor($tuba -> kolorHTML).'">'.$tuba -> kolor.'</td>
	<td style="background-color: '.$wlokno -> kolorHTML.';border: 1px solid #656565; color: '.$FP -> znajdzKolor($wlokno -> kolorHTML).'">'.$wlokno -> kolor.' ('.$wynik -> kabelWloknoID.')</td>
	</tr>';
}
if($iloscWlokien > 0){
?>
<tr>
<td colspan="3">
<b><a href="?modul=relacje&co=usunWlokna&relacjaID=<?php echo $_G -> relacjaID ?>" class="usun">Uwolnij włókna</a></b>
</td>
</tr>
<?php } ?>
</table>
