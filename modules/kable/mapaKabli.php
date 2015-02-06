<?php

	$punktID = array();
	$PHPdata = array();
	$JSustawienia = array('pokazPunkty' => 1);
	if(isset($_G -> pokazPunkty))
		$JSustawienia['pokazPunkty'] = $_G -> pokazPunkty;
	
	$dlugoscKabli = 0;
$zapytanie = $FP -> db_q('SELECT * FROM kabel ORDER BY kabelID ASC');
while($wynik = $zapytanie -> fetch_object()){
	$dlugoscKabli += $FP -> dlugoscKabla($wynik -> kabelID);
	$kabelPunktID = $FP -> db_sq('SELECT punktIDStart, punktIDKoniec FROM kabel WHERE kabelID = "'.$wynik -> kabelID.'"');

	$iloscPunktow = $FP -> db_sq('SELECT COUNT(*) as ilosc FROM kabelPunkt WHERE kabelID = "'.$wynik -> kabelID.'"') -> ilosc;
	if($iloscPunktow > 0){
	
		$zapytanie2 = $FP -> db_q('SELECT * FROM kabelPunkt WHERE kabelID = "'.$wynik -> kabelID.'" ORDER BY kolejnosc ASC');
		while($wynik2 = $zapytanie2 -> fetch_object()){
			$punkt = $FP -> db_sq('SELECT gps, opis, punktTyp.typ, punkt.punktTypID FROM punkt LEFT JOIN punktTyp ON punkt.punktTypID = punktTyp.punktTypID WHERE punktID = "'. $wynik2 -> punktID .'"');
			
			
			//$punktID[$wynik -> kabelID][$wynik2 -> kolejnosc] = $wynik2 -> punktID;
			$PHPdata[$wynik -> kabelID][$wynik2 -> kolejnosc]['typ'] = $punkt -> typ;
			$PHPdata[$wynik -> kabelID][$wynik2 -> kolejnosc]['opis'] = $punkt -> opis;
			$PHPdata[$wynik -> kabelID][$wynik2 -> kolejnosc]['gps'] = $punkt -> gps;
			$PHPdata[$wynik -> kabelID][$wynik2 -> kolejnosc]['punktID'] = $wynik2 -> punktID;
			$PHPdata[$wynik -> kabelID][$wynik2 -> kolejnosc]['punktTypID'] = $punkt -> punktTypID;
			//jeżeli mufa to pobieramy mufaID
			if($punkt -> punktTypID == 1)
				@$PHPdata[$wynik -> kabelID][$wynik2 -> kolejnosc]['mufaID'] = $FP -> db_sq('SELECT mufaID from mufa WHERE punktID = "'.$wynik2 -> punktID.'"') -> mufaID;
			
			//jeżeli przełącznica to pobieramy przelacznicaID
			else if($punkt -> punktTypID == 2)
				@$PHPdata[$wynik -> kabelID][$wynik2 -> kolejnosc]['przelacznicaID'] = $FP -> db_sq('SELECT przelacznicaID from przelacznica WHERE punktID = "'.$wynik2 -> punktID.'"') -> przelacznicaID;
			
			$PHPdata[$wynik -> kabelID][$wynik2 -> kolejnosc]['kabelPunktID'] = $wynik2 -> kabelPunktID;
			$PHPdata[$wynik -> kabelID]['opis'] = $FP -> pobierzRelacjeKabla($wynik -> kabelID,1);
			$PHPdata[$wynik -> kabelID]['iloscWlokien'] = $FP -> db_sq('SELECT COUNT(*) as ilosc FROM kabelWlokno WHERE kabelID = "'.$wynik -> kabelID.'"') -> ilosc;
			
		}
	
	}
	//jeżeli nie ma punktów posrednich, narysujmy kabel po punkcie poczatkowym i koncowym
	else{
		$punktyKoncowe = array(1 => $wynik -> punktIDStart, 2 => $wynik -> punktIDKoniec);
			foreach($punktyKoncowe as $kolejnosc => $wynik3){
				$punkt = $FP -> db_sq('SELECT gps, opis, punktTyp.typ, punkt.punktTypID FROM punkt LEFT JOIN punktTyp ON punkt.punktTypID = punktTyp.punktTypID WHERE punktID = "'. $wynik3 .'"');
				
				
				//$punktID[$wynik -> kabelID][$kolejnosc] = $wynik3;
				$PHPdata[$wynik -> kabelID][$kolejnosc]['typ'] = $punkt -> typ;
				$PHPdata[$wynik -> kabelID][$kolejnosc]['opis'] = $punkt -> opis;
				$PHPdata[$wynik -> kabelID][$kolejnosc]['gps'] = $punkt -> gps;
				$PHPdata[$wynik -> kabelID][$kolejnosc]['punktID'] = $wynik3;
				$PHPdata[$wynik -> kabelID][$kolejnosc]['punktTypID'] = $punkt -> punktTypID;
				//jeżeli mufa to pobieramy mufaID
				if($punkt -> punktTypID == 1)
					@$PHPdata[$wynik -> kabelID][$kolejnosc]['mufaID'] = $FP -> db_sq('SELECT mufaID from mufa WHERE punktID = "'.$wynik3.'"') -> mufaID;
				
				//jeżeli przełącznica to pobieramy przelacznicaID
				else if($punkt -> punktTypID == 2)
					@$PHPdata[$wynik -> kabelID][$kolejnosc]['przelacznicaID'] = $FP -> db_sq('SELECT przelacznicaID from przelacznica WHERE punktID = "'.$wynik3.'"') -> przelacznicaID;
				
				$PHPdata[$wynik -> kabelID][$kolejnosc]['kabelPunktID'] = 0;
				$PHPdata[$wynik -> kabelID]['opis'] = $FP -> pobierzRelacjeKabla($wynik -> kabelID,1);
				$PHPdata[$wynik -> kabelID]['iloscWlokien'] = $FP -> db_sq('SELECT COUNT(*) as ilosc FROM kabelWlokno WHERE kabelID = "'.$wynik -> kabelID.'"') -> ilosc;
		}
	
	}
}
?>
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script src="inc/markerclusterer_compiled.js" type="text/javascript"></script>

