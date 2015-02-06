<?php
$wynik = $FP -> db_sq('SELECT * FROM kabel WHERE kabelID = "'.(int)$_R -> kabelID.'"');

if(isset($_P -> kabelID)){
	//czy obsluguje relacje
	if($FP -> db_sq('SELECT COUNT(*) as ilosc FROM relacjaWlokno NATURAL LEFT JOIN kabelWlokno WHERE kabelID = "'.(int)$_R -> kabelID.'"') -> ilosc > 0)
		echo $FP -> komunikat(false, false, 'Nie można usunąć kabla, który obsługuje relacje.<br>Najpierw usuń relacje z włókien a dopiero potem kabel.');
	//czy ma zaspawane wlokna
	else if($FP -> db_sq('SELECT COUNT(*) as ilosc FROM mufaSpaw WHERE kabelID1 = "'.(int)$_R -> kabelID.'" OR  kabelID2 = "'.(int)$_R -> kabelID.'"') -> ilosc > 0)
		echo $FP -> komunikat(false, false, 'Nie można usunąć kabla, który ma zaspawane włókna w mufie.<br>Najpierw usuń spaw w mufie a dopiero potem kabel.');	

	//czy ma zaspawane wlokna w przelacznicy
	else if($FP -> db_sq('SELECT COUNT(*) as ilosc FROM przelacznicaPort WHERE kabelID = "'.(int)$_R -> kabelID.'"') -> ilosc > 0)
		echo $FP -> komunikat(false, false, 'Nie można usunąć kabla, który ma zaspawane włókna w przełącznicy.<br>Najpierw usuń port w przełącznicy a dopiero potem kabel.');	
	else if(!empty($_P -> kabelID)){
		$zapytanie = $FP -> db_q('DELETE FROM kabel WHERE kabelID = "'.$_P -> kabelID.'"');
		$zapytanie2 = $FP -> db_q('DELETE FROM kabelWlokno WHERE kabelID = "'.$_P -> kabelID.'"');
		$zapytanie3 = $FP -> db_q('DELETE FROM kabelPunkt WHERE kabelID = "'.$_P -> kabelID.'"');
		
		echo $FP -> komunikat($zapytanie, 'Kabel nr <i>'.$_P -> kabelID.'</i> został prawidłowo usunięty.<br><br><a href="?modul=kable&co=listaKabli">Powrót do listy kabli</a>', 'Wystąpił błąd podczas usuwania kabelu nr <i>'.$_P -> kabelID.'</i>');
		if($zapytanie)
			$FP -> log('Usunięto kabel nr '.$_P -> kabelID);
	}
}

else{
?>
<form action="?modul=kable&co=usunKabel" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Usuwanie kabla</h3>
<div class="usun"><b>Czy na pewno chcesz usunąć ten kabel?</b></div></td>
</tr>
<tr>
<td>ID</td>
<td><?php echo $wynik -> kabelID ?><input type="hidden" name="kabelID" value="<?php echo $wynik -> kabelID ?>"></td>
</tr>
<td>Początek</td>
<td><?php echo $FP -> pobierzPunkt($wynik -> punktIDStart); ?></td>
</tr>
<tr>
<td>Koniec</td>
<td><?php echo $FP -> pobierzPunkt($wynik -> punktIDKoniec); ?></td>
</tr>
<tr>
<td>Opis</td>
<td><?php echo $wynik -> opis ?></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Usuń" class="usun"></td>
</tr>

</table>
</form>
<?php
}
?>