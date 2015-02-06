<?php
$wynik = $FP -> db_sq('SELECT * FROM punktTyp WHERE punktTypID = "'.(int)$_R -> punktTypID.'"');

if(isset($_P -> punktTypID)){
	if($_P -> punktTypID < 3)
		echo $FP -> komunikat(false, false, 'Nie możesz usunąć predefiniowanych typów punktów');
	
	else if($FP -> db_sq('SELECT COUNT(*) as ilosc FROM punkt WHERE punktTypID = "'.(int)$_R -> punktTypID.'"') -> ilosc > 0)
		echo $FP -> komunikat(false, false, 'Nie możesz usunąć typu punktu,<br>który jest przypisany do jednego z punktów');
	
	else if(!empty($_P -> punktTypID)){
	$zapytanie = $FP -> db_q('DELETE FROM punktTyp WHERE punktTypID = "'.$_P -> punktTypID.'"');

	echo $FP -> komunikat($zapytanie, 'Typ punktu nr <i>'.$_P -> punktTypID.'</i> został prawidłowo usunięty.<br><br><a href="?modul=ustawienia&co=typyPunktow">Powrót do listy typów punktów</a>', 'Wystąpił błąd podczas usuwania typu punktu nr <i>'.$_P -> punktTypID.'</i><br><br><a href="?modul=ustawienia&co=typyPunktow">Powrót do listy typów punktów</a>');
	if($zapytanie)
		$FP -> log('Usunięto typ punktu - '.$wynik -> typ);

	}
}

else{
?>
<form action="?modul=ustawienia&co=usunTypPunktu" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Usuwanie typu punktu</h3>
<div class="usun"><b>Czy na pewno chcesz usunąć ten typ punktu?</b></div></td>
</tr>
<tr>
<td>ID</td>
<td><?php echo $wynik -> punktTypID ?><input type="hidden" name="punktTypID" value="<?php echo $wynik -> punktTypID ?>"></td>
</tr>
<tr>
<td>Typ</td>
<td><?php echo $wynik -> typ ?></td>
</tr>
<tr>
<td>Kolor</td>
<?php echo '<td style="background-color: '.$FP -> koloryPunktowTlo($wynik -> kolorPunkt).'; color: '.$FP -> koloryPunktowTekst($wynik -> kolorPunkt).';">'.$wynik -> kolorPunkt.'</td>'; ?></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Usuń" class="usun"></td>
</tr>

</table>
</form>
<?php
}
?>