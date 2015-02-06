<?php
$wynik = $FP -> db_sq('SELECT * FROM przelacznica WHERE przelacznicaID = "'.$_R -> przelacznicaID.'"');
 
if(isset($_P -> kabelID)){

	
	if(@$_P -> kabelWloknoID == 0)
		echo $FP -> komunikat(false, false, 'Aby dodać port, wybierz włókno');
	
	elseif($FP -> db_sq('SELECT COUNT(*) as ilosc FROM przelacznicaPort WHERE przelacznicaID = "'.$_P -> przelacznicaID.'"  AND kabelWloknoID = "'.$_P -> kabelWloknoID.'"') -> ilosc > 0)
		echo $FP -> komunikat(false, false, 'Dane włókno w przełącznicy już zostało wyprowadzone');
		
	else{
	
		$zapytanie = $FP -> db_q('INSERT INTO przelacznicaPort SET przelacznicaID = "'.$_P -> przelacznicaID.'", kabelID = "'.$_P -> kabelID.'", kabelWloknoID = "'.$_P -> kabelWloknoID.'", port = "'.$_P -> port.'", typ = "'.$_P -> typ.'"');
		
		echo $FP -> komunikat($zapytanie, 'Port '.$_P -> typ.' nr '.$_P -> port.' w przełącznicy <i>nr '.$_P -> przelacznicaID .'</i><br> został prawidłowo połączony z kablem w relacji<br><i>'.$FP -> pobierzRelacjeKabla($_P -> kabelID).'</i><br><br><a href="?modul=przelacznice&co=listaPortow&przelacznicaID='.$_P -> przelacznicaID.'">Powrót do listy portów w przełącznicy nr '.$_P -> przelacznicaID.'</a>',
		'Wystąpił błąd podczas dodawania portu do przełącznicy <i>nr '.$_P -> przelacznicaID.'</i><br><br><a href="?modul=przelacznice&co=listaPortow&przelacznicaID='.$_P -> przelacznicaID.'">Powrót do listy portów w przełącznicy nr '.$_P -> przelacznicaID.'</a>');
		if($zapytanie)
			$FP -> log('Port '.$_P -> typ.' nr '.$_P -> port.' w przełącznicy <i>nr '.$_P -> przelacznicaID .'</i> został prawidłowo połączony z kablem nr '.$_P -> kabelID);
		
	}
	
}
?>
<script>
var uzywanyKabelID1;
var zablokowanyKabelID2;
var przelacznicaID;

function zmienTube(poleID, kabelID){
	
	var tuby = $.getJSONValues('ajax.php?modul=przelacznice&co=pobierzTuby&kabelID='+kabelID);
	var ret = '<select id="kolorTubaID" name="kolorTubaID" onChange="zmienWlokno(\''+poleID+'\',\'\',\''+kabelID+'\')">';
	for(key in tuby){
		ret += '<option id="'+ key +'" style="background-color: '+ tuby[key]['kolorHTML'] +'; color: '+ znajdzKolor(tuby[key]['kolorHTML']) +';">' + tuby[key]['kolor'] + '</option>';
	}
	ret += '</select>';
	$('#TDkolorTubaID').html(ret);
	
}

function zmienWlokno(poleID, kabelID){
	kabelID = $('#kabelID').val();
	var kolorTubaID = $('#kolorTubaID').children(":selected").attr("id");
	
	
	var wlokna = $.getJSONValues('ajax.php?modul=przelacznice&co=pobierzWlokna&kabelID='+kabelID+'&kolorTubaID='+kolorTubaID+'&przelacznicaID='+przelacznicaID);
	
	var ret = '<select id="kabelWloknoID"  name="kabelWloknoID" onChange="zmienKolor()">';
	for(key in wlokna){
		var dalszaRelacja = '';
		if(wlokna[key]['dalszaRelacja'] != undefined)
			dalszaRelacja = ' (' + wlokna[key]['dalszaRelacja'] + ')';

		ret += '<option value="'+ wlokna[key]['kabelWloknoID'] +'" style="background-color: '+ wlokna[key]['kolorHTML'] +'; color: '+ znajdzKolor(wlokna[key]['kolorHTML']) +';">' + wlokna[key]['kabelWloknoID'] + ' - ' + wlokna[key]['kolor'] + dalszaRelacja + '</option>';
	}
	ret += '</select>';

	$('#TDkolorWloknoID').html(ret);
	zmienKolor();
	return kabelID;
}
function zmienKolor(){
	$('#kolorTubaID').css('background-color', $('#kolorTubaID').children(":selected").css('background-color'));
	$('#kolorTubaID').css('color', znajdzKolor($('#kolorTubaID').children(":selected").css('background-color')));
	$('#kabelWloknoID').css('background-color', $('#kabelWloknoID').children(":selected").css('background-color'));
	$('#kabelWloknoID').css('color', znajdzKolor($('#kabelWloknoID').children(":selected").css('background-color')));

}

$( document ).ready(function() {
	//pobieramy domyslne wartosci
	przelacznicaID = $('#przelacznicaID').val();
	zmienTube('#kabelID', $('#kabelID').val());
	zmienWlokno('#kabelID', $('#kabelID').val());
	
	//gdy zmienil sie kabel - ladujemy nowe tuby i wlokna
	$('#kabelID').change(function(){
		zmienTube('#kabelID', $('#kabelID').val());
		zmienWlokno('#kabelID', $('#kabelID').val());
	});

});
</script>
<form action="?modul=przelacznice&co=dodajPort" method="POST">
<input type="hidden" name="przelacznicaID" id="przelacznicaID" value="<?php echo $_R -> przelacznicaID ?>">
<table align="center">
<tr>
<td colspan="2"><h3>Dodawanie portu w przełącznicy nr <?php echo $_R -> przelacznicaID ?></h3>Pokazywane są tylko wolne włókna<br>i kable zaczynające lub kończące się w punkcie tej przełącznicy<br><br></td>
</tr>
<tr><td>Nr portu</td>
<td><input type="text" name="port"></td>
</tr>
<tr><td>Typ portu</td>
<td><input type="text" name="typ" value="SC/UPC" onClick="$(this).val('')"></td>
</tr>
<tr>
<td>Kabel</td>
<td><?php echo $FP -> pobierzKableDoSelecta('kabelID', false, false, 'punktIDStart = "'.$wynik -> punktID.'" OR punktIDKoniec = "'.$wynik -> punktID.'"'); ?></td>
</tr>
<tr>
<td>Tuba</td>
<td id="TDkolorTubaID"></td>
</tr>
<tr>
<td>Włókno</td>
<td id="TDkolorWloknoID"></td>

<tr>
<td colspan="2"><br><input type="submit" value="Dodaj!"></td>
</tr>

</table>
</form>