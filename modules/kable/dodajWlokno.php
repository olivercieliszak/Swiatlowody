<?php

if(isset($_P -> kabelID)){
	$zapytanie2 = false;
	$iloscWlokien = 0;
	if(count($_P -> kolorTubaID) > 0){
		foreach($_P -> kolorTubaID as $singleKolorTubaID){
			if(count($_P -> kolorWloknoID) > 0){
			
				foreach($_P -> kolorWloknoID as $singleKolorWloknoID){
					//sprawdzenie czy dane wlokno w danej tubie juz nie istnieje, zabezpieczenie przed duplikatami
					$zapytanie1 = $FP -> db_sq('SELECT COUNT(*) as ilosc FROM kabelWlokno WHERE kabelID = "'.$_P -> kabelID.'" AND kolorTubaID = "'.$singleKolorTubaID.'" AND kolorWloknoID = "'.$singleKolorWloknoID.'"');
					if($zapytanie1 -> ilosc == 0){
						$zapytanie2 = $FP -> db_q('INSERT INTO kabelWlokno SET kabelID = "'.$_P -> kabelID.'", kolorTubaID = "'.$singleKolorTubaID.'", kolorWloknoID = "'.$singleKolorWloknoID.'"');
						$iloscWlokien++;
					}
					else
						echo $FP -> komunikat(false, false, 'Jedno z dodawanych włókien już istnieje w tym kablu');
				}
			}
		}
	}
	
	echo $FP -> komunikat($zapytanie2, $iloscWlokien.' nowych włókien w kablu w relacji <i>'.$FP -> pobierzRelacjeKabla($_P -> kabelID).'</i> zostało dodanych prawidłowo<br><br><a href="?modul=kable&co=listaWlokien&kabelID='.$_P -> kabelID.'">Powrót do listy włókien w kablu nr '.$_P -> kabelID.'</a>',
	'Wystąpił błąd podczas dodawania włókien do kabla w relacji <i>'.$FP -> pobierzRelacjeKabla($_P -> kabelID).'</i><br><br><a href="?modul=kable&co=listaWlokien&kabelID='.$_P -> kabelID.'">Powrót do listy włókien w kablu nr '.$_P -> kabelID.'</a>');
	if($zapytanie2)
		$FP -> log('Dodano '.$iloscWlokien.' włókien do kabla nr '.$_P -> kabelID);
}
?>
<script>
$( document ).ready(function() {
    $(".multiple").css("height", parseInt($(".multiple option").length) * 17);

});
</script>
<form action="?modul=kable&co=dodajWlokno" method="POST">
<table align="center">
<tr>
<td colspan="2"><h3>Dodawanie włókien do kabla</h3></td>
</tr>
<tr>	
<td>Kabel</td>
<td><?php echo $FP -> pobierzKableDoSelecta('kabelID', $_R -> kabelID); ?></td>
</tr>
<tr>
<td>Kolor tuby</td>
<td><?php echo $FP -> pobierzDoKolorowegoSelecta('kolor', 'kolorTuba', 'kolorTubaID', 'kolorHTML', 'kolorTubaID[]', false, true); ?></td>
</tr>
<tr>
<td>Kolory włókien</td>
<td><?php echo $FP -> pobierzDoKolorowegoSelecta('kolor', 'kolorWlokno', 'kolorWloknoID', 'kolorHTML', 'kolorWloknoID[]', false, true); ?></td>
</tr>
<tr>
<td colspan="2"><input type="submit" value="Dodaj!"></td>
</tr>

</table>
</form>