<?php
$punkt = $FP -> db_sq('SELECT * FROM punkt WHERE punktID = "'.(int)$_R -> punktID.'"');

if(isset($_P -> punktID)){
	if($FP -> db_sq('SELECT count(*) as ilosc FROM relacja WHERE  punktIDStart = "'.(int)$_R -> punktID.'" OR punktIDKoniec = "'.(int)$_R -> punktID.'"') -> ilosc > 0)
		echo $FP -> komunikat(false, false, 'Nie można usunąć punktu używanego w relacji.<br>Najpierw usuń punkt z relacji a dopiero potem sam punkt');
	else if($FP -> db_sq('SELECT count(*) as ilosc FROM przelacznica WHERE punktID = "'.(int)$_R -> punktID.'"') -> ilosc > 0)
		echo $FP -> komunikat(false, false, 'Nie można usunąć punktu używanego w przełącznicy.<br>Najpierw usuń punkt z przełącznicy a dopiero potem sam punkt');
	else if($FP -> db_sq('SELECT count(*) as ilosc FROM mufa WHERE punktID = "'.(int)$_R -> punktID.'"') -> ilosc > 0)
		echo $FP -> komunikat(false, false, 'Nie można usunąć punktu używanego w mufie.<br>Najpierw usuń punkt z mufy a dopiero potem sam punkt');
	else if($FP -> db_sq('SELECT count(*) as ilosc FROM kabelPunkt WHERE punktID = "'.(int)$_R -> punktID.'"') -> ilosc > 0)
		echo $FP -> komunikat(false, false, 'Nie można usunąć punktu używanego jako przebieg kabla.<br>Najpierw usuń punkt z przebiegu kabla a dopiero potem sam punkt');
	else if($FP -> db_sq('SELECT count(*) as ilosc FROM kabel WHERE punktIDStart = "'.(int)$_R -> punktID.'" OR punktIDKoniec = "'.(int)$_R -> punktID.'"') -> ilosc > 0)
		echo $FP -> komunikat(false, false, 'Nie można usunąć punktu używanego w kablu.<br>Najpierw usuń punkt z kabla a dopiero potem sam punkt');
	else if(!empty($_P -> punktID)){
		$zapytanie = $FP -> db_q('DELETE FROM punkt WHERE punktID = "'.$_P -> punktID.'"');
		echo $FP -> komunikat($zapytanie, 'Punkt nr <i>'.$_P -> punktID.'</i> został prawidłowo usunięty.<br><br><a href="?modul=punkty&co=listaPunktow">Powrót do listy punktów</a>', 'Wystąpił błąd podczas usuwania punktu nr <i>'.$_P -> punktID.'</i>');
		if($zapytanie)
			$FP -> log('Usunięto punkt nr '.$_P -> punktID);
	}
}

else{
?>
<form action="?modul=punkty&co=usunPunkt" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Usuwanie punktu</h3>
<div class="usun"><b>Czy na pewno chcesz usunąć ten punkt?</b></div></td>
</tr>
<tr>
<td>ID</td>
<td><?php echo $punkt -> punktID ?><input type="hidden" name="punktID" value="<?php echo $punkt -> punktID ?>"></td>
</tr>
<tr>
<td>Opis i adres</td>
<td><?php echo $punkt -> opis ?></td>
</tr>
<tr>
<td>GPS</td>
<td><?php echo $punkt -> gps ?></td>
</tr>
<tr>
<td>Typ</td>
<td><?php echo $FP -> db_sq('SELECT typ FROM punktTyp WHERE punktTypID = "'.$punkt -> punktTypID.'"') -> typ; ?></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Usuń" class="usun"></td>
</tr>

</table>
</form>
<?php
}
?>