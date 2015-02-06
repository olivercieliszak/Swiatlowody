<?php
if(isset($_P -> punktIDStart)){
	$zapytanie = $FP -> db_iq('INSERT INTO relacja SET punktIDStart = "'.$_P -> punktIDStart.'", opisStart = "'.$_P -> opisStart.'", punktIDKoniec = "'.$_P -> punktIDKoniec .'", opisKoniec = "'.$_P -> opisKoniec.'", opis = "'.$_P -> opis.'"');
	$test = false;
	if($zapytanie > 0)
		$test = true;
		
	echo $FP -> komunikat($test, 'Nowa relacja <i>'.$FP -> pobierzPunkt($_P -> punktIDStart).' - '.$FP -> pobierzPunkt($_P -> punktIDKoniec).'</i> została dodana prawidłowo<br><br><a href="?modul=relacje&co=listaRelacji">Powrót do listy relacji</a>',
	'Wystąpił błąd podczas dodawania  relacji <i>'.$FP -> pobierzPunkt($_P -> punktIDStart).' - '.$FP -> pobierzPunkt($_P -> punktIDKoniec).'</i><br><br><a href="?modul=relacje&co=listaRelacji">Powrót do listy relacji</a>');
	
	if($test)
		$FP -> log('Dodano relację nr '.$zapytanie.' między punktami nr '.$_P -> punktIDStart.' a '.$_P -> punktIDKoniec);
	
}
$JSautosugestia = array();
$JSautosugestia['id'] = false;
$JSautosugestia['gps'] = false;
$JSautosugestia['typ'] = false;

?>
<form action="?modul=relacje&co=dodajRelacje" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Dodawanie nowej relacji</h3></td>
</tr>
<tr>
<td>Początek</td>
<td>
<?php
$punktIDStart = false;
if(isset($_R -> punktIDStart))
	$punktIDStart = $_R -> punktIDStart;
	
	echo '<select name="punktIDStart" id="punktIDStart" onChange="zmienKolor()">';
	$zapytanie = $FP -> db_q('SELECT punktID, kolorPunkt, typ, opis, gps FROM punkt NATURAL LEFT JOIN punktTyp WHERE punkt.punktTypID < 3 ORDER BY punktID ASC');
	while($wynik = $zapytanie -> fetch_object()){
		if(!isset($punkty[$wynik -> punktID])){
			if($wynik -> opis == '')
				$wynik -> opis = '(bez opisu)';
			
			if($wynik -> punktID == $JSautosugestia['id']){
				echo  '<option value="'.$wynik -> punktID.'" style="background-color: '.$FP -> koloryPunktowTlo($wynik -> kolorPunkt).'; color: '.$FP -> koloryPunktowTekst($wynik -> kolorPunkt).';" selected>'.$wynik -> punktID.' - '.$wynik -> typ.': '.$wynik -> opis.'</option>';
				
				$JSautosugestia['gps'] = $wynik -> gps;
			}
			else
				echo  '<option value="'.$wynik -> punktID.'" style="background-color: '.$FP -> koloryPunktowTlo($wynik -> kolorPunkt).'; color: '.$FP -> koloryPunktowTekst($wynik -> kolorPunkt).';">'.$wynik -> punktID.' - '.$wynik -> typ.': '.$wynik -> opis.'</option>';
		
			$PHPdata[$wynik -> gps] = $wynik;

		}
	}
	echo '</select>';

?> <a href="#" onClick="pokazMape('Start');"><span id="pokaz_Start">Pokaż mapę</span></a>

