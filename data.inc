<?php

// Funzioni relative alla visualizzazione della data

function data($giorno, $mese, $anno)
{
	
	print "<select name=\"giorno\">";
 	for ($x=1; $x<=31; $x++)
	{
		print "<option value=\"$x\"";
		print ($x==$giorno)?" SELECTED":"";
		print ">".$x;
	}
	print "</select>";
	print "<select name=\"mese\">";
 	$mesi = Array("gennaio","febbraio","marzo","aprile","maggio","giugno","luglio","agosto",
	              "settembre","ottobre","novembre","dicembre");
	for ($x=1; $x<=count($mesi); $x++)
	{
		print "<option value=\"$x\"";
		print ($x==$mese)?" SELECTED":"";
		print ">".$mesi[$x-1];
	}
	print "</select>";
	
	print "<select name=\"anno\">";
	$data_cur= getdate();
	$anno_cur=$data_cur['year'];
 	for ($x=2010; $x<=$anno_cur; $x++)
	{
		print "<option value=\"$x\"";
		print ($x==$anno)?" SELECTED":"";
		print ">".$x;
	}
	print "</select>";
}
function data_fine($giorno, $mese, $anno)
{
	print "<select name=\"giorno_fine\">";
 	for ($x=1; $x<=31; $x++)
	{
		print "<option value=\"$x\"";
		print ($x==$giorno)?" SELECTED":"";
		print ">".$x;
	}
	print "</select>";
	print "<select name=\"mese_fine\">";
 	$mesi = Array("gennaio","febbraio","marzo","aprile","maggio","giugno","luglio","agosto",
	              "settembre","ottobre","novembre","dicembre");
	for ($x=1; $x<=count($mesi); $x++)
	{
		print "<option value=\"$x\"";
		print ($x==$mese)?" SELECTED":"";
		print ">".$mesi[$x-1];
	}
	print "</select>";
	$data_cur= getdate();
	$anno_cur=$data_cur['year'];
	print "<select name=\"anno_fine\">";
 	for ($x=2010; $x<=$anno_cur; $x++)
	{
		print "<option value=\"$x\"";
		print ($x==$anno)?" SELECTED":"";
		print ">".$x;
	}
	print "</select>";
}

function data_inizio_anno_scolastico()
{
	print "<select name=\"giorno_inizio\">";
 	for ($x=1; $x<=31; $x++)
	{
		print "<option value=\"$x\"";
		print ($x==1)?" SELECTED":"";
		print ">".$x;
	}
	print "</select>";
	print "<select name=\"mese_inizio\">";
 	$mesi = Array("gennaio","febbraio","marzo","aprile","maggio","giugno","luglio","agosto",
	              "settembre","ottobre","novembre","dicembre");
	for ($x=1; $x<=count($mesi); $x++)
	{
		print "<option value=\"$x\"";
		print ($x==9)?" SELECTED":"";
		print ">".$mesi[$x-1];
	}
	print "</select>";
	$data_cur= getdate();
	$anno_cur=$data_cur['year'];
	$mese_cur=$data_cur['month'];
	if ($mese_cur<7) $anno=$anno_cur-1;
			
	print "<select name=\"anno_inizio\">";
 	for ($x=2010; $x<=$anno_cur; $x++)
	{
		print "<option value=\"$x\"";
		print ($x==$anno)?" SELECTED":"";
		print ">".$x;
	}
	print "</select>";
}

function data_odierna()
{
	$data= getdate();
	$giorno=$data['mday'];
	$mese=$data['mon'];
	$anno=$data['year'];
	data($giorno,$mese,$anno);
}

 
?>
