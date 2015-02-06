<?php

	$PHPdata = array();

	$zapytanie = $FP -> db_q('SELECT punktID, gps, opis, punktTyp.typ, punkt.punktTypID FROM punkt NATURAL LEFT JOIN punktTyp ORDER BY punktID ASC');
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
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script src="inc/markerclusterer_compiled.js" type="text/javascript"></script>
<script>
//dane z PHP
<?php echo 'var PHPdata = '.json_encode($PHPdata).';'; ?>
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
	var punkty = [];
	
	
	for(x in PHPdata){

		gps = rozdzielGPS(PHPdata[ x ].gps);


		var zIndexVal = undefined;
			
		//rysujemy punkty posrednie
		while(listaPozycji[gps.lat] == gps.lon){
			gps.lat = parseFloat(gps.lat) + odlegloscX;
			gps.lon = parseFloat(gps.lon) + odlegloscX;
		}
		listaPozycji[gps.lat] = gps.lon;

		
	
			if(punkty[PHPdata[x].punktID] != 1 && PHPdata[x].punktID != undefined){
				var zawartoscMarkera = '<div style="white-space: nowrap">'+ PHPdata[x].opis + '<br>(' + PHPdata[x].typ + ' - punkt nr ' + PHPdata[x].punktID + ')</div><br>';
				
				//jezeli typ danych to mufa
				if(PHPdata[x].punktTypID == 1){
					zawartoscMarkera += '<a href="?modul=mufy&co=listaSpawow&mufaID='+ PHPdata[x].mufaID + '" class="zmien">Lista spawów</a><br>';
					zIndexVal = 'max';
				}
				
				//jezeli typ danych to przelacznica
				if(PHPdata[x].punktTypID == 2){
					zawartoscMarkera + '<a href="?modul=przelacznice&co=listaPortow&przelacznicaID='+ PHPdata[x].przelacznicaID + '" class="zmien">Lista portów</a><br>';
					zIndexVal = 'max';
				}
			
					
				zawartoscMarkera += '<a href="?modul=punkty&co=listaObiektow&punktID=' + PHPdata[x].punktID + '" class="zmien">Co tu jest?</a><br>';
				if(PHPdata[x].punktTypID != 1 && PHPdata[x].punktTypID != 2){
					zawartoscMarkera += '<a href="?modul=mufy&co=dodajMufe&punktID=' + PHPdata[x].punktID + '" class="zmien">Dodaj mufę</a><br>';
					zawartoscMarkera += '<a href="?modul=przelacznice&co=dodajPrzelacznice&punktID=' + PHPdata[x].punktID + '" class="zmien">Dodaj przełącznicę</a><br>';
				}
				
				zawartoscMarkera += '<a href="?modul=kable&co=przetnijKabel&punktID=' + PHPdata[x].punktID + '" class="zmien">Przetnij kabel</a><hr>';
				zawartoscMarkera += '<a href="?modul=punkty&co=zmienPunkt&punktID=' + PHPdata[x].punktID + '" class="zmien">Zmień punkt</a><br>';
				zawartoscMarkera += '<a href="?modul=punkty&co=usunPunkt&punktID=' + PHPdata[x].punktID + '" class="usun">Usuń punkt</a>';
				
				//dodajemy marker na mapie
				var tmpMarker = dodajMarker(gps.lat, gps.lon,zawartoscMarkera,kolorMarkera(PHPdata[x].punktTypID), zIndexVal);
				markery.push(tmpMarker);

				markerBounds.extend(new google.maps.LatLng(gps.lat, gps.lon));
				

			}
		}

		//uwaga! to musi byc uruchomione przed boundsami
		//uruchamiamy listenera do zooma i centera by trzymac to w Cookiesach
		uruchomPo();
		//przyblizamy do umieszczonych punktow
		if(markerBounds)
			mapa.fitBounds(markerBounds);

		//agregujemy markery
		markerClusterer = new MarkerClusterer(mapa, markery, markerClustererOptions);
		//ustawiamy wysokosc mapy
		$('#mapa').width('100%');
		$('#mapa').height(window.innerHeight - $('#tech').height());
		google.maps.event.trigger(mapa, 'resize');
	});

</script>

   <div id="mapa" style="height:550px; margin: 0"></div>

