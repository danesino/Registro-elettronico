<?php
	header("Content-type: Application/octet-stream");
	header("Content-Disposition: attachment; filename={$_POST['file']}");
	header("Content-Description: Download PHP");
	readfile($_POST['dir'].$_POST['file']);
?>
