<?php
$wynik = $FP -> db_sq('SELECT * FROM punkt WHERE punktID = "'.(int)$_R -> punktID.'"');

if(isset($_P -> punktID)){
	if(!empty($_P -> punktID)){
	
	if($_P -> punktTypID <= 2)
		$_P -> punktTypID = $wynik -> punktTypID;
	
	$zapytanie = $FP -> db_q('UPDATE punkt SET gps = "'.$_P -> gps.'", opis = "'.$_P -> opis .'" WHERE punktID = "'.$_P -> punktID.'"');	

	echo $FP -> komunikat($zapytanie, 'Punkt <i>'.$_P -> opis.'</i> został prawidłowo zmieniony<br><br><a href="?modul=punkty&co=listaPunktow">Powrót do listy punktów</a>', 'Wystąpił błąd podczas zmiany punktu <i>'.$_P -> opis.'</i><br><br><a href="?modul=punkty&co=listaPunktow">Powrót do listy punktów</a>');
	if($zapytanie)
		$FP -> log('Zmieniono punkt nr '.$_P -> punktID);
	}
}
else{

$PHPdata = array('gps' => $wynik -> gps);
?>
<form action="?modul=punkty&co=zmienPunkt" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Zmiana punktu</h3></td>
</tr>
<tr>
<td>ID</td>
<td><?php echo $wynik -> punktID ?><input type="hidden" name="punktID" value="<?php echo $wynik -> punktID ?>"></td>
</tr>
<tr>
<td>Opis  i adres</td>
<td><input type="text" name="opis" size="30" value="<?php echo $wynik -> opis ?>"></td>
</tr>
<tr>
<td>GPS</td>
<td><input type="text" name="gps" id="gps" size="30" value="<?php echo $wynik -> gps ?>"></td>
</tr>
<?php
if($wynik -> punktTypID > 2){
?>
<tr>
<td>Typ</td>
<td><?php echo $FP -> pobierzTypyPunktowDoSelecta('punktTypID', $wynik -> punktTypID); ?><br><a href="?modul=ustawienia&co=typyPunktow" class="zmien">Zarządzaj typami punktów</a></td>
</tr>
<?php } ?>
<tr>
<td colspan="2"><input type="submit" value="Zmień!"></td>
</tr>

</table>
</form>
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script src="inc/markerclusterer_compiled.js" type="text/javascript"></script>

<script>
<?php
echo 'var PHPdata = '.json_encode($PHPdata).';';
?>

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
		
		var gps = rozdzielGPS(PHPdata['gps']);
		if(gps.lat > 0 && gps.lon > 0){
			mapa.setCenter(new google.maps.LatLng(gps.lat, gps.lon));
			
			var tmpMarker = placeMarker(new google.maps.LatLng(gps.lat, gps.lon));
			markery.push(tmpMarker);
			markerBounds.extend(new google.maps.LatLng(gps.lat, gps.lon));

		}
		google.maps.event.addListener(mapa,'click',function(zdarzenie)
		{
			placeMarker(zdarzenie.latLng);
			$('#gps').val(skroc(zdarzenie.latLng));
		});
		
		//przyblizamy do umieszczonych punktow
		if(markerBounds)
			mapa.fitBounds(markerBounds);
		//agregujemy markery
		//ustawiamy wysokosc mapy
		$('#mapa').height(window.innerHeight-$('#tech').height()-$('table').height()-4);
		$('#mapa').width($('table').width());
		google.maps.event.trigger(mapa, 'resize');
	});

	
</script>
   <div id="mapa" style="width:100%;margin: auto"></div>



</center>
<?php
}
?>