<script>
//dane z PHP
<?php echo 'var PHPdata = '.json_encode($PHPdata).';'; ?>
<?php echo 'var JSustawienia = '.json_encode($JSustawienia).';'; ?>
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
	var punkty = [];
	for(kabelID in PHPdata){
		
		for(x in PHPdata[kabelID]){

			gps = rozdzielGPS(PHPdata[kabelID][ x ].gps);


			var zIndexVal = undefined;
			while(listaPozycji[gps.lat] == gps.lon){
				gps.lat = parseFloat(gps.lat) + odlegloscX;
				gps.lon = parseFloat(gps.lon) + odlegloscX;
			}				
			//rysujemy punkty posrednie
				
			if(x > 1 && !isNaN(x - 1)){
				
				gps1 = rozdzielGPS(PHPdata[kabelID][ x - 1 ].gps);
				var grubosc = 1;
				if(PHPdata[kabelID].iloscWlokien <= 8)
					grubosc = 1;
				else if (PHPdata[kabelID].iloscWlokien <= 12)
					grubosc = 1.5;
				else if (PHPdata[kabelID].iloscWlokien <= 24)
					grubosc = 2.5;
				else if (PHPdata[kabelID].iloscWlokien <= 72)
					grubosc = 3;					
				else
					grubosc = 4;
				kreska(gps1.lat, gps1.lon, gps.lat, gps.lon, PHPdata[kabelID].opis + '<br><br><b><a href="?modul=kable&co=listaWlokien&kabelID='+kabelID+'">Lista włókien (Ilość włókien: '+PHPdata[kabelID].iloscWlokien+') </a></b><br><hr>Punkty sąsiednie:<br><div style="white-space: nowrap">- ' + PHPdata[kabelID][x - 1].typ + ' <b>'+ PHPdata[kabelID][x - 1].opis + '</b> (' + PHPdata[kabelID][x - 1].punktID + ')</div><div style="white-space: nowrap">- ' + PHPdata[kabelID][x].typ + ' <b>'+ PHPdata[kabelID][x].opis + '</b> (' + PHPdata[kabelID][x].punktID + ')</div>','yellow',grubosc);
				markerBounds.extend(new google.maps.LatLng(gps1.lat, gps1.lon));
				markerBounds.extend(new google.maps.LatLng(gps.lat, gps.lon));

			}
			if(JSustawienia.pokazPunkty == 1){
			

				if(punkty[PHPdata[kabelID][x].punktID] != 1 && PHPdata[kabelID][x].punktID != undefined){
					var zawartoscMarkera = '<div style="white-space: nowrap">'+ PHPdata[kabelID][x].opis + '<br>(' + PHPdata[kabelID][x].typ + ' - punkt nr ' + PHPdata[kabelID][x].punktID + ')</div><br>';
					
					//jezeli typ danych to mufa
					if(PHPdata[kabelID][x].punktTypID == 1){
						zawartoscMarkera += '<a href="?modul=mufy&co=listaSpawow&mufaID='+ PHPdata[kabelID][x].mufaID + '" class="zmien">Lista spawów</a><br>';
						zIndexVal = 'max';
					}
					
					//jezeli typ danych to przelacznica
					if(PHPdata[kabelID][x].punktTypID == 2){
						zawartoscMarkera + '<a href="?modul=przelacznice&co=listaPortow&przelacznicaID='+ PHPdata[kabelID][x].przelacznicaID + '" class="zmien">Lista portów</a><br>';
						zIndexVal = 'max';
					}
						
						
					zawartoscMarkera += '<a href="?modul=punkty&co=listaObiektow&punktID=' + PHPdata[kabelID][x].punktID + '" class="zmien">Co tu jest?</a><br>';
					if(PHPdata[kabelID][x].punktTypID != 1 && PHPdata[kabelID][x].punktTypID != 2){
						zawartoscMarkera += '<a href="?modul=mufy&co=dodajMufe&punktID=' + PHPdata[kabelID][x].punktID + '" class="zmien">Dodaj mufę</a><br>';
						zawartoscMarkera += '<a href="?modul=przelacznice&co=dodajPrzelacznice&punktID=' + PHPdata[kabelID][x].punktID + '" class="zmien">Dodaj przełącznicę</a><br>';
					}
					
					zawartoscMarkera += '<a href="?modul=kable&co=przetnijKabel&punktID=' + PHPdata[kabelID][x].punktID + '" class="zmien">Przetnij kabel</a><hr>';
					zawartoscMarkera += '<a href="?modul=punkty&co=zmienPunkt&punktID=' + PHPdata[kabelID][x].punktID + '" class="zmien">Zmień punkt</a><br>';
					zawartoscMarkera += '<a href="?modul=punkty&co=usunPunkt&punktID=' + PHPdata[kabelID][x].punktID + '" class="usun">Usuń punkt</a>';
					
					//dodajemy marker na mapie
					var tmpMarker = dodajMarker(gps.lat, gps.lon,zawartoscMarkera,kolorMarkera(PHPdata[kabelID][x].punktTypID), zIndexVal);
					markery.push(tmpMarker);

					markerBounds.extend(new google.maps.LatLng(gps.lat, gps.lon));

					}
			}
			punkty[PHPdata[kabelID][x].punktID] = 1;
			listaPozycji[gps.lat] = gps.lon;

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
		$('#mapa').height(window.innerHeight - $('#tech').height() - $('#tech2').height());
		google.maps.event.trigger(mapa, 'resize');

	
	});
</script>
<div id="mapa" style="width:100%; height:100%"></div>
<div style="float: right; margin-top: 10px; text-align: right" id="tech2">
<?php
if($JSustawienia['pokazPunkty'] == 1) 
	echo '<a href="?modul=kable&co=mapaKabli&pokazPunkty=0" class="zmien">Ukryj punkty</a>';
else
	echo '<a href="?modul=kable&co=mapaKabli&pokazPunkty=1" class="zmien">Pokaż punkty</a>';

?><br>
Długość wszystkich kabli: <?php echo $dlugoscKabli; ?> m
</div>