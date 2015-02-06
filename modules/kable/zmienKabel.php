<?php
if(isset($_P -> kabelID)){
	if(!empty($_P -> kabelID)){
	$zapytanie = $FP -> db_q('UPDATE kabel SET opis = "'.$_P -> opis.'" WHERE kabelID = "'.$_P -> kabelID.'"');
	echo $FP -> komunikat($zapytanie, 'Kabel w relacji <i>'.$FP -> pobierzRelacjeKabla($_P -> kabelID).'</i> został zmieniony prawidłowo<br><br><a href="?modul=kable&co=listaKabli">Powrót do listy kabli</a>',
	'Wystąpił błąd podczas zmiany kabla w relacji <i>'.$FP -> pobierzRelacjeKabla($_P -> kabelID).'</i><br><br><a href="?modul=kable&co=listaKabli">Powrót do listy kabli</a>');
	if($zapytanie)
		$FP -> log('Kabel nr '.$_P -> kabelID.' został zmieniony');
	}
}
else{

$kabel = $FP -> db_sq('SELECT * FROM kabel WHERE kabelID = "'.(int)$_R -> kabelID.'"');

?>
<form action="?modul=kable&co=zmienKabel" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Zmiana kabla</h3><br>Zmiana punktów kabla jest możliwa<br>
tylko poprzez usunięcie i dodanie nowego kabla.</td>
</tr>
<tr>
<td>ID</td>
<td><?php echo $kabel -> kabelID ?><input type="hidden" name="kabelID" value="<?php echo $kabel -> kabelID ?>"></td>
</tr>
<?php if(isset($_COOKIE['debug'])){
if($_COOKIE['debug'] == 1){
?>
<tr>
<td>Początek</td>
<td><?php echo $FP -> pobierzDoSelecta('opis', 'punkt', 'punktID', 'punktIDStart', $kabel -> punktIDStart, false, true); ?></td>
</tr>
<tr>
<td>Koniec</td>
<td><?php echo $FP -> pobierzDoSelecta('opis', 'punkt', 'punktID', 'punktIDKoniec', $kabel -> punktIDKoniec, false, true); ?></td>
</tr>
<?php
}
}
else{
?>
<tr>
<td>Początek</td>
<td><?php echo $FP -> pobierzPunkt($kabel -> punktIDStart); ?></td>
</tr>
<tr>
<td>Koniec</td>
<td><?php echo $FP -> pobierzPunkt($kabel -> punktIDKoniec); ?></td>
</tr>
<?php
}
?>
<tr>
<td>Opis</td>
<td><input type="text" name="opis" size="30" value="<?php echo $kabel -> opis ?>"></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Zmień!"></td>
</tr>

</table>
</form>
<?php
}
?>