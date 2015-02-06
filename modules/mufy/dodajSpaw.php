<?php
$wynik = $FP -> db_sq('SELECT * FROM mufa WHERE mufaID = "'.$_R -> mufaID.'"');
 
if(isset($_P -> kabelID1)){
	
	if(@$_P -> kabelWloknoID1 == 0 || @$_P -> kabelWloknoID1 == 0)
		echo $FP -> komunikat(false, false, 'Aby dodać spaw, wybierz włókno');
	
	elseif($FP -> db_sq('SELECT COUNT(*) as ilosc FROM mufaSpaw WHERE mufaID = "'.$_P -> mufaID.'"  AND (kabelWloknoID1 = "'.$_P -> kabelWloknoID1.'" AND kabelWloknoID2 = "'.$_P -> kabelWloknoID2.'") OR (kabelWloknoID1 = "'.$_P -> kabelWloknoID2.'" AND kabelWloknoID2 = "'.$_P -> kabelWloknoID1.'")') -> ilosc > 0)
		echo $FP -> komunikat(false, false, 'Dany spaw w mufie już istnieje');
		
	else{
		//odwracamy kolejnosc tych co sa zamienione wzgledem wczesniejszych pozycji by ladnie sortowac kable
		if($FP -> db_sq('SELECT COUNT(*) as ilosc FROM mufaSpaw WHERE mufaID = "'.$_P -> mufaID.'" AND kabelID1 = "'.$_P -> kabelID2.'"') -> ilosc > 0){
			$tmp['kabelID2'] = $_P -> kabelID2;
			$tmp['kabelWloknoID2'] = $_P -> kabelWloknoID2;
			$_P -> kabelID2 = $_P -> kabelID1;
			$_P -> kabelWloknoID2 = $_P -> kabelWloknoID1;
			$_P -> kabelID1 = $tmp['kabelID2'];
			$_P -> kabelWloknoID1 = $tmp['kabelWloknoID2'];
		}
		
		$zapytanie = $FP -> db_q('INSERT INTO mufaSpaw SET mufaID = "'.$_P -> mufaID.'", kabelID1 = "'.$_P -> kabelID1.'", kabelWloknoID1 = "'.$_P -> kabelWloknoID1.'", kabelID2 = "'.$_P -> kabelID2.'", kabelWloknoID2 = "'.$_P -> kabelWloknoID2.'", opis = "'.$_P -> opis.'"');
		
		echo $FP -> komunikat($zapytanie, 'Spaw włókien w mufie <i>nr '.$_P -> mufaID .'</i><br> z kablami w relacji <i>'.$FP -> pobierzRelacjeKabla($_P -> kabelID1).'</i><br> i <i>'.$FP -> pobierzRelacjeKabla($_P -> kabelID2).'</i><br> został dodany prawidłowo<br><br><a href="?modul=mufy&co=listaSpawow&mufaID='.$_P -> mufaID.'">Powrót do listy spawów w mufie nr '.$_P -> mufaID.'</a>',
		'Wystąpił błąd podczas dodawania spawu do mufy <i>nr '.$_P -> mufaID.'</i><br><br><a href="?modul=mufy&co=listaSpawow&mufaID='.$_P -> mufaID.'">Powrót do listy spawów w mufie nr '.$_P -> mufaID.'</a>');
		if($zapytanie)
			$FP -> log('Dodano spaw włókien nr '.$_P -> kabelWloknoID1.'/'.$_P -> kabelID1.' i '.$_P -> kabelWloknoID2.'/'.$_P -> kabelID2.' w mufie nr '.$_P -> mufaID);
	}
}
?>
<script>
var uzywanyKabelID1;
var zablokowanyKabelID2;
var mufaID;

function zmienTube(poleID, poleNr, kabelID){
	
	var tuby = $.getJSONValues('ajax.php?modul=ajax&co=pobierzTuby&kabelID='+kabelID);
	var ret = '<select id="kolorTubaID'+poleNr+'" name="kolorTubaID'+poleNr+'" onChange="zmienWlokno(\''+poleID+'\',\''+poleNr+'\',\''+kabelID+'\')">';
	for(key in tuby){
		ret += '<option id="'+ key +'" style="background-color: '+ tuby[key]['kolorHTML'] +'; color: '+ znajdzKolor(tuby[key]['kolorHTML']) +';">' + tuby[key]['kolor'] + '</option>';
	}
	ret += '</select>';
	$('#TDkolorTubaID'+poleNr).html(ret);

}

