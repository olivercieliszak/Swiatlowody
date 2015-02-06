<?php 

if($_G -> kabelID > 0){
	$return = $FP -> pobierzRelacjeLogicznaWlokna($kabelID);
	
	echo json_encode($return);
}