</td>
</tr>
<tr>
<td>Początek - opis</td>
<td><input type="text" name="opisStart" size="30"></td>
</tr>
<tr>
<td>Koniec</td>
<td>
<?php
$punktIDKoniec = false;
if(isset($_R -> punktIDKoniec))
	$punktIDKoniec = $_R -> punktIDKoniec;
	
	echo '<select name="punktIDKoniec" id="punktIDKoniec" onChange="zmienKolor()">';
	$zapytanie = $FP -> db_q('SELECT punktID, kolorPunkt, typ, opis, gps FROM punkt NATURAL LEFT JOIN punktTyp WHERE punkt.punktTypID < 3 ORDER BY punktID ASC');
	while($wynik = $zapytanie -> fetch_object()){
		if(!isset($punkty[$wynik -> punktID])){
			if($wynik -> opis == '')
				$wynik -> opis = '(bez opisu)';
			
			if($wynik -> punktID == $JSautosugestia['id']){
				echo  '<option value="'.$wynik -> punktID.'" style="background-color: '.$FP -> koloryPunktowTlo($wynik -> kolorPunkt).'; color: '.$FP -> koloryPunktowTekst($wynik -> kolorPunkt).';">'.$wynik -> punktID.' - '.$wynik -> typ.': '.$wynik -> opis.'</option>';
				
				$JSautosugestia['gps'] = $wynik -> gps;
			}
			else
				echo  '<option value="'.$wynik -> punktID.'" style="background-color: '.$FP -> koloryPunktowTlo($wynik -> kolorPunkt).'; color: '.$FP -> koloryPunktowTekst($wynik -> kolorPunkt).';">'.$wynik -> punktID.' - '.$wynik -> typ.': '.$wynik -> opis.'</option>';
		
			$PHPdata[$wynik -> gps] = $wynik;

		}
	}
	echo '</select>';
?>
 <a href="#" onClick="pokazMape('Koniec');"><span id="pokaz_Koniec">Pokaż mapę</span></a>
</td>
</tr>
<tr>
<td>Koniec - opis</td>
<td><input type="text" name="opisKoniec" size="30"></td>
</tr>
<tr>
<td>Dodatkowy opis</td>
<td><input type="text" name="opis" size="30"></td>
</tr>

<tr>
<td colspan="2"><input type="submit" value="Dodaj!"></td>
</tr>

</table>
</form>
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script src="inc/markerclusterer_compiled.js" type="text/javascript"></script>

<script>
//dane z PHP
<?php 
echo 'var PHPdata = '.json_encode($PHPdata).';';
echo 'var PHPautosugestia = '.json_encode($JSautosugestia).';';
 ?>
 //alert(print_r(PHPdata));
