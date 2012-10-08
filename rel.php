<?

	include "Testa.inc";
	autorizza_ruoli(ID_ADMIN,ID_OPERATORE);
	$a=file_get_contents('VERSION');
	$b=getdate();
	echo $b[0];
	echo mktime($b[0]);
	include "Coda.php";
?>
