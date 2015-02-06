<?php

if(isset($_P -> strona)){

	$strona = $_P -> strona;
	$punktIDMufa = $FP -> db_sq('SELECT punktID FROM mufa WHERE mufaID = "'.$_P -> mufaID.'"') -> punktID;

	//1. czy kabel nie obsluguje zadnej relacji?
	$iloscRelacji = $FP -> db_sq('SELECT COUNT(*) AS ilosc FROM relacjaWlokno NATURAL LEFT JOIN kabelWlokno WHERE kabelWlokno.kabelID = "'.$_P -> kabelID.'"') -> ilosc;
	if($iloscRelacji > 0)
		echo $FP -> komunikat(false, false, 'Nie można dodać kabla do mufy jeżeli obsługuje już jakąś relację.');

	//1a. sprawdzamy czy kabel nie jest juz w mufie
	else if($FP -> db_sq('SELECT COUNT(*) AS ilosc FROM kabel WHERE (punktIDStart = "'.$punktIDMufa.'" OR punktIDKoniec = "'.$punktIDMufa.'") AND kabelID = "'.$_P -> kabelID.'"') -> ilosc > 0)
		echo $FP -> komunikat(false, false, 'Ten kabel już jest w tej mufie.');
	
	else{
	
	//2. zmieniany jest punkt poczatkowy lub koncowy kabla na nowy
	if($strona == "poczatek")
		$zapytanieUpdate = $FP -> db_q('UPDATE kabel SET punktIDStart = "'.$punktIDMufa.'" WHERE kabelID = "'.$_P -> kabelID.'"');
	else if($strona == "koniec")
		$zapytanieUpdate = $FP -> db_q('UPDATE kabel SET punktIDKoniec = "'.$punktIDMufa.'" WHERE kabelID = "'.$_P -> kabelID.'"');

	//3a. jezeli przebieg kabla jest uzupelniony, dopisac nowy punkt poczatkowy / koncowy
	$iloscPunktow = $FP -> db_sq('SELECT COUNT(*) as ilosc FROM kabelPunkt WHERE kabelID = "'.$_P -> kabelID.'"') -> ilosc;
	if($iloscPunktow > 1){
			
			if($strona == "poczatek"){
				$zapytanie = $FP -> db_q('SELECT kabelPunktID, kolejnosc FROM kabelPunkt WHERE kabelID = "'.$_P -> kabelID.'" AND kolejnosc >= 1 ORDER BY kolejnosc ASC'); 
				while($wynik = $zapytanie -> fetch_object()){
					
					$FP -> db_q('UPDATE kabelPunkt SET kolejnosc = "'.++$wynik -> kolejnosc.'" WHERE kabelPunktID = "'.$wynik -> kabelPunktID.'"');
				}
				$zapytanieInsert = $FP -> db_iq('INSERT INTO kabelPunkt SET kabelID = "'.$_P -> kabelID.'", punktID = "'.$punktIDMufa .'", kolejnosc = "1"');
				
				
			}
			else if($strona == "koniec"){
			
				$ostatniaKolejnosc = $FP -> db_sq('SELECT kolejnosc FROM kabelPunkt WHERE kabelID = "'.$_P -> kabelID .'" ORDER BY kolejnosc DESC LIMIT 1') -> kolejnosc;

				$zapytanieInsert = $FP -> db_iq('INSERT INTO kabelPunkt SET kabelID = "'.$_P -> kabelID.'", punktID = "'.$punktIDMufa .'", kolejnosc = "'.( $ostatniaKolejnosc + 1 ).'"');

			}
	}
		
	echo $FP -> komunikat($zapytanieUpdate, 'Kabel nr '.$_P -> kabelID.' został prawidłowo dodany do mufy nr '.$_P -> mufaID, 'Wystąpił błąd przy dodawaniu kabla do mufy');
	if($zapytanieUpdate)
		$FP -> log('Kabel nr '.$_P -> kabelID.' został dodany do mufy nr '.$_P -> mufaID);
	}
}
else{
?>
<form action="?modul=mufy&co=dodajKabel" method="POST">
<input type="hidden" name="mufaID" value="<?php echo $_R -> mufaID ?>">
<table align="center">
<tr>
<td colspan="2"><h3>Dodawanie nowego kabla do mufy nr <?php echo $_R -> mufaID ?></h3></td>
</tr>
<tr>
<td>Kabel</td>
<td>
<?php
		$zapytanie = $FP -> db_q('SELECT kabelID, punktIDStart, punktIDKoniec, opis FROM kabel ORDER BY kabelID ASC');
		$JSdata = array();
		echo '<select name="kabelID" id="kabelID" onChange="zmienDane();">';
		while($wynik = $zapytanie -> fetch_object()){
				echo  '<option value="'.$wynik -> kabelID.'">'.$wynik -> kabelID.': '.$FP -> pobierzPunkt($wynik -> punktIDStart).' - '.$FP -> pobierzPunkt($wynik -> punktIDKoniec).'</option>';
				//$JSdata[$wynik -> kabelID] = (array)$wynik;
				$JSdata[$wynik -> kabelID]['punktIDStart']['id'] = $wynik -> punktIDStart;
				$JSdata[$wynik -> kabelID]['punktIDStart']['opis'] = str_replace('<br>', '', $FP -> pobierzPunkt($wynik -> punktIDStart));
				$JSdata[$wynik -> kabelID]['punktIDKoniec']['id'] = $wynik -> punktIDKoniec;
				$JSdata[$wynik -> kabelID]['punktIDKoniec']['opis'] = str_replace('<br>', '', $FP -> pobierzPunkt($wynik -> punktIDKoniec));
				$JSdata[$wynik -> kabelID]['opis'] = $wynik -> opis;
				
		}
		echo '</select>';
	?>
</td>
</tr>
<tr>
<td>Wybierz stronę:</td>
<td>
<input type="radio" name="strona" value="poczatek">
Początek: <b><span id="poczatek"></span></b><br>
<input type="radio" name="strona" value="koniec">
Koniec: <b><span id="koniec"></span></b><br>
<script><?php echo 'var PHPdata = '.json_encode($JSdata); ?></script>
<script>
	function zmienDane(){
			kabelID = $('#kabelID').val();
			$('#poczatek').text(PHPdata[kabelID]['punktIDStart']['opis']);
			$('#koniec').text(PHPdata[kabelID]['punktIDKoniec']['opis']);
	
	}
		zmienDane();
		$( document ).ready(function() {
  // Handler for .ready() called.
	$('select').val($("select option:first").val());
	$('input').prop('checked', false);  
});
</script>
</td>
</tr>

<tr>
<td colspan="2"><input type="submit" value="Dodaj!"></td>
</tr>

</table>
</form>
<?php
}
?>