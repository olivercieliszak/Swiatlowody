<?php
$mufa = $FP -> db_sq('SELECT * FROM mufa WHERE mufaID = "'.(int)$_R -> mufaID.'"');

if(isset($_P -> mufaID)){
	if(!empty($_P -> mufaID)){
	$zapytanie = $FP -> db_q('UPDATE mufa SET  punktID = "'.$_P -> punktID .'", opis = "'.$_P -> opis.'" WHERE mufaID = "'.$_P -> mufaID.'"');
	echo $FP -> komunikat($zapytanie, 'Mufa nr '.$_P -> mufaID.' w punkcie <i>'.$FP -> pobierzPunkt($_P -> punktID).'</i> została zmieniona prawidłowo<br><br><a href="?modul=mufy&co=listaMuf">Powrót do listy muf</a>',
	'Wystąpił błąd podczas zmiany mufy<br><br><a href="?modul=mufy&co=listaMuf">Powrót do listy muf</a>');
	if($zapytanie)	
		$FP -> log('Zmieniono mufę nr '.$_P -> mufaID);
	}
}
else{


?>
<form action="?modul=mufy&co=zmienMufe" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Zmiana mufy</h3></td>
</tr>
<tr>
<td>ID</td>
<td><?php echo $mufa -> mufaID ?><input type="hidden" name="mufaID" value="<?php echo $mufa -> mufaID ?>"></td>
</tr>
<tr>
<td>Punkt</td>
<td><?php echo $FP -> pobierzDoSelecta('opis', 'punkt', 'punktID', 'punktID', $mufa -> punktID, false, true); ?></td>
</tr>
<tr>
<td>Opis</td>
<td><input type="text" name="opis" size="30" value="<?php echo $mufa -> opis ?>"></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Zmień!"></td>
</tr>

</table>
</form>
<?php
}
?>