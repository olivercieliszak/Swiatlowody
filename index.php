<?php
session_start();
require_once('inc/logout.php');
require_once('inc/functions.php');
include('inc/header.php');
include('inc/users.php');
$FP=new Swiatlowody;

if(isset($_R -> kto) && isset($_R -> haslo)){
	if(array_key_exists($_R -> kto, $userTmp) && $userTmp[$_R -> kto] == md5($_R -> haslo)){
		$_G -> userID = 999;
		$_G -> userName = $_R -> kto;
		$_G -> auth = sha1($FP -> kluczDoAPI . time().$_G -> userID.$_G -> userName);
	}
	else{
		echo $FP -> komunikat(false,false,'Podane dane są nieprawidłowe');
	}

}
if(isset($_G -> auth) && isset($_G -> userID) && isset($_G -> userName))
	$FP -> login($_G -> auth, time(), $_G -> userID, $_G -> userName);
else if((!isset($_SESSION['ok']) || (isset($_SESSION['ok']) && $_SESSION['ok'] != 1)) && isset($userTmp) && is_array($userTmp)){
	echo '<div class="komunikat" style="color: #000; border: 1px solid grey;background-color: #eee;"><form><table><tr><td>Nazwa użytkownika</td><td><input type="text" name="kto"></td></tr><tr><td>Hasło</td><td><input type="password" name="haslo"></td></tr><tr><td colspan="2"><input type="submit" value="Zaloguj się"></td></tr></table></form></div>';
	exit();
}
else
	$FP -> login();


$moduly = array(
	'Kable' => '?modul=kable&co=listaKabli',
	'Punkty' => '?modul=punkty&co=listaPunktow',
	'Mufy' => '?modul=mufy&co=listaMuf',
	'Przełącznice' => '?modul=przelacznice&co=listaPrzelacznic',
	'Relacje' => '?modul=relacje&co=listaRelacji',
	'Ustawienia' => '?modul=ustawienia&co=ustawieniaMenu',
	'Pomoc' => '?modul=pomoc&co=pomocMenu',
	'Wyloguj ['.$FP -> userName.']' => '?wyloguj=1'
);
?><script>
var ukryteMenu = 0;
var staraPrawaMargin = 0;
var staraPrawaWidth = 0;
var staraTabelaWidth = 0;
var staraAutorMargin = 0;
var staraChowaczMargin = 0;
function chowaj(){

	$('#lewa').toggle();
	//jesli odkryte, ukryj
	if(ukryteMenu == 0){
		ukryteMenu = 1;
		setCookie('menu', 1, 30);
		staraPrawaMargin = $('#prawa').css('margin-left');
		staraAutorMargin = $('#autor').css('left');
		staraPrawaWidth = $('#prawa').css('width');
		staraChowaczMargin = $('#chowacz').css('margin-left');
		$('#chowacz').text('>');
		$('#chowacz').css('margin-left', '5px');
		$('#prawa').css('margin-left', '0');
		$('#autor').css('left', '2px');
		$('#prawa').css('width', '100%');
		$('#prawa table').css('width', '100%');
		
		if(mapa)
			google.maps.event.trigger(mapa, 'resize');

	}
	else{
		ukryteMenu = 0;
		setCookie('menu', 0, 30);
		
		$('#chowacz').text('<');
		if(staraChowaczMargin)
			$('#chowacz').css('margin-left', staraChowaczMargin);
		if(staraPrawaMargin)
			$('#prawa').css('margin-left', staraPrawaMargin);
		if(staraAutorMargin)
			$('#autor').css('left', staraAutorMargin);
		if(staraPrawaWidth)
			$('#prawa').css('width', staraPrawaWidth);
		
		$('#prawa table').css('width', '100%');
	}
}
jQuery(document).ready(function($) {
	if(getCookie('menu') == 1)
		chowaj();
	
});

</script>
<a href="#" onclick="chowaj()"><div id="chowacz"><</div></a>
<div id="lewa">
<div id="lewa_content">
<center><h1>Światłowody</h1><span style="font-size: 7.5px; color: 000">SYSTEM PASZPORTYZACJI SIECI OPTYCZNYCH</span> </center><br>
<?php
foreach((array)$moduly as $nazwaModulu => $modul){

		echo '<a href="'.$modul.'" class="link"><div id="'.strtolower(str_replace(array('ą', 'ł'), array('a','l'), $nazwaModulu)).'" class="menu">'.$nazwaModulu.'</div></a>';

}
?>


</div>
</div>
<div id="autor">Oliver Cieliszak - v1.0</div>
<div id="prawa">
<div id="prawa_content">
<?php


if(isset($_G -> modul) && isset($_G -> co)){
	$_G -> modul = str_replace(array('ą', 'ł'), array('a','l'), $_G -> modul);
	include('./modules/'.$_G -> modul.'/'.$_G -> co.'.php');
}
?>


</div>
<div id="tech">
Ilość zapytań: <?php echo $FP -> iloscZapytan; ?><br>
Czas wykonywania: <?php echo round(microtime(true)- $FP -> timeAll,4).' s (SQL: '.round($FP -> timeSQL, 4).' s)'; ?>
</div>
</div>
<?php
	
include('inc/footer.php');

?>