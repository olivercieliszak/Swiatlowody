	var mapa;
	var dymek;

	var marker;
	var markerBounds;
	var markerClusterer;
	var markerClustererOptions = {gridSize: 50, maxZoom: 15};	
	var odlegloscX = 0.00001;
	var odlegloscY = 0.00001;
	var poczatkowyLat = 52;
	var poczatkowyLon = 19;
	var poczatkowyZoom = 6;
	
	function kolorMarkera(punktTypID){
		var kolory = $.getJSONValues('ajax.php?modul=ajax&co=pobierzKoloryTypowPunktow');
		if(kolory[punktTypID] == "")
			return 'czerwona';
		else
			return kolory[punktTypID];
	}
		function kreska(lat1, lon1, lat2, lon2, zawartosc, kolor, grubosc, widzialnosc){
			zawartosc = pick(zawartosc, ' ');
			kolor = pick(kolor, 'yellow');
			grubosc = pick(grubosc, '2');
			widzialnosc = pick(widzialnosc, '1');

			google.maps.event.addListener(			
			new google.maps.Polyline({
					map:            mapa,
					path:           [new google.maps.LatLng(lat1, lon1), new google.maps.LatLng(lat2, lon2)],
					strokeColor:    kolor,
					strokeWeight:   grubosc,
					strokeOpacity:	widzialnosc
					})
					, 'click', function(zdarzenie){
						dymek.close(mapa);
						dymek.setContent('<center><div>'+zawartosc+'</div><br></center>');
						dymek.setPosition(zdarzenie.latLng);
						dymek.open(mapa);
						
					});
			//alert(Lat1+','+Lng1+','+Lat2+','+Lng2);
		}	
		function skroc(co){
			var co2 = co.toString();
			co2 = co2.replace("(", "");
			co2 = co2.replace(')', '');
			co2 = co2.replace(' ', '');
			var co_arr = co2.split(',');
			var szerokosc = parseFloat(co_arr[0]);
			var dlugosc = parseFloat(co_arr[1]);
			var wynik = Round(szerokosc, 6)+","+Round(dlugosc, 6);
			return wynik;
		}					
		function Round(n, k) {
			var factor = Math.pow(10, k);
			return Math.round(n*factor)/factor;
		}
		function dodajMarker(lat,lng,tekst,ikona, zIndexVal)
		{
			
			var rozmiar = new google.maps.Size(32,32);
			var rozmiar_mini = new google.maps.Size(16,16);
			var rozmiar_cien = new google.maps.Size(59,32);
			var punkt_startowy = new google.maps.Point(0,0);
			var punkt_zaczepienia = new google.maps.Point(16,16);
			var kolory = new Array();
			var zIndexVal = pick(zIndexVal, -1);
			if(zIndexVal == "max")
				zIndexVal = google.maps.Marker.MAX_ZINDEX + 1;
			//alert(google.maps.Marker.MAX_ZINDEX);
			kolory['niebieska'] = "http://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png";
			kolory['czerwona'] = "http://maps.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png";
			kolory['niebieska_awaria'] = "http://www.google.com/intl/en_us/mapfiles/ms/micons/blue.png";
			kolory['czerwona_awaria'] = "http://maps.google.com/intl/en_us/mapfiles/ms/micons/red.png";
			kolory['zolta'] = "http://maps.google.com/intl/en_us/mapfiles/ms/micons/yellow-dot.png";
			kolory['zolta_awaria'] = "http://maps.google.com/intl/en_us/mapfiles/ms/micons/yellow.png";
			kolory['fioletowa'] = "http://maps.google.com/intl/en_us/mapfiles/ms/micons/purple-dot.png";
			kolory['fioletowa_awaria'] = "http://maps.google.com/intl/en_us/mapfiles/ms/micons/purple.png";
			kolory['rozowa'] = "http://maps.google.com/intl/en_us/mapfiles/ms/micons/pink-dot.png";
			kolory['zielona'] = "http://maps.google.com/intl/en_us/mapfiles/ms/micons/green-dot.png";
			kolory['czarna'] = "http://maps.google.com/mapfiles/marker_black.png";
			kolory['biala'] = "http://maps.google.com/mapfiles/marker_white.png";
			ikona = pick(ikona, 'czarna');
			tekst = pick(tekst, ' ');
			
			var marker = new google.maps.Marker({ position: new google.maps.LatLng(lat,lng),
			map: mapa,
			icon: new google.maps.MarkerImage(kolory[ikona], rozmiar, punkt_startowy, punkt_zaczepienia),
			draggable:false,
			zIndex: zIndexVal
			} );
		//	alert(zIndexVal);
			//wrzucenie markera na mape
			google.maps.event.addListener(marker,"click",function(zdarzenie){
					dymek.close(mapa);
					dymek.setContent('<center><div>'+tekst+'</div><br></center>');
					dymek.setPosition(zdarzenie.latLng);
					dymek.open(mapa,marker);
				}
			);
			
			return marker;
		}
	function placeMarker(location) {
		if(marker)
			marker.setMap(null);
		  marker = new google.maps.Marker({
			position: location,
			draggable:false,
			map: mapa,
		});
		return marker;
	}
	function uruchomPo(){
		var test1 = false;
		var test2 = false;
		if(getCookie('zoom') != undefined && getCookie('zoom') > 0){
			var zoom = parseInt(getCookie('zoom'));
			mapa.setZoom(zoom);
			//alert(zoom);
			test1 = true;
		}
		if(getCookie('pozycja') != undefined){
			var pozycja = rozdzielGPS(getCookie('pozycja'));
			mapa.setCenter(new google.maps.LatLng(pozycja.lat, pozycja.lon));
			//alert(pozycja.lat+pozycja.lon);
			test2 = true;
		}
		
		//zeby wylaczyc markerbounds gdy mamy zoom i pozycje z cookiesow to musimy oproznic nasz obiekt
		if(test1 && test2){
			markerBounds = false;
			//alert(111);
			//alert(markerBounds);
		}
		//alert(markerBounds);
		//po zakonczeniu wszystkich operacji nalezy pobrac zoom i center zeby pozniej tez wyswietlic
		 google.maps.event.addListener(mapa, 'idle', function(zdarzenie) {
			var zoomLevel = mapa.getZoom();
			var pozycja = skroc(mapa.getCenter());
			
			setCookie('zoom', zoomLevel, 1);
			setCookie('pozycja', pozycja, 1);
		 });
		 google.maps.event.trigger(mapa, 'resize');

	}