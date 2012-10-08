<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  VisualizzaScuola.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: visualizza informazioni su una scuola
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore, operatore
// ----------------------------------------------------------------------

$title = "Visualizza Scuola";
include("Testa.inc");
autorizza_ruoli(ID_ADMIN,ID_OPERATORE,ID_OSPEDALIERO);

$sql = $link->query("SELECT * FROM Scuole WHERE id_scuola='{$_GET['id']}' --");
if (PEAR::isError($sql)) {die($res->getMessage());}
$riga = $sql->fetchRow(DB_FETCHMODE_ASSOC);

?>
	<h3>Informazioni complete scuola</h3>
	<table class="elenco">
		
		<tr><th>Nome</th><td bgcolor="#DDDDDD"><?=$riga['nome']?></td></tr>
		<tr><th>Codice</th><td bgcolor="#DDDDDD"><?=$riga['codice']?></td></tr>
		<tr><th>Denominazione</th><td bgcolor="#DDDDDD"><?=$riga['denominazione']?></td></tr>
		<tr><th>Indirizzo</th><td bgcolor="#DDDDDD"><?=$riga['indirizzo']?></td></tr>
		<tr><th>CAP</th><td bgcolor="#DDDDDD"><?=$riga['cap']?></td></tr>
		<tr><th>Citt&agrave;</th><td bgcolor="#DDDDDD"><?=$riga['citta']?></td></tr>
		<tr><th>Provincia</th><td bgcolor="#DDDDDD"><?=$riga['provincia']?></td></tr>
		<tr><th>Telefono</th><td bgcolor="#DDDDDD"><?=$riga['telefono']?></td></tr>
		<tr><th>Fax</th><td bgcolor="#DDDDDD"><?=$riga['fax']?></td></tr>
		<tr><th>E-mail</th><td bgcolor="#DDDDDD"><?=$riga['email']?></td></tr>
		<tr><th>Sito web</th><td bgcolor="#DDDDDD"><?=$riga['sitoweb']?></td></tr>
	 	
	</table>
<?
$up="VisualizzaScuole";
include "Coda.inc";?>

