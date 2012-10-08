<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  VisualizzaStudenti.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: visualizza elenco studenti con informazioni elementari 
// Modifica 22/7/09: visualizza classe e non CF (Sophia Danesino)
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore, operatore
// ----------------------------------------------------------------------
	
$title = "Elenco Studenti";

include "Testa.inc";
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN,ID_OPERATORE);
 	
print " 
<script type=\"text/javascript\">
function validazione(){
  if (confirm(\"Sei proprio sicuro di voler cancellare lo studente?\")) 
  {
    return true;
  }
  else 
  {
    return false;
  }
}</script>
";
print "<h2>Elenco completo studenti</h2>";

if(isset($_POST['cancella'])){
 
	$r =& $link->query("SELECT * FROM  Degenze, Reparto WHERE (id_studente= ? ) and Degenze.id_degenza=Reparto.id_degenza and attivo='S'",$_POST['id']);
  $n = $r->numRows();
  if ( $n != 0)
   die("<dl><dd>Registro - Impossibile cancellare uno studente gi&agrave; degente in ospedale</dd></dl>");
  $sql = $link->query("DELETE FROM Studenti WHERE (id_studente= ? )", $_POST['id']);
  errore_DB($sql);
  exit("<dl><dt>Cancellazione effettuata con successo<br /><br />Per cancellare un altro studente cliccare <a href=\"./VisualizzaStudenti.php\">qui</a></dt></dl>");
}

$sql =& $link->query("SELECT * FROM Studenti ORDER BY cognome --");
$num_righe = $sql->numRows();

if (!$num_righe):
  echo "<dl><dd>Nessuno Studente &egrave; stato inserito</dd></dl>";
else:
  print "<h3> La ricerca ha rilevato ";
  echo ($num_righe>1) ? "$num_righe studenti</h3>" : "uno studente</h3>";
?>

<table class="elenco">
 <tr>
  <th>Cognome</th>
  <th>Nome</th>
  <th>Sesso</th>
  <th>Straniero</th>
  <th>HC</th>
  <th>Data di nascita</th>
  <th>Classe</th>
  <th>Ordine</th>
  <th>Ripetente</th>
  <th>RC</th>
  <th>Esame di stato</th>
  <th>Lingue straniere</th>
  <th>Scuola</th>
  <?php	
     if ($profile==ID_ADMIN)  // l'amministratore può cancellare uno studente
		print('<th colspan="7">Azioni</th>');
	else // l'operatore no
		print('<th colspan="6">Azioni</th>');
  ?>
 </tr>

<?	
$pari=1;
 while ($riga =& $sql->fetchRow(DB_FETCHMODE_ASSOC)):
  $class = ($pari) ? "pari" : "dispari";
  $pari = 1-$pari;
  switch($riga['ordine']) {
  	case 'M': $ordine='Scuola dell\'infanzia'; break;
  	case 'P': $ordine='Scuola primaria'; break;
  	case 'I': $ordine='Scuola secondaria di primo grado'; break;
  	case 'S': $ordine='Scuola secondaria';
  }
?>
 <tr class="<?=$class?>">
  <td nowrap='nowrap'><?=$riga['cognome']?></td>
	<td nowrap='nowrap'><?=$riga['nome']?></td>
	<td nowrap='nowrap'><?php echo ($riga['sesso'] == "M") ? "Maschio</td>" : "Femmina</td>";?>
	<td nowrap='nowrap'><?php echo ($riga['straniero'] == "0") ? "NO</td>" : "SI</td>";?>
	<td nowrap='nowrap'><?php echo ($riga['HC'] == "0") ? "NO</td>" : "SI</td>";?>
	<td nowrap='nowrap'><?php $data = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['n_data']); print "$data";?></td>
	<td nowrap='nowrap'><?=$riga['classe']?></td>
	<td nowrap='nowrap'><?=$ordine?></td>
	<td nowrap='nowrap'><?php echo ($riga['ripetente'] == "0") ? "NO</td>" : "SI</td>";?>
	<td nowrap='nowrap'><?php echo ($riga['RC'] == "0") ? "NO</td>" : "SI</td>";?>
	<td nowrap='nowrap'><?php echo ($riga['esame'] == "0") ? "NO</td>" : "SI</td>";?>
	<td nowrap='nowrap'><?=$riga['lingua1']?>&nbsp;<?=$riga['lingua2']?>&nbsp;<?=$riga['lingua3']?></td>

<?php  
				//Scuola di appartenenza
				$sql_scuola =& $link->query("SELECT nome, Scuola.id_scuola FROM Scuola,Scuole WHERE (Scuola.id_studente = ?  AND Scuola.tipo='p' AND Scuola.id_scuola=Scuole.id_scuola)", $riga['id_studente']);
				errore_DB($sql_scuola); 
	   		$riga_scuola =& $sql_scuola->fetchRow();
  				print "<td nowrap='nowrap'>".$riga_scuola['nome']."</td>";						

     			// Informazioni complete scuola di appartenenza studente 
				print "<td nowrap='nowrap'>";
				print '<form method="post" action="VisualizzaScuola.php?id='.$riga_scuola['id_scuola'].'">';
    			print '<input type="image" src="./immagini/scuola.png" alt="Informazioni complete scuola" title="Informazioni complete scuola">';
   			print " </form></td>";
?>
	<td>
   <form method="post" action="VisualizzaStudente.php">
    <input type="hidden" name="id" value="<?=$riga['id_studente']?>" />
    <input type="image" src="./immagini/button_index.png" alt="Informazioni complete" title="Informazioni complete"/>
   </form>
  </td>
  <td>
   <form method="post" action="ModificaStudente.php">
    <input type="hidden" name="id" value="<?=$riga['id_studente']?>" />
    <input type="image" src="./immagini/button_edit.png" alt="Modifica" title="Modifica"/>
   </form>
  </td>
  <td>
   <form method="post" action="GestioneDegenze.php">
    <input type="hidden" name="id" value="<?=$riga['id_studente']?>" />
    <input type="image" src="./immagini/button_insert.png" alt="Degenze" title="Degenze"/>
   </form>
  </td>
	<td>
   <form method="post" action="ProspettoScolastico.php">
    <input type="hidden" name="id" value="<?=$riga['id_studente']?>" />
    <input type="image" src="./immagini/prospetto.png" alt="Prospetto scolastico" title="Prospetto scolastico"/>
   </form>
  </td>
  
  	<td>
	<form method="post" action="DocumentiProgrammazione.php">
		 <input type="hidden" name="id_studente" value="<?=$riga['id_studente']?>" />
		 <input type="image" src="./immagini/fileopen.png" alt="Documenti programmazione" title="Documenti programmazione"/>
	</form>
	</td>
	
	
	<?php	
	if ($profile==ID_ADMIN) { 
	?>
   <td>
   <form method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="return validazione()">
    <input type="hidden" name="id" value="<?=$riga['id_studente']?>" />
    <input type="hidden" name="cancella" value="1" />
    <input type="image" src="./immagini/button_drop.png" alt="Cancellazione studente" title="Cancellazione studente" />
   </form>
  </td>
     <?php 
   } 
   ?>

 </tr>
<?	
 endwhile;
  print "\t</table>\n\n";
 endif;
$up="index";
include "Coda.inc";
?>
