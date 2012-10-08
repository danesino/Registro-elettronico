<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) - (Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt)
// Please READ carefully the Docs/License.txt file for further details
// Please READ the Docs/credits.txt file for complete credits list
// ----------------------------------------------------------------------
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: Menu principale registro
// ----------------------------------------------------------------------

$title = "Menu principale";
include("Testa.inc");
autorizza_ruoli(ID_ADMIN,ID_OPERATORE);
?>
<h1 class="title">Registro elettronico</h1>
<table class="index">
<tr><td colspan="2">
<?php
// Visualizza il ruolo con cui si è collegati e il nome della scuola ospedaliera
$r =& $link->query("SELECT * from Ruoli WHERE (id_ruolo= ?) LIMIT 1", $RUOLO);
if (PEAR::isError($r))	
	 echo "<dl><dd>$r</dd></dl>";
else
	 $r->fetchInto($obj);
print "Ciao <strong>$NOME $COGNOME!</strong> Sei collegato come <strong>{$obj['descrizione']}</strong> alla Scuola ospedaliera <strong>$REG</strong>";
?>        
</td></tr>
<tr>
	 <td  class="studentisx">Scuole</td>
	 <td  class="studentidx">
		  <a href="InserimentoScuola.php">Inserimento</a> - <small>Inserimento nuova scuola</small><br />
		  <a href="VisualizzaScuole.php">Modifica</a> - <small>Modifica dati scuole</small><br />
		  <a href="VisualizzaScuole.php">Elenco</a> - <small>Elenco completo</small><br />
	 </td>
</tr>
<tr>
	<td  class="studentisx">Studenti</td>
	<td  class="studentidx">
	 <a href="InserimentoStudente.php">Inserimento</a> - <small>Inserimento nuovo studente</small><br />
	 <a href="VisualizzaStudenti.php">Elenco</a> - <small>Elenco completo studenti</small><br />
	 <a href="RicercaStudente.php">Ricerca </a> - <small>Ricerca per cognome</small><br />
   </td>
</tr>
<tr>
	<td class="studentisx">Reparti</td>
	<td class="studentidx"><a href="GestioneReparti.php">Gestione</a> - <small>Gestione completa</small><br /></td>
</tr>
<tr>
	<td class="utentisx">Utenti</td>
	<td class="utentidx">
	 <a href="InserimentoUtente.php">Inserimento</a> - <small>Inserimento nuovo utente</small><br />
	 <a href="VisualizzaUtenti.php">Elenco</a> - <small>Elenco completo</small><br />
   </td>
</tr>
<tr>
	<td class="utentisx">Materie</td>
	<td class="utentidx"><a href="GestioneMaterie.php">Gestione</a> - <small>Gestione completa</small><br /></td>
</tr>
<tr>
	 <td class="cdcsx">Gruppi di lavoro</td>
	 <td class="cdcdx">
		  <a href="InserimentoClasse.php">Inserimento</a> - <small>Inserimento nuovo gruppo di lavoro</small><br/>
		  <a href="VisualizzaClassi.php">Elenco</a> - <small>Elenco completo  gruppi di lavoro</small><br/>
		  <a href="GestioneCdC.php">Consiglio di Classe</a> - <small>Composizione del Consiglio di Classe</small><br/>
		  <a href="GestioneClasse.php">Studenti</a> - <small>Assegnazione studenti e gestione registro</small><br/>
		  <a href="InviaMessaggio.php">Invia messaggi</a> - <small>Inviare messaggi ai membri del proprio gruppo di lavoro</small><br/>
		  <a href="VisualizzaMessaggi.php?a=1">Leggi messaggi</a> - <small>Leggere messaggi ricevuti</small><br/>
		  <a href="DownloadPresenzeMensili.php">Presenze</a> - <small>Download presenze mensili</small><br/>
		  <a href="Statistiche.php">Statistiche</a> - <small>Statistiche riassuntive frequenze</small><br/>
  
	 </td>
</tr>
</table>

<div class="rel">
<?
$a=file_get_contents('VERSION');
echo "Rel. ".$a;
echo '<br /><a href="http://www.fsf.org/licensing/licenses/gpl.txt">GNU General Public License v.2</a></div>';
$up="index";
include "Coda.inc";?>
