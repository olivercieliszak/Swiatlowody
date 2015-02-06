<?php
$wynik = $FP -> db_sq('SELECT * FROM kolorTuba WHERE kolorTubaID = "'.(int)$_R -> kolorTubaID.'"');

if(isset($_P -> kolorTubaID)){
	if($FP -> db_sq('SELECT COUNT(*) as ilosc FROM kabelWlokno WHERE kolorTubaID = "'.(int)$_R -> kolorTubaID.'"') -> ilosc > 0)
		echo $FP -> komunikat(false, false, 'Nie możesz usunąć koloru tuby, który jest przypisany do jednego z włókien');

	else if(!empty($_P -> kolorTubaID)){
	$zapytanie = $FP -> db_q('DELETE FROM kolorTuba WHERE kolorTubaID = "'.(int)$_P -> kolorTubaID.'"');

	echo $FP -> komunikat($zapytanie, 'Kolor tuby nr <i>'.$_P -> kolorTubaID.'</i> został prawidłowo usunięty.<br><br><a href="?modul=ustawienia&co=koloryTub">Powrót do listy kolorów tub</a>', 'Wystąpił błąd podczas usuwania typu koloru nr <i>'.$_P -> kolorTubaID.'</i><br><br><a href="?modul=ustawienia&co=koloryTub">Powrót do listy typów kolorów</a>');
	if($zapytanie)
		$FP -> log('Usunięto kolor tuby - '.$wynik -> kolor);
	
	}
}

else{
?>
<form action="?modul=ustawienia&co=usunKolorTuby" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Usuwanie koloru tuby</h3>
Czy na pewno chcesz usunąć ten kolor tuby?</td>
</tr>
<tr>
<td>ID</td>
<td><?php echo $wynik -> kolorTubaID ?><input type="hidden" name="kolorTubaID" value="<?php echo $wynik -> kolorTubaID ?>"></td>
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