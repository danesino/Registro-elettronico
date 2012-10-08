<?php
	
// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  ModificaDettaglioPeriodo.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: modifica dati relativi ai periodi di degenza di un utente
// (periodo di frequenza, reparti e tipi di degenza)
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore
// ----------------------------------------------------------------------

$title = "Modifica informazioni degenza";

include "Testa.inc";
include "FunzioniDB.inc";
include("data.inc");
autorizza_ruoli(ID_ADMIN);

   
if (isset($_POST['modifica_periodo']))
{
	// modifica dati degenza
	// modifica tabella Degenze con le date di inizio e fine
	$data_inizio=$_POST['anno']."-".$_POST['mese']."-".$_POST['giorno'];
	if(empty($_POST['anno_fine']))
		$data_fine='0000-00-00';
	else
		$data_fine=$_POST['anno_fine']."-".$_POST['mese_fine']."-".$_POST['giorno_fine'];
	$sql =& $link->query('UPDATE  Degenze  SET data_inizio= ? , data_fine= ? WHERE (id_degenza= ? )', array($data_inizio, $data_fine, $_POST['id_degenza']));
	errore_DB($sql);
	exit("<dl><dt>Modifica Periodo effettuata con successo</dt></dl>");
} //end modifica periodo
     	
if (isset($_POST['modifica_reparto']))
{
	// modifica tabella Reparto con il tipo di degenza
	$data=$_POST['anno']."-".$_POST['mese']."-".$_POST['giorno'];
	$sql =& $link->query('UPDATE  Reparto SET tipo_degenza= ? , id_reparto= ?, data_cambio= ? WHERE (id_repdeg= ? )', array($_POST['tipo_degenza'],$_POST['id_reparto'],$data,$_POST['id_repdeg']));
	errore_DB($sql);
	die("<dl><dt>Modifica Reparto effettuata con successo</dt></dl>");
} //end if modifica reparto
    		
if (isset($_POST['cancella_reparto']))
{
	// cancellazione reparto e tipo degenza
	$sql=& $link->query('DELETE FROM Reparto WHERE (id_repdeg= ?)',$_POST['id_repdeg']);
	errore_DB($sql);
	
	//metto a 0 la data di cambio reparto nel primo record successivo
	$sql =& $link->query("SELECT id_repdeg FROM Reparto WHERE (id_degenza = ?)", $_POST['id_degenza']);
	$riga =& $sql->fetchRow(); 
	$sql =& $link->query('UPDATE Reparto SET data_cambio="0000-00-00" WHERE (id_repdeg= ? )', $riga['id_repdeg']);
	errore_DB($sql);
	
	die("<dl><dt>Cancellazione effettuata con successo</dt></dl>");
}
	
$sql =& $link->query("SELECT nome,cognome FROM Studenti WHERE (id_studente = ?)", $_POST['id_studente']);
$riga =& $sql->fetchRow(); 

print "<h2>Modifica prospetto scolastico {$riga['cognome']} {$riga['nome']}</h2>";
 
// storico periodi di frequenza in scuola ospedaliera

