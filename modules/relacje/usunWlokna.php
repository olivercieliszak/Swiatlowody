<?php
$wynik = $FP -> db_sq('SELECT * FROM relacja WHERE relacjaID = "'.$_R -> relacjaID.'"');

if(isset($_P -> relacjaID)){
	if(!empty($_P -> relacjaID)){
		$zapytanie = $FP -> db_q('DELETE FROM relacjaWlokno WHERE relacjaID = "'.$_P -> relacjaID.'"');

		echo $FP -> komunikat($zapytanie, 'Włókna z relacji nr <i>'.$_P -> relacjaID.'</i> zostały prawidłowo usunięte.<br><br><a href="?modul=relacje&co=listaWlokien&relacjaID='.$_P -> relacjaID.'">Powrót do listy włókien</a>', 'Wystąpił błąd podczas usuwania relacji nr <i>'.$_P -> relacjaID.'</i>');
		if($zapytanie)
			$FP -> log('Usunięto włókna z relacji nr '.$_P -> relacjaID);
	}
}


else{
?>
<form action="?modul=relacje&co=usunWlokna" method="POST">
<input type="hidden" name="relacjaID" value="<?php echo $_R -> relacjaID ?>">
<table align="center">
<tr>
<td colspan="3"><h3>Usuwanie włókien z relacji nr <?php echo $wynik -> relacjaID.':<br>'.$FP -> pobierzPunkt($wynik -> punktIDStart).' - '.$FP -> pobierzPunkt($wynik -> punktIDKoniec).'<br><br>'.$wynik -> opis ?></h3>
<div class="usun"><b>Czy na pewno chcesz usunąć te włókna z relacji?</b></div></td>
</tr>
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
	<td>'.$FP -> pobierzRelacjeKabla($wynik -> kabelID).'</td>
	<td style="background-color: '.$tuba -> kolorHTML.';border: 1px solid #656565;; color: '.$FP -> znajdzKolor($tuba -> kolorHTML).'">'.$tuba -> kolor.'</td>
	<td style="background-color: '.$wlokno -> kolorHTML.';border: 1px solid #656565; color: '.$FP -> znajdzKolor($wlokno -> kolorHTML).'">'.$wlokno -> kolor.' ('.$wynik -> kabelWloknoID.')</td>
	</tr>';
}
?>
<tr>
<td colspan="3"><input type="submit" value="Usuń" class="usun"></td>
</tr>

</table>
</form>
<?php
}
?>