<?php
$wynik = $FP -> db_sq('SELECT * FROM kolorWlokno WHERE kolorWloknoID = "'.(int)$_R -> kolorWloknoID.'"');

if(isset($_P -> kolorWloknoID)){
	if($FP -> db_sq('SELECT COUNT(*) as ilosc FROM kabelWlokno WHERE kolorWloknoID = "'.(int)$_R -> kolorWloknoID.'"') -> ilosc > 0)
		echo $FP -> komunikat(false, false, 'Nie możesz usunąć koloru włókna,<br>który jest przypisany do jednego z włókien');
	
	else if(!empty($_P -> kolorWloknoID)){
	$zapytanie = $FP -> db_q('DELETE FROM kolorWlokno WHERE kolorWloknoID = "'.(int)$_P -> kolorWloknoID.'"');
	
	echo $FP -> komunikat($zapytanie, 'Kolor włókna nr <i>'.$_P -> kolorWloknoID.'</i> został prawidłowo usunięty.<br><br><a href="?modul=ustawienia&co=koloryWlokien">Powrót do listy kolorów włókien</a>', 'Wystąpił błąd podczas usuwania typu koloru nr <i>'.$_P -> kolorWloknoID.'</i><br><br><a href="?modul=ustawienia&co=koloryWlokien">Powrót do listy typów kolorów</a>');
	if($zapytanie)
		$FP -> log('Usunięto kolor włókna - '.$wynik -> kolor);

	}
}

else{
?>
<form action="?modul=ustawienia&co=usunKolorWlokna" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Usuwanie koloru włókna</h3>
Czy na pewno chcesz usunąć ten kolor?</td>
</tr>
<tr>
<td>ID</td>
<td><?php echo $wynik -> kolorWloknoID ?><input type="hidden" name="kolorWloknoID" value="<?php echo $wynik -> kolorWloknoID ?>"></td>
</tr>
<tr>
<td>Nazwa koloru</td>
<td><?php echo $wynik -> kolor ?></td>
</tr>
<tr>
<td>Kod HTML koloru</td>
<td style="background-color: <?php echo $wynik -> kolorHTML ?>;border: 1px solid #656565;"><?php echo $wynik -> kolorHTML ?></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Usuń!"></td>
</tr>

</table>
</form>
<?php
}
?>