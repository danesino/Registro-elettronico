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
autorizza_ruoli(ID_OSPEDALIERO, ID_DOMICILIARE);
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
	<td  class="utentisx">Registro</td>
	<td  class="utentidx">
	 <a href="Registro.php">Compilazione del registro</a>
  </td>
</tr>
<tr>
	<td  class="cdcsx">Messaggi</td>
	<td  class="cdcdx">
	 <a href="InviaMessaggio.php">Invio Messaggi</a>
	 <br> <br>
	 <a href="VisualizzaMessaggi.php?a=1">Archivio Messaggi Ricevuti</a>
  </td>
</tr>
<tr>
	<td  class="utentisx">Logout</td>
	<td  class="utentidx">
	 <a href="Logout.php">Termine sessione</a>
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
