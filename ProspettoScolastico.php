<?php
	
// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  ProspettoScolastico.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: Visualizza i periodi di frequenza alla scuola ospedaliera,
// i reparti e il tipo di degenza per ogni periodo, i gruppi di lavoro
// che ha frequentato in ogni periodo.
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore, operatore, docenti del 
// consiglio di classe di uno studente, docenti afidatari e genitori 
// associati allo studente
// ----------------------------------------------------------------------

$title = "Visualizzazione prospetto scolastico studente";

include "Testa.inc";
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN,ID_OPERATORE,ID_OSPEDALIERO,ID_OSSERVATORE,ID_AFFIDATARIO, ID_DOMICILIARE);
	
include("autenticazione_db.php"); 
if ($profile==ID_OSSERVATORE||$profile==ID_AFFIDATARIO||$profile==ID_DOMICILIARE)
	autorizza_affidatario_genitore($_POST['id'],$codice_utente);
	
if ($profile==ID_OSPEDALIERO)
	// Verifica autorizzazione da parte del docente a esaminare
	// il prospetto scolastico di quello studente
	autorizza_docente_studente($_POST['id'],$codice_utente);

	$sql =& $link->query("SELECT * FROM Studenti WHERE id_studente={$_POST['id']}");
	$riga =& $sql->fetchRow();

	print "<h2>Prospetto scolastico {$riga['cognome']} {$riga['nome']}</h2>";
	echo "<center><form method=\"post\" action=\"DettaglioPeriodoAnnuale.php\">".
       "<input type=\"hidden\" name=\"id_studente\" value=\"{$riga['id_studente']}\" />".
       "<input type=\"hidden\" name=\"id_degenza\" value=\"{$riga['id_degenza']}\" />".
       "<input type=\"submit\" value=\"Prospetto Scolastico Annuale\"/></form>";
    echo "<br>";
   	echo "<form method=\"post\" action=\"DettaglioPeriodoSpecifico.php\">".
       "<input type=\"hidden\" name=\"id_studente\" value=\"{$riga['id_studente']}\" />".
       "<input type=\"hidden\" name=\"id_degenza\" value=\"{$riga['id_degenza']}\" />".
       "<input type=\"submit\" value=\"Prospetto Scolastico Periodo Specifico\"/></form>";
     echo "</center><br>";
     
	// storico periodi di frequenza in scuola ospedaliera
	echo "<h3>Storico periodi di frequenza</h3>";
	$sql =& $link->query("SELECT * FROM Degenze WHERE Degenze.id_studente =".$_POST['id']." ORDER BY id_degenza");
	$num_righe = $sql->numRows(); 
	if ( $num_righe != 0)
	{
		print '<table class="elenco">';
		print "<tr>";
		print '<th colspan="2">Periodo di frequenza</th>';
		if (($profile==ID_ADMIN)|| ($profile == ID_OPERATORE))
		{		
			print '<th>Reparto - Tipo degenza - Data inizio frequenza in reparto</th>';
			print '<th>Gruppo di lavoro</th>';
			print '<th colspan="3">Azione</th>';
		}
		else
			print '<th colspan="2">Azione</th>';
 		print "</tr>";
		
	 	$pari=1;
		while ($riga =& $sql->fetchRow())
		{
			$class = ($pari) ? "pari" : "dispari";
			$pari=1-$pari;
			print "<tr class='$class'><td nowrap='nowrap'>";
			$data_inizio = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['data_inizio']);
			print $data_inizio."</td>";
			print "<td nowrap='nowrap'>";
			if ($riga['data_fine'] == '0000-00-00')
				print "frequentante";	
			else
			{
				$data = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['data_fine']);
				print "$data</td>";
			}
			
			if (($profile==ID_ADMIN)|| ($profile == ID_OPERATORE))
			{
				// visualizza reparto/i e tipo di degenza
				$sql_reparto =& $link->query("SELECT * FROM Reparto,Reparti WHERE
				 	Reparto.id_degenza={$riga['id_degenza']} AND 
				 	Reparto.id_reparto=Reparti.id_reparto ORDER BY Reparto.id_repdeg");
				$num_righe_reparto = $sql_reparto->numRows();
				if ( $num_righe_reparto == 1)
				{
					$riga_reparto = $sql_reparto->fetchRow();
					print "<td nowrap='nowrap'><center>".$riga_reparto['nome'];
					switch ($riga_reparto['tipo_degenza'])
					{ 
	  					case "DH": print '&nbsp;-&nbsp;Day Hospital&nbsp;'; break;
	  					case "DO": print '&nbsp;-&nbsp;Degenza ordinaria&nbsp;'; break;
						case "SC":
	  					default: print '';
					}	
					print "-&nbsp;".$data_inizio;		
					print "</center></td>";
				}
				else  // più reparti nello periodo di degenza
				{
				   print "<td nowrap='nowrap'>";
				   while ($riga_reparto =& $sql_reparto->fetchRow())
				   {
					  if (($riga_reparto['attivo']=='S') && ($riga['data_fine']=='0000-00-00'))
						print "<b>";
					print "{$riga_reparto['nome']}";
				   	switch ($riga_reparto['tipo_degenza'])
				   	{ 
	  					case "DH": print '&nbsp;-&nbsp;Day Hospital&nbsp;'; break;
	  					case "DO": print '&nbsp;-&nbsp;Degenza ordinaria&nbsp;'; break;
						default: print '';
				   	}
					
					if ($riga_reparto['data_cambio'] != '0000-00-00') {
						$data_cambio = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga_reparto['data_cambio']);
						print "-&nbsp;".$data_cambio;
					}
					else
						print "-&nbsp;".$data_inizio;		
					if (($riga_reparto['attivo']=='S') && ($riga['data_fine'] == '0000-00-00'))
						print "</b>";
					print "<br />";					
				   }
				   print "</td>";					
				 }			
			
				// Classi frequentate
				$sql_classe =& $link->query("SELECT DISTINCT * FROM Classe, Classi WHERE
				 	Classe.id_degenza={$riga['id_degenza']} AND
					Classe.id_classe=Classi.id_classe ORDER BY Classe.id_clasdeg");
				$num_righe_classe = $sql_classe->numRows();
				if ( $num_righe_classe == 1)
				{
					// lo studente è sempre stato nella stessa classe
					$riga_classe = $sql_classe->fetchRow();
					print "<td nowrap='nowrap'><center>{$riga_classe['classe']}</center></td>";
				}
				else
				{
					// lo studente ha frequentato più classi nello stesso periodo di degenza
				   	print "<td nowrap='nowrap'>";
				   	while ($riga_classe = $sql_classe->fetchRow())
					{
						if (($riga_classe['attivo']=='S')&& ($riga['data_fine'] == '0000-00-00'))
							print "<b>{$riga_classe['classe']}</b><br />";
						else
							print "{$riga_classe['classe']}<br />";
				   	}
					print "</td>";
				}
			}
		
			echo "<td align=\"center\" nowrap='nowrap'>";
			echo "<form method=\"post\" action=\"DettaglioPeriodo.php\">".
           "<input type=\"hidden\" name=\"id_studente\" value=\"{$riga['id_studente']}\" />".
           "<input type=\"hidden\" name=\"id_degenza\" value=\"{$riga['id_degenza']}\" />".
           "<input type=\"image\" src=\"./immagini/prospetto.png\" alt=\"Dettaglio periodo\" title=\"Dettaglio periodo\"/></form></td>";

			print "<td align=\"center\" nowrap='nowrap'>";
			echo "<form method=\"post\" action=\"StampaDettaglioPeriodo.php\">".
           "<input type=\"hidden\" name=\"id_studente\" value=\"{$riga['id_studente']}\" />".
           "<input type=\"hidden\" name=\"id_degenza\" value=\"{$riga['id_degenza']}\" />".
           "<input type=\"image\" src=\"./immagini/stampa.png\" alt=\"Stampa dettaglio periodo\" title=\"Stampa dettaglio periodo\"/></form></td>";
			
			if ($profile==ID_ADMIN)
			{
				print "<td align=\"center\" nowrap='nowrap'>";
				echo "<form method=\"post\" action=\"ModificaDettaglioPeriodo.php\">".
				"<input type=\"hidden\" name=\"id_studente\" value=\"{$riga['id_studente']}\" />".
				"<input type=\"hidden\" name=\"id_degenza\" value=\"{$riga['id_degenza']}\" />".
				"<input type=\"image\" src=\"./immagini/button_edit.png\" alt=\"Modifica informazioni periodo\" title=\"Modifica informazioni periodo\"/></form></td>";
  			}
			print "</tr>";
	 	}
		print "</table>";		
	}

	if ($profile==ID_OSSERVATORE||$profile==ID_AFFIDATARIO||$profile==ID_DOMICILIARE)
		$up="indice_aff.php";
	elseif($profile==ID_OSPEDALIERO)
		$up="Registro.php";
		
if(isset($up)) echo "<br><br><center><a href=\"$up\"><img src=\"immagini/menu.png\"></a><br>Pagina Iniziale</center>";
include "Coda.inc";
?>
