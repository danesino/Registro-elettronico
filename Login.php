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
// Nome file:  Login.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: Gestisce l'accesso al registro e visualizza il menù 
// principale (per amministratore e operatore), o la scelta del gruppo di 
// lavoro (per docenti e altri utenti).
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore, operatore, docente (solo se 
// insegna in quel gruppo di lavoro), insegnante affidatario (solo se 
// associato a quello studente), genitore (solo se associato a quello studente)
// ----------------------------------------------------------------------

$title = "Login";
include "Testa.inc";			
include "FunzioniDB.inc";
    	
// Visualizza nome ospedale
$elenco =& $link->query("SELECT * FROM Ospedale");	
$elenco->fetchInto($riga);
print '<h1>Scuola ospedaliera '.$riga['nome'].'</h1><br />';

// Visualizza ruolo con cui è stato effettuato il collegamento
$elenco =& $link->query("SELECT * FROM Ruoli WHERE (id_ruolo= ?) LIMIT 1", $RUOLO);	
errore_DB($elenco);
$elenco->fetchInto($ruolo);
$messaggi = $link->getOne('select count(id) from Messaggi where (id_utente_dest = ? AND nuovo = "1") ', $CODICE_UTENTE);
print '<dl><dt>ciao <b>'.$NOME.' '.$COGNOME.'</b> sei collegato come '.$ruolo['descrizione'].'</dt></dl><dl>';
if ($messaggi){
echo '<dt>Hai <b>';
echo ($messaggi>1) ? "$messaggi</b> nuovi messaggi" : "1</b> nuovo messaggio";
echo " <a href='VisualizzaMessaggi.php'>Leggi</a>";}
echo "</dt></dl>";
	
if (($profile==ID_ADMIN) || ($profile==ID_OPERATORE))
	print "<br /><br /><div align=\"center\"><a href=\"indice.php\"><img src=\"immagini/accesso.png\" alt=\"avanti\" title=\"Avanti\" border=\"0\" /></a><p>avanti</p></div>";
else if ($profile==ID_OSPEDALIERO)
	print "<br /><br /><div align=\"center\"><a href=\"indice_doc.php\"><img src=\"immagini/accesso.png\" alt=\"avanti\" border=\"0\" /></a><p>avanti</p></div>";
	else
	print "<br /><br /><div align=\"center\"><a href=\"indice_aff.php\"><img src=\"immagini/accesso.png\" alt=\"avanti\" border=\"0\" /></a><p>avanti</p></div>";

$link->disconnect();
?>
  </div>
 </body>
</html>

