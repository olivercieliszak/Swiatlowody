<?php
$wynik = $FP -> db_sq('SELECT * FROM kolorWlokno WHERE kolorWloknoID = "'.(int)$_R -> kolorWloknoID.'"');

if(isset($_P -> kolorWloknoID)){
	if(!empty($_P -> kolorWloknoID)){
	$zapytanie = $FP -> db_q('UPDATE kolorWlokno SET kolor = "'.$_P -> kolor.'", kolorHTML = "'.$_P -> kolorHTML.'" WHERE kolorWloknoID = "'.$_P -> kolorWloknoID.'"');

	echo $FP -> komunikat($zapytanie, 'Kolor włókna - <i>'.$_P -> kolor.'</i> - został prawidłowo zmieniony<br><br><a href="?modul=ustawienia&co=koloryWlokien">Powrót do listy kolorów tub</a>', 'Wystąpił błąd podczas zmiany typu koloru <i>'.$_P -> kolor.'</i><br><br><a href="?modul=ustawienia&co=koloryWlokien">Powrót do listy kolorów tub</a>');
	if($zapytanie)
		$FP -> log('Zmieniono kolor włókna z '.$wynik -> kolor.' na '.$_P -> kolor);

	}
}
else{

?>
<script src='inc/spectrum.js'></script>
<link rel='stylesheet' href='inc/spectrum.css' />
<form action="?modul=ustawienia&co=zmienKolorWlokna" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Zmiana koloru włókna</h3></td>
</tr>
<tr>
<td>ID</td>
<td><?php echo $wynik -> kolorWloknoID ?><input type="hidden" name="kolorWloknoID" value="<?php echo $wynik -> kolorWloknoID ?>"></td>
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