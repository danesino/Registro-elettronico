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

$title = "Visualizzazione prospetto degenze studenti";

include "Testa.inc";
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN);
	
include("autenticazione_db.php"); 

	print "<h3>Prospetto degenze multiple</h3>";
	print '<table class="elenco">';
	print "<tr>";
	print '<th>Cognome</th>';
	print '<th>Nome</th>';
	print '<th colspan="2">Periodo di frequenza</th>';
	print '<th>Reparto - Tipo degenza - Data inizio frequenza in reparto</th>';
	print '<th>Gruppo di lavoro</th>';
	print "</tr>";
 	$pari=1;

    // Esamino tutti gli studenti
	$sql_studente =& $link->query("SELECT * FROM Studenti");
	while ( $studente  =& $sql_studente->fetchRow())
    {	//while00 per tutti gli studenti
  
		$sql =& $link->query("SELECT * FROM Degenze WHERE Degenze.id_studente =".$studente['id_studente']." ORDER BY id_degenza");
		$num_righe = $sql->numRows(); 
		
		if ( $num_righe > 1)
		{	
			$class = ($pari) ? "pari" : "dispari";
			$pari=1-$pari;

			while ($riga =& $sql->fetchRow())
			{ //while01 per tutte le degenze
			print "<tr class='$class'><td nowrap='nowrap'>".$studente['cognome']."</td><td nowrap='nowrap'>".$studente['nome'];
				print "<td nowrap='nowrap'>";
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
				print "</tr>";
			}
		
			
	 	}
	 } // end while00
	 print "</table>";		
	
	
echo "<br><br><center><a href=\"indice.php\"><img src=\"immagini/menu.png\"></a><br>Pagina Iniziale</center>";
include "Coda.inc";
?>
