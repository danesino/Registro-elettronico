<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// Please READ carefully the Docs/License.txt file for further details
// Please READ the Docs/credits.txt file for complete credits list
// ----------------------------------------------------------------------
// Nome file: VisualizzaAssociazioniStudenti.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: visualizza l'associazione tra un utente e uno o più studenti
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore
// ----------------------------------------------------------------------

$title= "Associazione studente-utente";
include("Testa.inc");
include("FunzioniDB.inc");
autorizza_ruoli(ID_ADMIN);

print "<h2>Associazione studente - utente</h2>";
$sql = "SELECT * FROM Studenti, Esterni WHERE Esterni.id_utente=".$_POST['id_utente']." AND Studenti.id_studente=Esterni.id_studente";
$res =& $link->query($sql);
$num_righe = $res->numRows();
if ($num_righe==0):
	 print "<dl><dd>L'utente non è associato a nessuno studente</dd></dl>";
else:?>
<h3>Numero studenti associati: <?=$num_righe?> </h3><br /><br />
<table class="elenco">
 <tr>
  <th>Cognome</th>
  <th>Nome</th>
  <th>Codice fiscale</th>
  <th>Sesso</th>
  <th>Data di nascita</th>
 </tr>
<?
	 $pari=1;
while ($riga =& $res->fetchRow())
{
	 $class = ($pari) ? "pari" : "dispari";
	 $pari=1-$pari;
	 print "<tr class='$class'>";
	 print "<td nowrap='nowrap'>{$riga['cognome']}</td>";

	 print "<td nowrap='nowrap'>{$riga['nome']}</td>";

	 print "<td nowrap='nowrap'>{$riga['CF']}</td>";
	 print "<td nowrap='nowrap'>";
	 echo ($riga['sesso'] == "M") ? "maschio</td>" : "femmina</td>";
	 print "<td nowrap='nowrap'>";
	 $data = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['n_data']);
	 print "$data</td>";
	 print "</tr>";
}
print "</table>";
endif;
$up="VisualizzaUtenti";
include "Coda.inc";
?>