function zmienWlokno(poleID, poleNr, kabelID){

	var kolorTubaID = $('#kolorTubaID'+poleNr).children(":selected").attr("id");
	var wlokna = $.getJSONValues('ajax.php?modul=ajax&co=pobierzWlokna&kabelID='+kabelID+'&kolorTubaID='+kolorTubaID+'&mufaID='+mufaID);
	
	var ret = '<select id="kabelWloknoID'+poleNr+'"  name="kabelWloknoID'+poleNr+'" onChange="zmienKolor(\''+poleNr+'\')">';
	
	for(key in wlokna){
		var dalszaRelacja = '';
		if(wlokna[key]['dalszaRelacja'] != undefined)
			dalszaRelacja = ' (' + wlokna[key]['dalszaRelacja'] + ')';
		ret += '<option value="'+ wlokna[key]['kabelWloknoID'] +'" style="background-color: '+ wlokna[key]['kolorHTML'] +'; color: '+ znajdzKolor(wlokna[key]['kolorHTML']) +';">' + wlokna[key]['kabelWloknoID'] + ' - ' + wlokna[key]['kolor'] + dalszaRelacja+'</option>';
				
	}
	
	ret += '</select>';

	$('#TDkolorWloknoID'+poleNr).html(ret);
	zmienKolor(poleNr);
}
function zmienKolor(poleNr){
	$('#kolorTubaID'+poleNr).css('background-color', $('#kolorTubaID'+poleNr).children(":selected").css('background-color'));
	$('#kolorTubaID'+poleNr).css('color', znajdzKolor($('#kolorTubaID'+poleNr).children(":selected").css('background-color')));
	$('#kabelWloknoID'+poleNr).css('background-color', $('#kabelWloknoID'+poleNr).children(":selected").css('background-color'));
	$('#kabelWloknoID'+poleNr).css('color', znajdzKolor($('#kabelWloknoID'+poleNr).children(":selected").css('background-color')));

}
//toggler ktory ma nie dopuscic do zaspawania kabla z samym sobą
function ukryjUzywanyKabel(){

	uzywanyKabelID1 = $('#kabelID1').val()
	$('#kabelID2').data('extraBox').disable(uzywanyKabelID1);
	if(zablokowanyKabelID2 > 0){
		$('#kabelID2').data('extraBox').enable(zablokowanyKabelID2);
	}
	
	zablokowanyKabelID2 = uzywanyKabelID1;
	zmienTube('#kabelID2', 2, $('#kabelID2').val());
	zmienWlokno('#kabelID2', 2, $('#kabelID2').val());
}
$( document ).ready(function() {

	
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

	//gdy zmienil sie kabel - ladujemy nowe tuby i wlokna
	$('#kabelID2').change(function(){
		zmienTube('#kabelID2', 2, $('#kabelID2').val());
		zmienWlokno('#kabelID2', 2, $('#kabelID2').val());
	});
	
});
</script>
<form action="?modul=mufy&co=dodajSpaw" method="POST">
<input type="hidden" name="mufaID" id="mufaID" value="<?php echo $_R -> mufaID ?>">
<table align="center">
<tr>
<td colspan="2"><h3>Dodawanie spawu w mufie nr <?php echo $_R -> mufaID ?></h3>Pokazywane są tylko wolne włókna<br>i kable zaczynające lub kończące się w punkcie tej mufy<br><br></td>
</tr>
<tr>
<td>Kabel A</td>
<td><?php echo $FP -> pobierzKableDoSelecta('kabelID1', false, false, 'punktIDStart = "'.$wynik -> punktID.'" OR punktIDKoniec = "'.$wynik -> punktID.'"'); ?></td>
</tr>
<tr>
<td>Tuba A</td>
<td id="TDkolorTubaID1"></td>
</tr>
<tr>
<td>Włókno A</td>
<td id="TDkolorWloknoID1"></td>
</tr>
<tr><td colspan="2"><br></td>
</tr>
<tr>
<td>Kabel B</td>
<td id="TDkabelID2"><?php echo $FP -> pobierzKableDoSelecta('kabelID2', false, false, 'punktIDStart = "'.$wynik -> punktID.'" OR punktIDKoniec = "'.$wynik -> punktID.'"'); ?></td>
</tr>
<tr>
<td>Tuba B</td>
<td id="TDkolorTubaID2"></td>
</tr>
<tr>
<td>Włókno B</td>
<td id="TDkolorWloknoID2"></td>
</tr></tr>
<tr><td colspan="2"><br></td>
</tr>
</tr>
<tr><td>Opis spawu</td>
<td><input type="text" name="opis"></td>
</tr>
<tr>
<td colspan="2"><br><input type="submit" value="Dodaj!"></td>
</tr>

</table>
</form>