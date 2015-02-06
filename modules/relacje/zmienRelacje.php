<?php
if(isset($_P -> relacjaID)){
	if(!empty($_P -> relacjaID)){
	$zapytanie = $FP -> db_q('UPDATE relacja SET punktIDStart = "'.$_P -> punktIDStart.'", opisStart = "'.$_P -> opisStart.'", punktIDKoniec = "'.$_P -> punktIDKoniec .'", opisKoniec = "'.$_P -> opisKoniec.'", opis = "'.$_P -> opis.'" WHERE relacjaID = "'.$_P -> relacjaID.'"');
	$punktIDStart = $FP -> pobierzPunkt($_P -> punktIDStart);
	$punktIDKoniec = $FP -> pobierzPunkt($_P -> punktIDKoniec);
	echo $FP -> komunikat($zapytanie, 'Relacja <i>'.$punktIDStart.' - '.$punktIDKoniec.'</i> została zmieniona prawidłowo<br><br><a href="?modul=relacje&co=listaRelacji">Powrót do listy relacji</a>',
	'Wystąpił błąd podczas zmiany kabla w relacji <i>'.$punktIDStart.' - '.$punktIDKoniec.'</i><br><br><a href="?modul=relacje&co=listaRelacji">Powrót do listy relacji</a>');
	if($zapytanie)
		$FP -> log('Zmieniono relację nr '.$_P -> relacjaID);
	}
}
else{

$wynik = $FP -> db_sq('SELECT * FROM relacja WHERE relacjaID = "'.(int)$_R -> relacjaID.'"');

?>
<form action="?modul=relacje&co=zmienRelacje" method="POST">
<input type="hidden" name="relacjaID" value="<?php echo $wynik -> relacjaID ?>">
<table align="center">
<tr>
<td colspan="2"><h3>Zmiana kabla</h3></td>
</tr>
<tr>
<td>ID</td>
<td><?php echo $wynik -> relacjaID ?></td>
</tr>
<tr>
<td>Początek</td>
<td><?php echo $FP -> pobierzDoSelecta('opis', 'punkt', 'punktID', 'punktIDStart', $wynik -> punktIDStart, false, true); ?></td>
</tr>
<tr>
<td>Początek - opis</td>
<td><input type="text" name="opisStart" size="30" value="<?php echo $wynik -> opisStart ?>"></td>
</tr>
<tr>
<td>Koniec</td>
<td><?php echo $FP -> pobierzDoSelecta('opis', 'punkt', 'punktID', 'punktIDKoniec', $wynik -> punktIDKoniec, false, true); ?></td>
</tr>
<tr>
<td>Koniec - opis</td>
<td><input type="text" name="opisKoniec" size="30" value="<?php echo $wynik -> opisKoniec ?>"></td>
</tr>
<tr>
<td>Opis</td>
<td><input type="text" name="opis" size="30" value="<?php echo $wynik -> opis ?>"></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Zmień!"></td>
</tr>

</table>
</form>
<?php
}
?>