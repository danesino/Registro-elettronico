<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  VisualizzaStudente.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: visualizza informazioni anagrafiche relative ad uno 
// studente
// 22/7/09 Tolte informazioni sulle lingue straniere (Sophia Danesino)
// 9/7/10 Aggiunta città e provincia scuole
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore, operatore
// ----------------------------------------------------------------------

$title = "Visualizza Studente";
include("Testa.inc");
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN,ID_OPERATORE,ID_OSPEDALIERO);

$sql = $link->query("SELECT * FROM Studenti WHERE  ( id_studente = ? )" , $_POST['id']);
errore_DB($sql);
$riga = $sql->fetchRow(DB_FETCHMODE_ASSOC);
?>
<h2>Visualizza informazioni complete</h2>
<table class="elenco">
	<tr><th>Cognome</th>	<td bgcolor="#DDDDDD" colspan="6"><?=$riga['cognome']?></td></tr>
	<tr><th>Nome</th> <td bgcolor="#DDDDDD" colspan="6"><?=$riga['nome']?></td></tr>
	<tr><th>Sesso</th> <td bgcolor="#DDDDDD" colspan="6"><?echo ($riga['sesso'] == "M") ? "maschio </td></tr>" : "femmina </td></tr>";?>
	<tr><th>Codice fiscale</th> <td bgcolor="#DDDDDD" colspan="6"><?=$riga['CF']?></td></tr>
	<tr><th>Data di nascita</th> <td bgcolor="#DDDDDD" colspan="6"><?
	$data = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['n_data']);
	print "$data";?></td></tr>
  <tr><th>Luogo di nascita</th>  
		<td bgcolor="#CDEFE7" align="center" >Citt&agrave;</td>
		<td bgcolor="#DDDDDD"><?=$riga['n_citta']?></td>
		<td bgcolor="#CDEFE7" align="center" >Provincia</td>
		<td bgcolor="#DDDDDD"><?=$riga['n_provincia']?></td>
		<td bgcolor="#CDEFE7" align="center" >Stato</td>
		<td bgcolor="#DDDDDD"><?=$riga['n_stato']?></td></tr>
	<tr><th rowspan="2">Residenza</th>
	 <td bgcolor="#CDEFE7" align="center">Citt&agrave; </td>
	 <td bgcolor="#DDDDDD"><?=$riga['r_citta']?></td>
	 <td bgcolor="#CDEFE7" align="center" >Provincia</td>
	 <td bgcolor="#DDDDDD"><?=$riga['r_provincia']?></td>
	 <td bgcolor="#CDEFE7" align="center" >Stato</td>
    <td bgcolor="#DDDDDD"><?=$riga['r_stato']?></td></tr>
	<tr>
	 <td bgcolor="#CDEFE7" align="center" >Via</td>
	 <td bgcolor="#DDDDDD"><?=$riga['r_via']?></td>
	 <td bgcolor="#CDEFE7" align="center" >Numero</td>
	 <td bgcolor="#DDDDDD"><?=$riga['r_numero']?></td>
	 <td bgcolor="#CDEFE7" align="center" >CAP</td>
	 <td bgcolor="#DDDDDD"><?=$riga['r_cap']?></td></tr>
  </td></tr>
  	<tr><th>Straniero</th> <td bgcolor="#DDDDDD" colspan="6"><?echo ($riga['straniero'] == "0") ? "NO </td></tr>" : "SI </td></tr>";?>
  	<tr><th>HC</th> <td bgcolor="#DDDDDD" colspan="6"><?echo ($riga['HC'] == "0") ? "NO </td></tr>" : "SI </td></tr>";?>

	<tr><th>Cittadinanza</th><td bgcolor="#DDDDDD" colspan="6"><?=$riga['cittadinanza']?></td></tr>
	<tr><th>Telefono</th><td bgcolor="#DDDDDD" colspan="6"><?=$riga['r_telefono']?></td></tr>
	<tr><th>Cellulare</th><td bgcolor="#DDDDDD" colspan="6"><?=$riga['r_cellulare']?></td></tr>
	<tr><th>Indirizzo e-mail</th><td bgcolor="#DDDDDD" colspan="6"><?=$riga['email']?></td></tr>
<?

// se lo studente è frequentante stampo le informazioni.
// 	
$sql_reparto = "SELECT Reparti.nome,Reparto.tipo_degenza, Degenze.data_inizio FROM Reparto,Reparti,Degenze 
		WHERE Degenze.id_studente={$riga['id_studente']} AND Degenze.data_fine='0000-00-00' AND Reparto.id_degenza=Degenze.id_degenza AND Reparto.id_reparto=Reparti.id_reparto AND Reparto.attivo='S'";
