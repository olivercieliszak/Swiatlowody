<?php
$wynik = $FP -> db_sq('SELECT * FROM kolorTuba WHERE kolorTubaID = "'.(int)$_R -> kolorTubaID.'"');

if(isset($_P -> kolorTubaID)){
	if(!empty($_P -> kolorTubaID)){
	$zapytanie = $FP -> db_q('UPDATE kolorTuba SET kolor = "'.$_P -> kolor.'", kolorHTML = "'.$_P -> kolorHTML.'" WHERE kolorTubaID = "'.$_P -> kolorTubaID.'"');

	echo $FP -> komunikat($zapytanie, 'Kolor <i>'.$_P -> kolor.'</i> został prawidłowo zmieniony<br><br><a href="?modul=ustawienia&co=koloryTub">Powrót do listy kolorów tub</a>', 'Wystąpił błąd podczas zmiany typu koloru <i>'.$_P -> kolor.'</i><br><br><a href="?modul=ustawienia&co=koloryTub">Powrót do listy kolorów tub</a>');
	if($zapytanie)
		$FP -> log('Zmieniono kolor tuby z '.$wynik -> kolor.' na '.$_P -> kolor);
	}
}
else{

?>
<script src='inc/spectrum.js'></script>
<link rel='stylesheet' href='inc/spectrum.css' />
<form action="?modul=ustawienia&co=zmienKolorTuby" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Zmiana koloru tuby</h3></td>
</tr>
<tr>
<td>ID</td>
<td><?php echo $wynik -> kolorTubaID ?><input type="hidden" name="kolorTubaID" value="<?php echo $wynik -> kolorTubaID ?>"></td>
</tr>
<tr>
<td>Nazwa koloru</td>
<td><input type="text" name="kolor" size="20" value="<?php echo $wynik -> kolor ?>"></td>
</tr>
<tr>
<td>Kod HTML koloru</td>
<td><input type="text" name="kolorHTML" id="kolorHTML" size="20" value="<?php echo $wynik -> kolorHTML ?>"> <script>
$("#kolorHTML").spectrum({
    preferredFormat: "hex",
    color: "<?php echo $wynik -> kolorHTML ?>",
	showButtons: false,
    showInput: true,
	hide: function(color) {
		$('#kodHTML').text(color.toHexString()); // #ff0000
	}
});
</script><div style="display: inline" id="kodHTML"><?php echo $wynik -> kolorHTML ?></div></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Zmień!"></td>
</tr>

</table>
</form>
<?php
}
?>