<?php
// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere - tulip
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) Version 2 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// Please READ carefully the Docs/License.txt file for further details
// Please READ the Docs/credits.txt file for complete credits list
// ----------------------------------------------------------------------
// Nome file: AssociaStudenti.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: visualizza l'elenco degli studenti e consente l'associazione 
// tra un utente (insegnante affidatario/genitore) e uno studente, o la 
// cancellazione di un'associazione precedentemente effettuata
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore
// ----------------------------------------------------------------------

$title = "Associazione studente-utente";

include "Testa.inc";
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN);


print "<h2>Associazione studente - utente</h2>";


if(isset($_POST['associa'])){
  $res =& $link->query("INSERT INTO Esterni (id_studente, id_utente) VALUES ('{$_POST['id_studente']}', '{$_POST['id_utente']}')");
  errore_DB($res);
  echo "<dl><dt>Associazione utente - studente effettuata con successo</dt></dl>";
}
if(isset($_POST['dissocia'])){
  $res =& $link->query("DELETE FROM Esterni WHERE id_studente='{$_POST['id_studente']}' AND id_utente='{$_POST['id_utente']}'");
  errore_DB($res);
  echo "<dl><dt>Eliminazione associazione utente - studente effettuata con successo</dt></dl>";
}


$res =& $link->query("SELECT * FROM Studenti ORDER BY cognome");
$num_righe = $res->numRows();  
?>
<h3>Numero studenti inseriti: <?=$num_righe?> </h3>

<table class="elenco">
<tr>
<th>Cognome</th>
<th>Nome</th>
<th>Codice fiscale</th>
<th>Sesso</th>
<th>Data di nascita</th>
<th>Associa</th>
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
    print ($riga['sesso'] == "M") ? "maschio</td>" : "femmina</td>";
    print "<td nowrap='nowrap'>";
    $data = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['n_data']);
    print "$data</td>";

		// verifico che non sia già stato associato
    $sql_associato = "SELECT * FROM  Esterni WHERE Esterni.id_utente=".$_POST['id_utente']." AND Esterni.id_studente=".$riga['id_studente'];
 		$sql =& $link->query($sql_associato);
		$num_righe_associato = $sql->numRows(); 
    
		print "<td align=\"center\" nowrap='nowrap'>";
		if ( $num_righe_associato == 0)
		{	
			echo "<form method=\"post\" action=\"{$_SERVER['PHP_SELF']}\">".
           "<input type=\"hidden\" name=\"id_utente\" value=\"{$_POST['id_utente']}\" />".
           "<input type=\"hidden\" name=\"id_studente\" value=\"{$riga['id_studente']}\" />".
           "<input type=\"hidden\" name=\"associa\" value=\"1\" />".
           "<input type=\"image\" src=\"./immagini/forum.png\" alt=\"Associa\" title=\"Associa studente-utente\"/></form>";
		}	
		else
		{	
			echo "<form method=\"post\" action=\"{$_SERVER['PHP_SELF']}\">".
           "<input type=\"hidden\" name=\"id_utente\" value=\"{$_POST['id_utente']}\" />".
           "<input type=\"hidden\" name=\"id_studente\" value=\"{$riga['id_studente']}\" />".
           "<input type=\"hidden\" name=\"dissocia\" value=\"1\" />".
           "<input type=\"image\" src=\"./immagini/button_drop.png\" alt=\"Dissocia\" title=\"Cancella associazione studente-utente\"/></form>";
		}	
    print "</td>";
		print "</tr>";
	}
	print "</table>";
$up="VisualizzaUtenti.php";
	if(isset($up)) echo "<br><br><center><a href=\"$up\"><img src=\"immagini/menu.png\"></a><br>Pagina Iniziale</center>";
include "Coda.inc";
?>
