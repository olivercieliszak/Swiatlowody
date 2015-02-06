<?php
if(isset($_G -> co2)){
	if($_G -> co2 == 'dodaj'){
		if(isset($_P -> kolor)){
			if(!empty($_P -> kolor)){
			
			if(empty($_P -> kolorHTML))
				$_P -> kolorHTML = '#000000';
			
			$zapytanie = $FP -> db_q('INSERT INTO kolorTuba SET kolor = "'.$_P -> kolor.'", kolorHTML = "'.$_P -> kolorHTML.'"');

			echo $FP -> komunikat($zapytanie, 'Nowy kolor tuby - <i>'.$_P -> kolor.'</i> - został dodany prawidłowo', 'Wystąpił błąd podczas dodawania koloru <i>'.$_P -> kolor.'</i>');
			if($zapytanie)
				$FP -> log('Dodano nowy kolor tuby - '.$_P -> kolor);
			}
		}
	}
	
}
?>
<script src='inc/spectrum.js'></script>
<link rel='stylesheet' href='inc/spectrum.css' />
<table align="center">
<tr>
<td colspan="5"><h3>Lista kolorów tub</h3></td>
</tr>
<tr>
<td><b>ID</b></td>
<td><b>Nazwa koloru</b></td>
<td><b>Kod HTML koloru</b></td>
<td></td>
</tr>

<?php
$zapytanie = $FP -> db_q('SELECT kolorTubaID, kolor, kolorHTML FROM kolorTuba ORDER BY kolorTubaID ASC');
while($wynik = $zapytanie -> fetch_object()){
echo '
<tr>
<td>'.$wynik -> kolorTubaID.'</td>
<td>'.$wynik -> kolor.'</td>
<td style="background-color: '.$wynik -> kolorHTML.';border: 1px solid #656565; color: '.$FP -> znajdzKolor($wynik -> kolorHTML).'">'.$wynik -> kolorHTML.'</td>
<td><a href="?modul=ustawienia&co=zmienKolorTuby&kolorTubaID='.$wynik -> kolorTubaID.'">Zmień</a> | <a href="?modul=ustawienia&co=usunKolorTuby&kolorTubaID='.$wynik -> kolorTubaID.'">Usuń</a></td>
</tr>';
}
?>
</table>
<br><br>
<form action="?modul=ustawienia&co=koloryTub&co2=dodaj" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Dodawanie nowego koloru tuby</h3></td>
</tr>
<tr>
<td>Nazwa koloru</td>
<td><input type="text" name="kolor" size="20"></td>
</tr>
<tr>
<td>Kod HTML koloru</td>
<td><input type="text" name="kolorHTML" id="kolorHTML" size="20"> <script>
$("#kolorHTML").spectrum({
    preferredFormat: "hex",
    color: "#000000",
	showButtons: false,
    showInput: true,
	hide: function(color) {
		$('#kodHTML').text(color.toHexString()); // #ff0000
	}
});
</script><div style="display: inline" id="kodHTML">#000000</div></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Dodaj!"></td>
</tr>
</table>
</form>