$r =& $link->query($sql_reparto);
$num_righe = $r->numRows();
if ($num_righe!=0)
{
	 print '<tr><th>Reparto</th>';
	 $riga_reparti =& $r->fetchRow(); 
	 print '<td bgcolor="#DDDDDD" colspan="6">'.$riga_reparti['nome']."</td></tr>\n";
	 print '<tr><th>Tipo di degenza</th>';
	 switch ($riga_reparti['tipo_degenza'])
	 {
		  case "DH": print '<td bgcolor="#DDDDDD" colspan="6">Day Hospital'; break;
		  case "DO": print '<td bgcolor="#DDDDDD" colspan="6">Degenza ordinaria'; break;
		  default: print '<td>';
	 }
	 print '<tr><th>Inizio frequenza</th>';
	 print '<td bgcolor="#DDDDDD" colspan="6">'; 
	 $data = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga_reparti['data_inizio']);
	 print "$data</td></tr>";
}	
print "</td></tr>";

 	print '<tr><th>Scuola</td>';
	
  $sql_scuola =& $link->query("SELECT nome,citta,provincia FROM Scuola,Scuole WHERE (Scuola.id_studente = ? ) AND Scuola.tipo='f' AND Scuola.id_scuola=Scuole.id_scuola", $riga['id_studente']);
  $riga_scuola =& $sql_scuola->fetchRow();
  print '<td bgcolor="#CDEFE7" align="center" >Affidataria</td>';
  print "<td bgcolor=\"#DDDDDD\">".$riga_scuola['nome']."&nbsp;".$riga_scuola['citta']."&nbsp;".$riga_scuola['provincia']."</td>";

  $sql_scuola =& $link->query("SELECT nome,citta,provincia FROM Scuola,Scuole WHERE (Scuola.id_studente = ? ) AND Scuola.tipo='p' AND Scuola.id_scuola=Scuole.id_scuola", $riga['id_studente']);
  $riga_scuola =& $sql_scuola->fetchRow();
  print '<td bgcolor="#CDEFE7" align="center">Appartenenza</td>';
  print "<td bgcolor=\"#DDDDDD\">".$riga_scuola['nome']."&nbsp;".$riga_scuola['citta']."&nbsp;".$riga_scuola['provincia']."</td></tr>";
  // Classe frequentata
  print "<tr><th>Classe</th><td bgcolor='#DDDDDD' colspan='6'>".$riga['classe']."</td></tr>";
  switch($riga['ordine']) {
  	case 'M': $ordine='Scuola dell\'infanzia'; break;
  	case 'P': $ordine='Scuola primaria'; break;
  	case 'I': $ordine='Scuola secondaria di primo grado'; break;
  	case 'S': $ordine='Scuola secondaria';
  }
  print "<tr><th>Ordine</th><td bgcolor='#DDDDDD' colspan='6'>".$ordine."</td></tr>";
  
  ?>
  <tr><th>Esame di stato</th> <td bgcolor="#DDDDDD" colspan="6"><?echo ($riga['esame'] == "0") ? "NO </td></tr>" : "SI </td></tr>";?>
  <tr><th>Ripetente</th> <td bgcolor="#DDDDDD" colspan="6"><?echo ($riga['ripetente'] == "0") ? "NO </td></tr>" : "SI </td></tr>";?>
  <tr><th>Studio Religione cattolica</th> <td bgcolor="#DDDDDD" colspan="6"><?echo ($riga['RC'] == "0") ? "NO </td></tr>" : "SI </td></tr>";?>
  <tr><th>Prima lingua straniera</th><td bgcolor="#DDDDDD" colspan="6"><?=$riga['lingua1']?></td></tr>
  <tr><th>Seconda lingua straniera</th><td bgcolor="#DDDDDD" colspan="6"><?=$riga['lingua2']?></td></tr>
  <tr><th>Terza lingua straniera</th><td bgcolor="#DDDDDD" colspan="6"><?=$riga['lingua3']?></td></tr>

<?php  
	print '<tr><th>Note informative</th>';
   print '<td bgcolor="#DDDDDD" colspan="6">'.$riga['note']."</td></tr>\n";
	print "</table>\n";
	
	// storico periodi di frequenza in scuola ospedaliera
	// ==========>>>>   $sql =& $link->query("SELECT * FROM Degenze WHERE (id_studente = ? ) AND data_fine != '0000-00-00'", $riga['id_studente']);
	$sql =& $link->query("SELECT * FROM Degenze WHERE data_fine != '0000-00-00' and id_studente = {$riga['id_studente']}");
  	errore_DB($sql);
	$num_righe = $sql->numRows();
	if ($num_righe)
	{
		print "<h3>Periodi di frequenza scuola ospedaliera</h3>";	
		print "<table class='elenco'>";
		print "<tr>";
		print '<th colspan="2">Periodo di frequenza</th>';
		print '<th>Reparto (Tipo degenza)</th>';
 		print "</tr>";
	
		$pari=1;
		while ($riga =& $sql->fetchRow())
		{
			$class = ($pari) ? "pari" : "dispari";
   		$pari=1-$pari;
			print "<tr class='$class'>";  		
			print "<td nowrap='nowrap'>\n";
			$data = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['data_inizio']);
			print "$data</td>\n";
			print "<td nowrap='nowrap'>";
			$data = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['data_fine']);
			print "$data</td>\n";
			print "<td nowrap='nowrap'>\n";
			
			$sql =& $link->query("SELECT nome,tipo_degenza FROM Reparto,Reparti WHERE ( Reparto.id_degenza = ? ) AND Reparto.id_reparto=Reparti.id_reparto", $riga['id_degenza']);
   		while ($riga_reparti =& $sql->fetchRow())
	   	{			
				print $riga_reparti['nome']." (";
				switch ($riga_reparti['tipo_degenza'])
				{ 
	  				case "DH": print 'Day Hospital)'; break;
	  				case "DO": print 'Degenza ordinaria)'; break;
	  				default: print '';
				}
				print "<br>";
			}		
			print "</td></tr>";
		}
		print "</table>";	
  }	

 $up="VisualizzaStudenti";	
 include "Coda.inc";
?>