</script>
<script type="text/javascript">
	var strona;
	function pokazMape(tmpStrona){
		strona = tmpStrona;
		
		var stronaPrzeciwna;
		if(strona == "Start")
			stronaPrzeciwna = "Koniec";
		else
			stronaPrzeciwna = "Start";

		if($('#pokaz_'+stronaPrzeciwna).text() != "Ukryj mapę"){
			if($('#pokaz_'+strona).text() == "Pokaż mapę"){
				$('#pokaz_'+strona).text('Ukryj mapę');
				$('#mapa').show();
			}
			else{
				$('#mapa').hide();
				$('#pokaz_'+strona).text('Pokaż mapę');
			}
		
			uruchomMape();
		}
	}
	function uruchomMape(){
	var opcjeMapy = {
		mapTypeId: google.maps.MapTypeId.HYBRID,
		//predefiniowane wartosci ktore sie zmienia jak sie pojawia jakies punkty
		zoom: poczatkowyZoom,
		center: new google.maps.LatLng(poczatkowyLat, poczatkowyLon)
	};
	mapa = new google.maps.Map(document.getElementById("mapa"), opcjeMapy);
	dymek = new google.maps.InfoWindow();
	markerBounds = new google.maps.LatLngBounds();

	var listaPozycji = [];
	var markery = [];

	for(punktID in PHPdata){

			var zIndexVal = undefined;
			
			gps = rozdzielGPS(PHPdata[punktID].gps);
			
			//przesuniecie wzgledem siebie punktow o takich samych koordynatach
			while(listaPozycji[gps.lat] == gps.lon){
				gps.lat = parseFloat(gps.lat) + odlegloscX;
				gps.lon = parseFloat(gps.lon) + odlegloscX;
			}
			var opis = '<b>'+PHPdata[punktID].opis+'</b>';
			if(opis == '')
				opis = '(brak opisu)';
			var zawartoscMarkera = '<div style="white-space: nowrap">'+ opis + '<br>(' + PHPdata[punktID].typ + ' - punkt nr ' + PHPdata[punktID].punktID + ')</div><br>';
			
			//jezeli typ danych to mufa
			if(PHPdata[punktID].punktTypID == 1){
				zawartoscMarkera += '<a href="?modul=mufy&co=listaSpawow&mufaID='+ PHPdata[punktID].mufaID + '" class="zmien">Lista spawów</a><br>';
				zIndexVal = 'max';
			}
			
			//jezeli typ danych to przelacznica
			if(PHPdata[punktID].punktTypID == 2){
				zawartoscMarkera + '<a href="?modul=przelacznice&co=listaPortow&przelacznicaID='+ PHPdata[punktID].przelacznicaID + '" class="zmien">Lista portów</a><br>';
				zIndexVal = 'max';
			}
				
				
			zawartoscMarkera += '<a href="?modul=punkty&co=listaObiektow&punktID=' + PHPdata[punktID].punktID+ '" class="zmien">Co tu jest?</a><br>';
			if(PHPdata[punktID].punktTypID != 1 && PHPdata[punktID].punktTypID != 2){
				zawartoscMarkera += '<a href="?modul=mufy&co=dodajMufe&punktID=' + PHPdata[punktID].punktID+ '" class="zmien">Dodaj mufę</a><br>';
				zawartoscMarkera += '<a href="?modul=przelacznice&co=dodajPrzelacznice&punktID=' + PHPdata[punktID].punktID+ '" class="zmien">Dodaj przełącznicę</a><br>';
			}
			
			zawartoscMarkera += '<a href="?modul=kable&co=przetnijKabel&punktID=' + PHPdata[punktID].punktID+ '" class="zmien">Przetnij kabel</a><hr>';
			zawartoscMarkera += '<a href="?modul=punkty&co=zmienPunkt&punktID=' + PHPdata[punktID].punktID+ '" class="zmien">Zmień punkt</a><br>';
			zawartoscMarkera += '<a href="?modul=punkty&co=usunPunkt&punktID=' + PHPdata[punktID].punktID+ '" class="usun">Usuń punkt</a>';
			
			var tmpMarker = dodajMarker(gps.lat, gps.lon,zawartoscMarkera,kolorMarkera(PHPdata[punktID].punktTypID), zIndexVal);

			markery.push(tmpMarker);
			markerBounds.extend(new google.maps.LatLng(gps.lat, gps.lon));		
			listaPozycji[gps.lat] = gps.lon;
			
			google.maps.event.addListener(tmpMarker,"click",function(zdarzenie){
	
				$('#punktID'+strona).val(PHPdata[skroc(zdarzenie.latLng)].punktID);
				}
			);
		
		}	
	

	uruchomPo();
	mapa.fitBounds(markerBounds);
	markerClusterer = new MarkerClusterer(mapa, markery, markerClustererOptions);
	
	if(PHPautosugestia.gps != false){
		var gps = rozdzielGPS(PHPautosugestia.gps);
		mapa.setCenter(new google.maps.LatLng(gps.lat, gps.lon));
		
	}
	$('#mapa').width($('table').width());
	$('#mapa').height(window.innerHeight-$('table').height()-$('#tech').height());
	}

	function zmienKolor(){
	
	$('#punktIDStart').css('background-color', $('#punktIDStart').children(":selected").css('background-color'));
	$('#punktIDStart').css('color', znajdzKolor($('#punktIDStart').children(":selected").css('background-color')));
	$('#punktIDKoniec').css('background-color', $('#punktIDKoniec').children(":selected").css('background-color'));
	$('#punktIDKoniec').css('color', znajdzKolor($('#punktIDKoniec').children(":selected").css('background-color')));
	
	}
	zmienKolor();
	
</script>
<div id="mapa"></div>