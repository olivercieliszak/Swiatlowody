<?php
$wynik = $FP -> db_sq('SELECT * FROM relacja WHERE relacjaID = "'.(int)$_R -> relacjaID.'"');
$iloscWlokien = $FP -> db_sq('SELECT COUNT(*) AS ilosc FROM relacjaWlokno WHERE relacjaID = "'.$_R -> relacjaID.'"') -> ilosc;
if(isset($_P -> relacjaID)){
	if($iloscWlokien > 0)
		echo $FP -> komunikat(false,false,'Nie można usunąć relacji do której przypisane są włókna');
	else{
		if(!empty($_P -> relacjaID)){
		$zapytanie = $FP -> db_q('DELETE FROM relacja WHERE relacjaID = "'.$_P -> relacjaID.'"');

		echo $FP -> komunikat($zapytanie, 'Relacja nr <i>'.$_P -> relacjaID.'</i> została prawidłowo usunięta.<br><br><a href="?modul=relacje&co=listaRelacji">Powrót do listy relacji</a>', 'Wystąpił błąd podczas usuwania relacji nr <i>'.$_P -> relacjaID.'</i>');
		if($zapytanie)
			$FP -> log('Usunięto relację nr '.$_P -> relacjaID);
		}
	}
}

else{
?>
<form action="?modul=relacje&co=usunRelacje" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Usuwanie kabla</h3>
Czy na pewno chcesz usunąć tą relację?</td>
</tr>
<tr>
<td>ID</td>
<td><?php echo $wynik -> relacjaID ?><input type="hidden" name="relacjaID" value="<?php echo $wynik -> relacjaID ?>"></td>
</tr>
<td>Początek</td>
<td><?php echo $FP -> pobierzPunkt($wynik -> punktIDStart); ?></td>
</tr>
<tr>
<td>Początek - opis</td>
<td><?php echo $wynik -> opisStart ?></td>
</tr>
<tr>
<td>Koniec</td>
<td><?php echo $FP -> pobierzPunkt($wynik -> punktIDKoniec) ?></td>
</tr>
<tr>
<td>Koniec - opis</td>
<td><?php echo $wynik -> opisKoniec ?></td>
</tr>
<tr>
<td>Opis</td>
<td><?php echo $wynik -> opis ?></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Usuń!"></td>
</tr>

</table>
</form>
<?php
}
?>