<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file: VisualizzaStudentiReparto.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: Visualizza l'elenco degli studenti per ogni reparto
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore
// ----------------------------------------------------------------------

$title = "Studenti del Reparto";

include "Testa.inc";
autorizza_ruoli(ID_ADMIN);

$sql =& $link->query("SELECT Studenti.*,Reparto.tipo_degenza FROM Studenti,Reparto,Degenze WHERE Reparto.id_degenza=Degenze.id_degenza AND (Reparto.id_reparto = ? ) AND Reparto.attivo='S' and Studenti.id_studente=Degenze.id_studente", $_POST['id_reparto']);
$num_righe = $sql->numRows();

print "<h2>Elenco studenti reparto {$_POST['nome_reparto']}</h2>";
if ($num_righe==0)
	 print "<dl><dd>La ricerca non ha individuato nessun elemento</dd></dl>";
else
{
	 print "<h3>La ricerca ha rilevato ";
	 echo ($num_righe > 1) ? "$num_righe studenti</h3><p>" : "uno studente</h3><p>";
	 print "<table border=0><tr>";
	 print '<th>Nome </th>';
	 print '<th>Cognome</th>';
	 print '<th>Tipo degenza</th>';
	 print "</tr>";
  
	 $pari=1;
	 while ($riga =& $sql->fetchRow()) 
	 {
		  $class = ($pari) ? "pari" : "dispari";
		  $pari=1-$pari;
		  print "<tr class='$class'>\n";
		  print "<td nowrap='nowrap'>{$riga['nome']}</td>\n";
		  print "<td nowrap='nowrap'>{$riga['cognome']}</td>\n";
		  print "<td nowrap='nowrap'>";
		  echo ($riga['tipo_degenza'] == 'DH') ? "Day Hospital</td>" : "Degenza ordinaria</td>";
		  print "</tr>";
	 }
	 print "</table>";
}
$up="GestioneReparti";
include "Coda.inc";
?>
