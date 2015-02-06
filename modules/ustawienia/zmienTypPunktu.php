<?php
$wynik = $FP -> db_sq('SELECT * FROM punktTyp WHERE punktTypID = "'.(int)$_R -> punktTypID.'"');

if(isset($_P -> punktTypID)){
	if(!empty($_P -> punktTypID)){
	$zapytanie = $FP -> db_q('UPDATE punktTyp SET typ = "'.$_P -> typ.'", kolorPunkt = "'.$_P -> kolorPunkt.'" WHERE punktTypID = "'.$_P -> punktTypID.'"');

	echo $FP -> komunikat($zapytanie, 'Typ <i>'.$_P -> typ.'</i> został prawidłowo zmieniony<br><br><a href="?modul=ustawienia&co=typyPunktow">Powrót do listy typów punktów</a>', 'Wystąpił błąd podczas zmiany typu punktu <i>'.$_P -> typ.'</i><br><br><a href="?modul=ustawienia&co=typyPunktow">Powrót do listy typów punktów</a>');
	if($zapytanie)
		$FP -> log('Zmieniono typ punktu z '.$wynik -> typ.' na '.$_P -> typ);
	}
}
else{

?>
<form action="?modul=ustawienia&co=zmienTypPunktu" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Zmiana typu punktu</h3></td>
</tr>
<tr>
<td>ID</td>
<td><?php echo $wynik -> punktTypID ?><input type="hidden" name="punktTypID" value="<?php echo $wynik -> punktTypID ?>"></td>
</tr>
<tr>
<td>Typ</td>
<td><input type="text" name="typ" size="30" value="<?php echo $wynik -> typ ?>"></td>
</tr>
<tr>
<td>Kolor</td>
<td><?php echo @$FP -> pobierzKoloryTypowPunktowDoSelecta($wynik -> kolorPunkt); ?></td>
</tr>
<tr>
<tr>
<td colspan="2"><input type="submit" value="Zmień!"></td>
</tr>

</table>
</form>
<?php
}
?>