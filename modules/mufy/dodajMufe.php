<?php

if(isset($_P -> punktID)){
	$zapytanie0 = $FP -> db_sq('SELECT * FROM punkt WHERE punktID = "'.$_P -> punktID.'"');
	$zapytanie1 = $FP -> db_iq('INSERT INTO punkt SET gps = "'.$zapytanie0 -> gps.'", opis = "Mufa w punkcie '.$zapytanie0 -> opis.'", punktTypID = "1"');
	$zapytanie2 = $FP -> db_iq('INSERT INTO mufa SET punktID = "'.$zapytanie1.'", opis = "'.$_P -> opis.'"');
	$test = false;
	if($zapytanie2 > 0)
		$test = true;

	echo $FP -> komunikat($test, 'Nowa mufa została dodana prawidłowo<br><br><a href="?modul=mufy&co=listaMuf">Powrót do listy muf</a>',
	'Wystąpił błąd podczas dodawania mufy<br><br><a href="?modul=mufy&co=listaMuf">Powrót do listy muf</a>');
	if($test)
		$FP -> log('Dodano mufę nr '.$zapytanie2);
}
$JSautosugestia = array();
$JSautosugestia['id'] = false;
$JSautosugestia['typ'] = false;
$JSautosugestia['gps'] = false;
if(isset($_R -> punktID)){

$JSautosugestia['id'] = $_R -> punktID;

}

?>

<form action="?modul=mufy&co=dodajMufe" method="POST">

<table align="center">
<tr>
<td colspan="2"><h3>Dodawanie nowej mufy</h3></td>
</tr>
<tr>
<td>Punkt</td>
<td><?php
$punktID = false;
if(isset($_R -> punktID))
	$punktID = $_R -> punktID;
	
	echo '<select name="punktID" id="punktID" onChange="zmienKolor()">';
	$zapytanie = $FP -> db_q('SELECT punktID, punktTypID, typ, opis, gps, kolorPunkt FROM punkt NATURAL LEFT JOIN punktTyp WHERE punkt.punktTypID != 1 ORDER BY punktID ASC');
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

?></td>
</tr>
<tr>
<td>Opis</td>
<td><input type="text" name="opis" size="30"></td>
</tr>

<td colspan="2"><input type="submit" value="Dodaj!"></td>
<tr>
</tr>

</table>
</form>
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script src="inc/markerclusterer_compiled.js" type="text/javascript"></script>

<script>
<?php 
echo 'var PHPdata = '.json_encode($PHPdata).';';
echo 'var PHPautosugestia = '.json_encode($JSautosugestia).';';
 ?>
</script>
<script type="text/javascript">
jQuery(document).ready(function($) {
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
				gps.lon = parseFloat(gps.lon) + odlegloscY;
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
	
				$('#punktID').val(PHPdata[skroc(zdarzenie.latLng)].punktID);
				zmienKolor();
				}
			);
		
		}	
	
	

	uruchomPo();
	if(markerBounds)
		mapa.fitBounds(markerBounds);
	markerClusterer = new MarkerClusterer(mapa, markery, markerClustererOptions);
	
	if(PHPautosugestia.gps != false){
		var gps = rozdzielGPS(PHPautosugestia.gps);
		mapa.setCenter(new google.maps.LatLng(gps.lat, gps.lon));
		
	}
	$('#mapa').width($('table').width());
	$('#mapa').height(window.innerHeight-$('table').height()-$('#tech').height());
	google.maps.event.trigger(mapa, 'resize');

	});

	function zmienKolor(){
	
	$('#punktID').css('background-color', $('#punktID').children(":selected").css('background-color'));
	$('#punktID').css('color', znajdzKolor($('#punktID').children(":selected").css('background-color')));
	
	}
	zmienKolor();	
</script>
   <div id="mapa" style="width:102.5%; height:450px; margin: auto"></div>