$sql =& $link->query("SELECT * FROM Degenze WHERE (Degenze.id_degenza = ? )", $_POST['id_degenza']);
$num_righe = $sql->numRows(); 
if ( $num_righe != 0)
{ // if01
	
		print "<h3>Modifica periodo di frequenza</h3>";
		print "<table border=0>";
		print "<tr>";
		print '<th>Inizio frequenza</th>';
		print '<th>Fine frequenza</th>';
		print '<th>Azione</th>';
 		print "</tr>";
		
		$riga =& $sql->fetchRow();
		print "<form action=\"{$_SERVER['PHP_SELF']}\" method=\"post\">";
		print '<input type="hidden" name="modifica_periodo" value="1">';
		print '<input type="hidden" name="id_degenza" value="'.$_POST['id_degenza'].'">';
		print '<input type="hidden" name="id_studente" value="'.$_POST['id_studente'].'">';
		print "<tr><td>";  		
				
		// visualizzo data inizio degenza			
		$data=split("-", $riga['data_inizio']);
		data($data[2],$data[1],$data[0]);

		// visualizzo data fine degenza
		print "<td>";
		if ($riga['data_fine'] == '0000-00-00')
			print "<center>frequentante</center>";	
		else
		{
			$data_fine=split("-", $riga['data_fine']);
			data_fine($data_fine[2],$data_fine[1],$data_fine[0]);
		}
				
		// bottone modifica
		print '<td align="center" nowrap=\'nowrap\'>';
		print '<INPUT type="submit" value="modifica"></form>';
   	    print "</td>";
		print "</tr></table>";

		
		// modifica reparti e tipo di degenza		
		print "<h3>Modifica reparti e tipo degenza</h3>";
		print "<table border=0>";
		print "<tr>";
		print '<th>Reparto</th>';
		print '<th>Tipo degenza</th>';
		print "<th>Data</th>";
		print '<th colspan="2">Azione</th>';
 		print "</tr>";

  	    // trova reparto/i e tipo/i di degenza
		$sql_reparto =& $link->query("SELECT * FROM Reparto,Reparti WHERE (Reparto.id_degenza= ? ) AND Reparto.id_reparto=Reparti.id_reparto ORDER BY Reparto.id_repdeg", $riga['id_degenza']);
		$num_reparti = $sql_reparto->numRows(); 
		
		while ($riga_reparto =& $sql_reparto->fetchRow())
		{				
			print '<form action="ModificaDettaglioPeriodo.php" method="post">';
			print '<input type="hidden" name="modifica_reparto" value="1">';
			print '<input type="hidden" name="id_repdeg" value="'.$riga_reparto['id_repdeg'].'">';
			print '<input type="hidden" name="id_studente" value="'.$_POST['id_studente'].'">';
			print '<input type="hidden" name="id_degenza" value="'.$_POST['id_degenza'].'">';
			
			
			// visualizzo reparto/reparti
			
			print "<td align=\"center\">";
				
			$sql_reparti = $link->query("SELECT * FROM Reparti");
			print '<select name="id_reparto" >';
			while ( $reparto =& $sql_reparti->fetchRow())
			{
				print '<option value="'.$reparto['id_reparto'].'"';
				if ($reparto['id_reparto'] == $riga_reparto['id_reparto']) print "selected";
				print ">".$reparto['nome']."</option>\n";
			}
			print "</select></td>";
				
			// visualizzo tipo di degenza
			print "<td>";
			print '<select name="tipo_degenza" >';
			print '<option value="DH"'; 
			if ($riga_reparto['tipo_degenza'] == 'DH') 
				print " SELECTED";
			print ">Day Hospital</option>";
			print '<option value="DO"'; 
			if ($riga_reparto['tipo_degenza'] == 'DO')
				print " SELECTED";
			print ">Degenza ordinaria</option></select></td>";

            // Campo data modifica: presenta la data corrente
            print "<td align=\"center\">";
            if ($riga_reparto['data_cambio']=='0000-00-00')
				print "Data inizio frequenza";
			else {
				$data=split("-", $riga_reparto['data_cambio']);
				data($data[2],$data[1],$data[0]);
			}
		    print "</td>";
		    
			// bottone modifica			
			print '<td align="center" nowrap="nowrap">';
			print '<INPUT type="submit" value="modifica"></form></td>';
			
			//link cancellazione reparto
			print "<td>";
			if (($riga_reparto['attivo']=="N") && ($num_reparti > 1))
			{
				print '<form method="post" action="ModificaDettaglioPeriodo.php">';
				print '<input type="hidden" name="id_degenza" value="'.$riga_reparto['id_degenza'].'">';
				print '<input type="hidden" name="id_repdeg" value="'.$riga_reparto['id_repdeg'].'">';
				print '<input type="hidden" name="cancella_reparto" value="1">';
				print '<input type="hidden" name="id_studente" value="'.$_POST['id_studente'].'">';
     			print '<input type="image" src="./immagini/button_drop.png" alt="Elimina" title="Elimina"/></form>';
			}
			print "</td>";
			print "</tr>";		
		}	
		print "</table></tr>";
	} //end if01
include "Coda.inc"; 
?>
