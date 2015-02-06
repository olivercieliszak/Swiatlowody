<?php

	$PHPdata = array();

	$zapytanie = $FP -> db_q('SELECT punktID, gps, opis, punktTyp.typ, punkt.punktTypID FROM punkt NATURAL LEFT JOIN punktTyp WHERE punktTypID =1 ORDER BY punktID ASC');
	while($wynik = $zapytanie -> fetch_object()){
		
		$PHPdata[$wynik -> punktID]['typ'] = $wynik -> typ;
		$PHPdata[$wynik -> punktID]['opis'] = $wynik -> opis;
		$PHPdata[$wynik -> punktID]['gps'] = $wynik -> gps;
		$PHPdata[$wynik -> punktID]['punktID'] = $wynik -> punktID;
		$PHPdata[$wynik -> punktID]['punktTypID'] = $wynik -> punktTypID;
		//jeżeli mufa to pobieramy mufaID
		if($wynik -> punktTypID == 1)
			@$PHPdata[$wynik -> punktID]['mufaID'] = $FP -> db_sq('SELECT mufaID from mufa WHERE punktID = "'.$wynik -> punktID.'"') -> mufaID;
		

	}

?>
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script src="inc/markerclusterer_compiled.js" type="text/javascript"></script>
<script>
//dane z PHP
<?php echo 'var PHPdata = '.json_encode($PHPdata).';'; ?>
</script>
<script type="text/javascript">

/* 

	Początek mapy

*/
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

/*

	Parsowanie danych z PHPdata
	
*/
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
	
	/*
	
		Zakończenie mapy
	
	*/
	
		//uwaga! to musi byc uruchomione przed boundsami
		//uruchamiamy listenera do zooma i centera by trzymac to w Cookiesach
		uruchomPo();
		//przyblizamy do umieszczonych punktow
		if(markerBounds)
			mapa.fitBounds(markerBounds);
		//agregujemy markery
		markerClusterer = new MarkerClusterer(mapa, markery, markerClustererOptions);
		//ustawiamy wysokosc mapy
		$('#mapa').height(window.innerHeight-$('#tech').height()-4);
		google.maps.event.trigger(mapa, 'resize');
		
	});
</script>
<div id="mapa" style="width:100%; height:100%"></div>
