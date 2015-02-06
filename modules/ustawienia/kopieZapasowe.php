<table>
<tr>
<td>
<h3>Kopie zapasowe</h3>
</td>
</tr>
<tr>
<script>
function wykonajKopie(){
	var opis = prompt('Podaj opis kopii zapasowej');
	if(opis == null)
			return false;
	location.href = '?modul=ustawienia&co=wykonajKopie&opis='+opis;
	return true;
}
</script>
<td><a href="#" onClick="wykonajKopie()" class="zmien">Wykonaj kopię</a>
</td>
</tr>
<tr>
<td>
<a href="?modul=ustawienia&co=zaladujKopie" class="zmien">Przywróć kopię</a>
</td>
</tr>
<tr>
<td>

<?php 
?>
</td>
</tr>

</table>