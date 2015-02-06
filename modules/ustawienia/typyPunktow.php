<?php
if(isset($_G -> co2)){
	if($_G -> co2 == 'dodaj'){
		if(isset($_P -> typ)){
			if(!empty($_P -> typ)){
			$zapytanie = $FP -> db_q('INSERT INTO punktTyp SET typ = "'.$_P -> typ.'", kolorPunkt = "'.$_P -> kolorPunkt.'"');

			echo $FP -> komunikat($zapytanie, 'Nowy typ <i>'.$_P -> typ.'</i> dodany prawidłowo', 'Wystąpił błąd podczas dodawania typu <i>'.$_P -> typ.'</i>');
			if($zapytanie)
				$FP -> log('Dodano nowy typ punktu - '.$_P -> typ);
			
			}
		}
	}
	
}
?>
<table align="center">
<tr>
<td colspan="5"><h3>Lista typów punktów</h3></td>
</tr>
<tr>
<td><b>ID</b></td>
<td><b>Opis</b></td>
<td><b>Kolor</b></td>
<td></td>
</tr>

<?php
$zapytanie = $FP -> db_q('SELECT punktTypID, typ, kolorPunkt FROM punktTyp ORDER BY punktTypID ASC');
while($wynik = $zapytanie -> fetch_object()){
	echo '
	<tr>
	<td>'.$wynik -> punktTypID.'</td>
	<td>'.$wynik -> typ.'</td>
	<td style="background-color: '.$FP -> koloryPunktowTlo($wynik -> kolorPunkt).'; color: '.$FP -> koloryPunktowTekst($wynik -> kolorPunkt).';">'.$wynik -> kolorPunkt.'</td>
	<td><a href="?modul=ustawienia&co=zmienTypPunktu&punktTypID='.$wynik -> punktTypID.'">Zmień</a> | <a href="?modul=ustawienia&co=usunTypPunktu&punktTypID='.$wynik -> punktTypID.'">Usuń</a></td>
	</tr>';
}
?>
</table>
<br><br>
<form action="?modul=ustawienia&co=typyPunktow&co2=dodaj" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Dodawanie nowego typu punktu</h3></td>
</tr>
<tr>
<td>Typ</td>
<td><input type="text" name="typ" size="20"></td>
</tr>
<tr>
<td>Kolor</td>
<td><?php echo $FP -> pobierzKoloryTypowPunktowDoSelecta(); ?></td>
</tr>
<tr>
<tr>
<td colspan="2"><input type="submit" value="Dodaj!"></td>
</tr>

</table>
</form>
