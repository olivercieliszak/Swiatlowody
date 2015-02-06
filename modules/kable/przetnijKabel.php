<?php
if(isset($_P -> punktID)){
	$kabelIDStary = $_P -> kabelID;
	$kabelStary = $FP -> db_sq('SELECT * FROM kabel WHERE kabelID = "'.$kabelIDStary.'"');
	$nowyPunktIDPoczatek = $_P -> punktID;
	$nowyPunktIDKoniec = $kabelStary -> punktIDKoniec;
	if($nowyPunktIDPoczatek == $kabelStary -> punktIDStart || $nowyPunktIDPoczatek == $nowyPunktIDKoniec){
	
		echo $FP -> komunikat(false, false, 'Nie można przeciąć kabla na jego końcu lub początku');
		
	}
	else{
	
	//0. Wykonujemy kopię zapasową bazy danych
	$wykonajKopie = $FP -> backup_tables('./backup/', 'Przecinanie kabla nr '.$kabelIDStary);

	//1. W tym punkcie dodajemy mufę
	$zapytanie1a = $FP -> db_sq('SELECT * FROM punkt WHERE punktID = "'.$nowyPunktIDPoczatek.'"');
	$zapytanie1b = $FP -> db_iq('INSERT INTO punkt SET gps = "'.$zapytanie1a -> gps.'", opis = "Mufa w punkcie '.$zapytanie1a -> opis.'", punktTypID = "1"');
	//od razu zmieniamy nowy punkt na punkt w ktorym znajduje sie tylko mufa, nie bedzie trzeba pozniej wprowadzac kabla do mufy
	$nowyPunktIDPoczatek = $zapytanie1b;
	$zapytanie1c = $FP -> db_iq('INSERT INTO mufa SET punktID = "'.$nowyPunktIDPoczatek.'", opis = "'.$_P -> opis.'"');

	$mufaID = $zapytanie1c;
	$FP -> log('Została dodana mufa nr '.$mufaID);

	//2. Kabel zmienia relacje, punktem koncowym jest punkt nowej mufy
	$zapytanie2 = $FP -> db_q('UPDATE kabel SET punktIDKoniec = "'.$nowyPunktIDPoczatek.'" WHERE kabelID = "'.$kabelIDStary.'"');
	//echo 'Zmieniono kabel<br>';
	$FP -> log('Kabel nr '.$kabelIDStary.' zmienił punkt końcowy');
	
	//3. Powstaje nowy kabel z punktem startowym ktorym jest punkt nowej mufy oraz punktem koncowym - punkt koncowy poprzedniego kabla
	$zapytanie3 = $FP -> db_iq('INSERT INTO kabel set punktIDStart = "'.$nowyPunktIDPoczatek.'", punktIDKoniec = "'.$nowyPunktIDKoniec.'", iloscWlokien = "'.$kabelStary -> iloscWlokien.'", opis = "'.$kabelStary -> opis.' | Wycięty z kabla '.$kabelIDStary.'"');
	//echo 'Dodano nowy kabel<br>';

	$kabelIDNowy = $zapytanie3;
	$FP -> log('Został dodany kabel nr '.$kabelIDNowy);
	
	$iloscWlokien = 0;
	//3a. przenosimy wlokna
	$zapytanie3a1 = $FP -> db_q('SELECT kolorTubaID, kolorWloknoID FROM kabelWlokno WHERE kabelID = "'.$kabelIDStary.'"');
	while($wynik = $zapytanie3a1 -> fetch_object()){
		$zapytanie3a2 = $FP -> db_q('INSERT INTO kabelWlokno SET kabelID = "'.$kabelIDNowy.'", kolorTubaID = "'.$wynik -> kolorTubaID.'", kolorWloknoID = "'.$wynik -> kolorWloknoID.'"');
		$iloscWlokien++;
	}
	//echo 'Przeniesiono włókna<br>';
	$FP -> log('Zostały dodane '.$iloscWlokien.' włókna do kabla nr '.$kabelIDNowy);
	
	//3a2. zmieniamy kabelID i kabelWloknoID w mufach do ktorych podlaczone sa te kable
	//3a2.1 - wybieramy mufy w ktorych zaczyna sie nasz kabel i zamieniamy kabel docelowy na nowy kabel
	$zapytanie3a2 = $FP -> db_q('SELECT * FROM mufaSpaw WHERE kabelID1 = "'.$kabelIDStary.'"');
	while ($wynik = $zapytanie3a2 -> fetch_object()){
		
		$zapytanie3a3 = $FP -> db_sq('SELECT kolorTubaID, kolorWloknoID FROM kabelWlokno WHERE kabelWloknoID = "'.$wynik -> kabelWloknoID1.'"');
		$zapytanie3a4 = $FP -> db_sq('SELECT kabelWloknoID FROM kabelWlokno WHERE kabelID = "'.$kabelIDNowy.'" AND kolorTubaID = "'.$zapytanie3a3 -> kolorTubaID.'" AND kolorWloknoID = "'.$zapytanie3a3 -> kolorWloknoID.'"');

		$FP -> db_q('UPDATE mufaSpaw SET kabelID1 = "'.$kabelIDNowy.'", kabelWloknoID1 = "'.$zapytanie3a4 -> kabelWloknoID.'", opis = "'.$wynik -> opis.' | Dawny kabel '.$kabelIDStary.'" WHERE kabelWloknoID1 = "'.$wynik -> kabelWloknoID1.'"');

	}	
	//echo 'Zmieniono kable w mufach<br>';
	$FP -> log('Przy przecinaniu kabla zmieniono informacje o kablach w mufach');
	//3a2.1 - wybieramy mufy w ktorych konczy sie nasz kabel
		//$zapytanie3a2 = $
	
	//3b. przenosimy punkty posrednie od punktID do konca
	//uzywamy danych bezposrednio z posta bo nowyPunktIDPoczatek zostalo zmienione przy zakladaniu mufy
	$staraKolejnosc = $FP -> db_sq('SELECT kolejnosc FROM kabelPunkt WHERE kabelID = "'.$kabelIDStary.'" AND punktID = "'.$_P -> punktID.'"') -> kolejnosc;
	
	//dodajemy punkt w którym nastapiło przecięcie (czyli punkt naszej mufy) - 2x - do starego i nowego kabla
	$kolejnosc = 1;
	$FP -> db_q('INSERT INTO kabelPunkt SET kabelID = "'.$kabelIDNowy.'", kolejnosc = "'.$kolejnosc++.'", punktID = "'.$nowyPunktIDPoczatek.'"');
	
	//dodajemy obiekt w ktorym znajduje sie mufa (np studnie)
	$punktZalezny = $FP -> db_sq('SELECT punktID FROM kabelPunkt WHERE kabelID = "'.$kabelIDStary.'" AND kolejnosc = "'.$staraKolejnosc.'"') -> punktID;
	$FP -> db_q('INSERT INTO kabelPunkt SET kabelID = "'.$kabelIDNowy.'", kolejnosc = "'.$kolejnosc++.'", punktID = "'.$punktZalezny.'"');

	$zapytanie3b = $FP -> db_q('SELECT kabelPunktID FROM kabelPunkt WHERE kabelID = "'.$kabelIDStary.'" AND kolejnosc > "'.$staraKolejnosc.'" ORDER BY kolejnosc ASC');
	while($wynik = $zapytanie3b -> fetch_object()){
		
		$FP -> db_q('UPDATE kabelPunkt SET kabelID = "'.$kabelIDNowy.'", kolejnosc = "'.$kolejnosc++.'" WHERE kabelPunktID = "'.$wynik -> kabelPunktID.'"');
		
	}
	$FP -> db_q('INSERT INTO kabelPunkt SET kabelID = "'.$kabelIDStary.'", kolejnosc = "'. ++$staraKolejnosc .'", punktID = "'.$nowyPunktIDPoczatek.'"');
	$FP -> log('Punkty pośrednie starego kabla nr '.$kabelIDStary.' zostały przeniesione do nowego kabla nr '.$kabelIDNowy);
	//echo 'Przeniesiono punkty pośrednie do nowego kabla<br>';
	
	//4. Dodawane są spawy na wybranych wloknach (mozna odznaczyc te wlokna na ktorych nie ma relacji logicznych)
	foreach($_P -> zaspawane as $kabelWloknoIDStary){
		//4a. Pobieramy wlokna o tych samych kolorach z poprzedniego kabla i laczymy z wloknami o tych samych kolorach w nowym kablu
		$zapytanie4a1 = $FP -> db_sq('SELECT kolorTubaID, kolorWloknoID FROM kabelWlokno WHERE kabelWloknoID = "'.$kabelWloknoIDStary.'" AND kabelID = "'.$kabelIDStary.'"');
		$zapytanie4a2 = $FP -> db_sq('SELECT kabelWloknoID FROM kabelWlokno WHERE kabelID = "'.$kabelIDNowy.'" AND kolorTubaID = "'.$zapytanie4a1 -> kolorTubaID.'" AND kolorWloknoID = "'.$zapytanie4a1 -> kolorWloknoID.'"');
		$kabelWloknoIDNowy = $zapytanie4a2 -> kabelWloknoID;
		$FP -> db_q('INSERT INTO mufaSpaw SET mufaID = "'.$mufaID.'", kabelID1 = "'.$kabelIDStary.'", kabelWloknoID1 = "'.$kabelWloknoIDStary.'", kabelID2 = "'.$kabelIDNowy.'", kabelWloknoID2 = "'.$kabelWloknoIDNowy.'"');
		$FP -> log('Został dodany spaw włókien nr '.$kabelWloknoIDStary.'/'.$kabelIDStary.' i nr '.$kabelWloknoIDNowy.'/'.$kabelIDNowy.' w mufie nr '.$mufaID);

	
	//5. Do istniejacych relacji zostaje dopisany nowy kabel zaraz za starym
	//5a. Pobieramy kolejnosc punktu w ktorym nastapilo przeciecie
		$zapytanie5a1 = $FP -> db_sq('SELECT relacjaID, kolejnosc FROM relacjaWlokno WHERE kabelWloknoID = "'.$kabelWloknoIDStary.'"');
		//sprawdzamy czy zaspawane wlokno bylo w jakiejkolwiek relacji. jezeli nie, nie ma co go dopisywac.
		if(isset($zapytanie5a1 -> kolejnosc)){
			$zapytanie5a2 = $FP -> db_q('SELECT relacjaWloknoID, kolejnosc FROM relacjaWlokno WHERE relacjaID = "'.$zapytanie5a1 -> relacjaID.'" AND kolejnosc > '.$zapytanie5a1 -> kolejnosc.' ORDER BY kolejnosc ASC'); 
			while($wynik = $zapytanie5a2 -> fetch_object()){
				
				$FP -> db_q('UPDATE relacjaWlokno SET kolejnosc = "'.($wynik -> kolejnosc + 1).'" WHERE relacjaWloknoID = "'.$wynik -> relacjaWloknoID.'"');
				
			}
			//dopisujemy nowe wlokno do relacji
			$zapytanie5a3 = $FP -> db_q('INSERT INTO relacjaWlokno SET relacjaID = "'.$zapytanie5a1 -> relacjaID.'", kabelWloknoID = "'.$kabelWloknoIDNowy.'", kolejnosc = "'.($zapytanie5a1 -> kolejnosc + 1).'"');
			$FP -> log('Relacja nr '.$zapytanie5a1 -> relacjaID.' została zaktualizowana o nowe włókno');
		}
	}
//	echo 'Dodano włókna do relacji<br>';
	echo $FP -> komunikat(true, 'Kabel został przecięty prawidłowo:<br>
	1. Została wykonana kopia zapasowa bazy danych.<br>Wersja kopii zapasowej: '.$wykonajKopie.'<br>
	2. Została utworzona nowa mufa nr '.$mufaID.' w nowym punkcie nr '.$nowyPunktIDPoczatek.'<br>
	3. Istniejący kabel zmienił relację<br>
	4. Powstał nowy kabel z wpisanymi już włóknami<br>
	5. Zostały dodane wybrane spawy<br>
	6. Nowy kabel został dopisany do relacji, które przez niego są zestawione<br><br>
	<a href="?modul=kable&co=listaKabli">Powrót do listy kabli</a>');
	$FP -> log('Kabel nr '.$kabelIDStary.' został przecięty');
	}
}
?>
<script>
function pobierzKable(punktID){
	
	var kable = $.getJSONValues('ajax.php?modul=ajax&co=pobierzKable&punktID='+punktID);
	var ret = '<select id="kabelID" name="kabelID" onChange="wloknaWKablu(this.value)">';
	var selected = getUrlParameter('kabelID');

	for(key in kable){
		if(key == selected)
			ret += '<option value="'+ key +'" selected>'+ kable[key] + '</option>';
		else
			ret += '<option value="'+ key +'">'+ kable[key] + '</option>';
	}
	ret += '</select>';
	$('#kabel').html(ret);

}

function wloknaWKablu(kabelID){


	var ret = '<table><tr><td>Tuba</td><td>Włókno</td><td>Relacja</td><td>Zaspawane?<br><a href="#" onclick="zaznaczWszystkie()"><u>Zaznacz wszystkie</u></a></td></tr>';
	
	var tuby = $.getJSONValues('ajax.php?modul=ajax&co=pobierzTuby&kabelID='+kabelID);
	for(key in tuby){

	
		var kolorTubaID = key;
		var wlokna = $.getJSONValues('ajax.php?modul=ajax&co=pobierzWlokna&kabelID='+kabelID+'&kolorTubaID='+kolorTubaID);
		
		for(keyWlokna in wlokna){
			ret += '<tr><td style="background-color: '+ tuby[key]['kolorHTML'] +'; color: '+ znajdzKolor(tuby[key]['kolorHTML']) +';">' + tuby[key]['kolor'] + '</td>';

			ret += '<td style="background-color: '+ wlokna[keyWlokna]['kolorHTML'] +'; color: '+ znajdzKolor(wlokna[keyWlokna]['kolorHTML']) +';">' + wlokna[keyWlokna]['kabelWloknoID'] + ' - ' + wlokna[keyWlokna]['kolor'] + '</option>';
			
			
			if(wlokna[keyWlokna]['relacja']){
				ret += '<td>'+wlokna[keyWlokna]['relacja']+'</td>';
				ret += '<td><input type="checkbox" class="zaspawane" name="zaspawane[]" value="'+wlokna[keyWlokna]['kabelWloknoID']+'" required checked></td></tr>';
				
			}
			else{
				
				ret += '<td></td>';
				ret += '<td><input type="checkbox" class="zaspawane" name="zaspawane[]" value="'+wlokna[keyWlokna]['kabelWloknoID']+'"></td></tr>';

			}

		}

	}
	

	ret += '</table>';
	$('#zaspawaneWlokna').html(ret);
	
}
function zaznaczWszystkie(){

	$('.zaspawane').prop('checked', true);

}
$( document ).ready(function() {

	pobierzKable($('#punktID').val());
	wloknaWKablu($('#kabelID').val());
	$('select[name="punktID"]').change(function (){
		pobierzKable($(this).val());
		wloknaWKablu($('#kabelID').val());
		pokoloruj_wiersze();
	});
	/*
	//przechowywanie danych z kabelID2
	$('#kabelID2').extraBox({ attribute: 'value' });
	
	//pobieramy domyslne wartosci
	mufaID = $('#mufaID').val();
	zmienTube('#kabelID1', 1, $('#kabelID1').val());
	zmienWlokno('#kabelID1', 1, $('#kabelID1').val());
	ukryjUzywanyKabel();
	
	//gdy zmienil sie kabel - ladujemy nowe tuby i wlokna
	$('#kabelID1').change(function(){
		zmienTube('#kabelID1', 1, $('#kabelID1').val());
		zmienWlokno('#kabelID1', 1, $('#kabelID1').val());
		ukryjUzywanyKabel();
	});
	*/

});
</script>

<form action="?modul=kable&co=przetnijKabel" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Dodawanie przecięcia kabla</h3></td>
</tr>
<tr>
<td>Punkt przecięcia</td>
<td><?php echo $FP -> pobierzPunktyDoSelecta($_R -> punktID); ?></td>
</tr>
<tr>
<td>Opis mufy</td>
<td><input type="text" name="opis" size="30"></td>
</tr>
<tr>
<td>Kabel</td>
<td id="kabel"></td>
</tr>
<tr>
<td colspan="2" style="text-align: center"><br><b>Wybierz zaspawane włókna</b><br>Nie można odznaczyć włókien, które realizują relację<br><br></td>
</tr>
<tr>
<td colspan="2" id="zaspawaneWlokna">

</td>
</tr>
<tr>
<td colspan="2"><input type="submit" class="usun" value="Ciach!"></td>
</tr>

</table>
</form>
