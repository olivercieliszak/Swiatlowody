<?php
$PHPgps = array('gps' => '');
$PHPdata = array();
$opis = '';
$gps = '';
$punktTypID = '';
if(isset($_P -> gps)){
	$gps = $_P -> gps;
	$opis = $_P -> opis;
	$punktTypID = $_P -> punktTypID;
	
	if($_P -> punktTypID == 1)
		echo $FP -> komunikat(false, false, 'Aby dodać nową mufę musisz wejść w Mufy a następnie wybrać Dodaj mufę');
	else if($_P -> punktTypID == 2)
		echo $FP -> komunikat(false, false, 'Aby dodać nową przełącznicę musisz wejść w Przełącznice a następnie wybrać Dodaj przełącznicę');

	else if(!empty($_P -> gps)){

	$zapytanie = $FP -> db_iq('INSERT INTO punkt SET gps = "'.$_P -> gps.'", opis = "'.$_P -> opis .'", punktTypID = "'.$_P -> punktTypID.'"');
	$test = false;
	if($zapytanie > 0)
		$test = true;
		
	echo $FP -> komunikat($test, 'Nowy punkt <i>'.$_P -> opis.'</i> dodany prawidłowo<br><br><a href="?modul=punkty&co=listaPunktow">Wróć do listy
	punktów</a>', 'Wystąpił błąd podczas dodawania punktu <i>'.$_P -> opis.'</i>');
	if($test)
		$FP -> log('Dodano punkt nr '.$zapytanie.' - '.$_P -> opis);
	
	$gps = '';
	$opis = '';
	
	$PHPgps['gps'] = $_P -> gps;
	}
}
$zapytanie = $FP -> db_q('SELECT punktID, gps, opis, punktTyp.typ, punkt.punktTypID FROM punkt NATURAL LEFT JOIN punktTyp ORDER BY punktID ASC ');
while($wynik = $zapytanie -> fetch_object()){
	
	$PHPdata[$wynik -> punktID]['typ'] = $wynik -> typ;
	$PHPdata[$wynik -> punktID]['opis'] = $wynik -> opis;
	$PHPdata[$wynik -> punktID]['gps'] = $wynik -> gps;
	$PHPdata[$wynik -> punktID]['punktID'] = $wynik -> punktID;
	$PHPdata[$wynik -> punktID]['punktTypID'] = $wynik -> punktTypID;
	//jeżeli mufa to pobieramy mufaID
	if($wynik -> punktTypID == 1)
		@$PHPdata[$wynik -> punktID]['mufaID'] = $FP -> db_sq('SELECT mufaID from mufa WHERE punktID = "'.$wynik -> punktID.'"') -> mufaID;
	
	//jeżeli przełącznica to pobieramy przelacznicaID
	else if($wynik -> punktTypID == 2)
		@$PHPdata[$wynik -> punktID]['przelacznicaID'] = $FP -> db_sq('SELECT przelacznicaID from przelacznica WHERE punktID = "'.$wynik -> punktID.'"') -> przelacznicaID;
}

?>
<form action="?modul=punkty&co=dodajPunkt" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Dodawanie nowego punktu</h3></td>
</tr>
<tr>
<td>Opis i adres</td>
<td><input type="text" name="opis" size="30" value="<?php echo $opis ?>"></td>
</tr>
<tr>
<td>GPS</td>
<td><input type="text" name="gps" id="gps" size="30" value="<?php echo $gps ?>"></td>
</tr>
<tr>
<td>Typ</td>
<td><?php echo $FP -> pobierzTypyPunktowDoSelecta('punktTypID', $punktTypID); ?><br><a href="?modul=ustawienia&co=typyPunktow" class="zmien">Zarządzaj typami punktów</a></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Dodaj!"></td>
</tr>

</table>
</form>
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script src="inc/markerclusterer_compiled.js" type="text/javascript"></script>

<script type="text/javascript">
 var PHPdata = <?php echo json_encode($PHPdata); ?>;
 var PHPgps = <?php echo json_encode($PHPgps); ?>;
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
				gps.lon = parseFloat(gps.lon) + odlegloscX;
			}
			var opis = '<b>'+PHPdata[punktID].opis+'</b>';
			if(opis == '')
				opis = '(brak opisu)';
			var zawartoscMarkera = '<div style="white-space: nowrap">'+ opis + '<br>(' + PHPdata[punktID].typ + ' - punkt nr ' + punktID+ ')</div><br>';
			
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
				
				
			zawartoscMarkera += '<a href="?modul=punkty&co=listaObiektow&punktID=' + punktID+ '" class="zmien">Co tu jest?</a><br>';
			if(PHPdata[punktID].punktTypID != 1 && PHPdata[punktID].punktTypID != 2){
				zawartoscMarkera += '<a href="?modul=mufy&co=dodajMufe&punktID=' + punktID+ '" class="zmien">Dodaj mufę</a><br>';
				zawartoscMarkera += '<a href="?modul=przelacznice&co=dodajPrzelacznice&punktID=' + punktID+ '" class="zmien">Dodaj przełącznicę</a><br>';
			}
			
			zawartoscMarkera += '<a href="?modul=kable&co=przetnijKabel&punktID=' + punktID+ '" class="zmien">Przetnij kabel</a><hr>';
			zawartoscMarkera += '<a href="?modul=punkty&co=zmienPunkt&punktID=' + punktID+ '" class="zmien">Zmień punkt</a><br>';
			zawartoscMarkera += '<a href="?modul=punkty&co=usunPunkt&punktID=' + punktID+ '" class="usun">Usuń punkt</a>';
			
			//dodajemy marker na mapie
			var tmpMarker = dodajMarker(gps.lat, gps.lon,zawartoscMarkera,kolorMarkera(PHPdata[punktID].punktTypID), zIndexVal);

			markery.push(tmpMarker);
			markerBounds.extend(new google.maps.LatLng(gps.lat, gps.lon));		
			listaPozycji[gps.lat] = gps.lon;
			
	}
	

	if(PHPgps.gps != ''){
		var gps = rozdzielGPS(PHPgps.gps);
		mapa.setCenter(new google.maps.LatLng(gps.lat, gps.lon));
		mapa.setZoom(17);
	}

	google.maps.event.addListener(mapa,'click',function(zdarzenie)
	{
			placeMarker(zdarzenie.latLng);
			$('#gps').val(skroc(zdarzenie.latLng));
	});
	$('#mapa').width('100%');
	$('#mapa').height(window.innerHeight-$('table').height()-$('#tech').height());

	uruchomPo();
	if(markerBounds)
		mapa.fitBounds(markerBounds);
	markerClusterer = new MarkerClusterer(mapa, markery, markerClustererOptions);
	google.maps.event.trigger(mapa, 'resize');
});
	
</script>
   <div id="mapa" style="margin: auto"></div>


</center>