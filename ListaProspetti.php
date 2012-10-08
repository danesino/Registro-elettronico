<?php
// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL)
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  ListaProspetti.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: pagina principale dell'osservatore con tutti i prospetti
// ----------------------------------------------------------------------
// Autorizzazione: tutti
// ----------------------------------------------------------------------

$title = "Prospetto scolastico studenti";

include "Testa.inc";
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN,ID_OPERATORE,ID_OSPEDALIERO,ID_OSSERVATORE,ID_AFFIDATARIO,ID_DOMICILIARE);

echo "<h2>Prospetto scolastico studenti</h2>";

$sql =& $link->query("SELECT * FROM Studenti, Esterni WHERE ( Esterni.id_utente= ? AND Studenti.id_studente=Esterni.id_studente)",$codice_utente);
$num_righe = $sql->numRows();
if ($num_righe!=0)
{		
	print "<table class='elenco'>";
	print "<tr>";
	print '<th>Cognome</th>';
	print '<th>Nome</th>';
	print '<th>Codice fiscale</th>';
	print '<th>Sesso</th>';
	print '<th>Data di nascita</th>';
	print '<th>Cittadinanza</th>';
	print '<th colspan="4">Residenza</th>';
	print '<th colspan="2">Azione</th>';
	print "</tr>";

	$pari=1;
	while ($riga =& $sql->fetchRow())
	{
		$class = ($pari) ? "pari" : "dispari";
  	$pari=1-$pari;
		
		print "<tr class='$class'>";
		print "<td nowrap='nowrap'>";
		print $riga['cognome']."</td>\n";
		print "<td nowrap='nowrap'>";
		print $riga['nome']."</td>\n";
		print "<td nowrap='nowrap'>";
		print $riga['CF']."</td>\n";
		print "<td nowrap='nowrap'>";
		print	($riga['sesso'] == "M") ? "maschio</td>\n" : "femmina</td>\n";
		print "<td nowrap='nowrap'>";
		$data = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['n_data']);
		print "$data</td>";
		print "<td nowrap='nowrap'>";
		print $riga['cittadinanza']."</td>\n";
		print "<td nowrap='nowrap'>";
		print $riga['r_via']."</td>\n";
		print "<td nowrap='nowrap'>";
		print $riga['r_numero']."</td>\n";
		print "<td nowrap='nowrap'>";
		print $riga['r_citta']."</td>\n";
		print "<td nowrap='nowrap'>";
		print $riga['r_provincia']."</td>\n";

		print "<td nowrap='nowrap'>";
		print "<form method=\"post\" action=\"ProspettoScolastico.php\">".
          "<input type=\"hidden\" name=\"id\" value=\"{$riga['id_studente']}\" />".
          "<input type=\"image\" src=\"./immagini/prospetto.png\" alt=\"Prospetto scolastico\" title=\"Prospetto scolastico\"/></form></td>";
		
		print "<td nowrap='nowrap'>";
		print "<form method=\"post\" action=\"DocumentiProgrammazione.php\">".
          "<input type=\"hidden\" name=\"id_studente\" value=\"{$riga['id_studente']}\" />".
          "<input type=\"image\" src=\"./immagini/fileopen.png\" alt=\"Documenti programmazione\" title=\"Documenti programmazione\"/></form></td>";
		
		print "</tr>";
	}
	print "</table>";

  if (($profile==ID_ADMIN)|| ($profile == ID_OPERATORE))
   $up="index.php";
  else
   $up="indice_aff.php";
  if (isset($up)) echo "<br><center><a href=\"$up\"><img src=\"immagini/menu.png\"></a><br>Pagina iniziale</center>";
  include "Coda.inc";
}    
?>
