<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file: VisualizzaUtenti.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: Visualizza l'elenco degli utenti con alcune informazioni
// (telefono, cellulare, e-mail, ruolo, codice utente
// 22/7/09 Aggiunto docente domiciliare (Sophia Danesino)
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore, operatori
// ----------------------------------------------------------------------

$title= "Visualizza utenti";
include("Testa.inc");
include("FunzioniDB.inc");
autorizza_ruoli(ID_ADMIN,ID_OPERATORE);

js_validazione();

if(isset($_POST['cancella'])){
  if ($_POST['ruolo']==ID_OSPEDALIERO)
  {
    // verifico che non sia inserito in un Consiglio di Classe
    $res =& $link->query("SELECT * FROM  CdC WHERE (id_utente= ? )",$_POST['id_utente']);
    $num_righe = $res->numRows(); 
    if ( $num_righe != 0)
          die("<dl><dd>Registro - Impossibile cancellare un docente inserito in un Consiglio di Classe</dd></dl>");
   }
  if ($_POST['ruolo']==ID_OSSERVATORE||$_POST['ruolo'] == ID_AFFIDATARIO)
  {
    // verifico che non sia inserito in un Consiglio di Classe
    $res =& $link->query("SELECT * FROM  Esterni WHERE (id_utente= ? )",$_POST['id_utente']);
    $num_righe = $res->numRows();
    if ( $num_righe != 0)
          die("<dl><dd>Registro - Impossibile cancellare un utente associato ad uno studente</dd></dl>");
  }
  $res =& $link->query("DELETE FROM Utenti WHERE (id_utente= ? )",$_POST['id_utente']);
  errore_DB($res);
  echo "<dl><dt>Utente cancellato con successo</dt></dl>";
}

$res =& $link->query("SELECT * FROM Utenti,Ruoli WHERE Utenti.id_ruolo=Ruoli.id_ruolo ORDER BY cognome");

// 	$elenco = mysql_query($sql);
$num_righe = $res->numRows(); 

	if ($num_righe==0)
		print "<h3>La ricerca non ha individuato alcun elemento</h3>";
	else
	{
		print "<h3>La ricerca ha rilevato $num_righe ";
		echo ($num_righe>1) ? "elementi</h3>" : "elemento</h3>";

		print "<table class=\"elenco\">";
		print "<tr>";
    		print '<th>Cognome</th>';
    		print '<th>Nome </th>';
    		print '<th>Telefono</th>';
    		print '<th>Cellulare</th>';
    		print '<th>E-mail</th>';
    		print '<th>Note</th>';
    		print '<th>Ruolo</th>';
		if ($profile == ID_ADMIN)
		{
    			print '<th>Codice utente</th>';
    			print '<th colspan="4">Azione</th>';
		}
		print "</tr>";

		$pari=1;
		while ($riga =& $res->fetchRow())
		{
			$class =	($pari) ? "pari" : "dispari";
			$pari=1-$pari;
		echo "<tr class='$class'>";
    	print "<td nowrap='nowrap'>{$riga['cognome']}</td>";
    	print "<td nowrap='nowrap'>{$riga['nome']}</td>";
    	print "<td nowrap='nowrap'>{$riga['telefono']}</td>";
    	print "<td nowrap='nowrap'>{$riga['cellulare']}</td>";
    	print "<td nowrap='nowrap'>{$riga['email']}</td>";
    	print "<td nowrap='nowrap'>{$riga['note']}</td>";
    	print "<td nowrap='nowrap'>";
		print "&nbsp;".$riga['descrizione']."&nbsp;</td>";
 		
		if ($profile == ID_ADMIN)
			{
    			print "<td nowrap='nowrap'>{$riga['username']}</td>";
								
				// modifica dati utenti
    			print "<td align=\"center\" nowrap='nowrap'>"; //<a href='FormModificaUtente.php?id_utente={$riga['id_utente']}'>";
				echo "<form method=\"post\" action=\"ModificaUtente.php\"><input type=\"hidden\" name=\"id_utente\" value=\"{$riga['id_utente']}\" />";
				echo "<input type=\"image\" src=\"./immagini/button_edit.png\" alt=\"Modifica\" title=\"Modifica\"/></form></td>";

				//	print '<img hspace="7" src="./immagini/button_edit.png" alt="Modifica" title="Modifica" border="0" /></a></td>';
			
				// elimina utente
   				print "<td align='center' nowrap='nowrap'>";
    			if (($riga['id_ruolo'] == ID_OSSERVATORE)||($riga['id_ruolo'] == ID_OSPEDALIERO)||($riga['id_ruolo'] == ID_DOMICILIARE)||($riga['id_ruolo'] == ID_AFFIDATARIO) ||($riga['id_ruolo'] == ID_OPERATORE)) 
				 {
					echo "<form method=\"post\" action=\"{$_SERVER['PHP_SELF']}\" onSubmit=\"return validazione()\"><input type=\"hidden\" name=\"id_utente\" value=\"{$riga['id_utente']}\" />".
            		 "<input type=\"hidden\" name=\"cancella\" value=\"1\" />".
            		 "<input type=\"hidden\" name=\"ruolo\" value=\"{$riga['id_ruolo']}\" />".
            		 "<input type=\"image\" src=\"./immagini/button_drop.png\" alt=\"Cancella\" title=\"Cancella\"/></form>";
    			}
    			print "</td>";
    				

				// associazione utente - studente (solo per insegnanti affidatari e genitori)
   				if ($riga['id_ruolo'] == ID_OSSERVATORE||$riga['id_ruolo'] == ID_AFFIDATARIO||$riga['id_ruolo'] == ID_DOMICILIARE)
					{
					// visualizza associazioni
    			  print "<td align=\"center\" nowrap='nowrap'>"; 
            echo "<form method=\"post\" action=\"VisualizzaAssociazioniStudenti.php\"><input type=\"hidden\" name=\"id_utente\" value=\"{$riga['id_utente']}\" />";
            echo "<input type=\"image\" src=\"./immagini/button_index.png\" alt=\"Informazioni\" title=\"Visualizza associazione utente-studente\"/></form></td>";

					// gestione associazioni
    			  print "<td align=\"center\" nowrap='nowrap'>"; 
            echo "<form method=\"post\" action=\"AssociaStudenti.php\"><input type=\"hidden\" name=\"id_utente\" value=\"{$riga['id_utente']}\" />";
            echo "<input type=\"image\" src=\"./immagini/forum.png\" alt=\"Associa\" title=\"Gestione associazione utente-studente\"/></form></td>";
				   }
				else 
				{
					print "<td align=\"center\" nowrap='nowrap'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
					print "<td align=\"center\" nowrap='nowrap'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
				}
				
			}
			print "</tr>";
	 	}
		print "</table>";
	}
$up="indice.php";
if(isset($up)) echo "<br><br><center><a href=\"$up\"><img src=\"immagini/menu.png\"></a><br>Pagina Iniziale</center>";
include "Coda.inc";
?>

