<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file: FormRicercaStudente.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: modulo per la ricerca di informazioni su uno studente in
// base al suo cognome
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore
// ----------------------------------------------------------------------

$title = "Ricerca Studente";

include "Testa.inc";
include "FunzioniDB.inc";
require "HTML/QuickForm.php";
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

echo "<h3>Ricerca - Ricerca nel cognome, nome, prospetto scolastico</h3>";
	
$form = new HTML_QuickForm('form');
$form->addElement('text', 'cognome', 'Parola chiave: ');
$form->addElement('submit', 'cerca', 'Cerca');
$form->display();

if(isset($_POST['cerca']))
{
 if(isset($_POST['cancella'])){
  $r =& $link->query("SELECT * FROM  Degenze WHERE (id_studente= ? )",$_POST['id']);
  $n =& $r->numRows();
  if ( $n != 0)
   die("<dl><dd>Registro - Impossibile cancellare uno studente gi&agrave; degente in ospedale</dd></dl>");
  $sql = $link->query("DELETE FROM Studenti WHERE (id_studente= ? )", $_POST['id']);
  errore_DB($sql);
  print("<dl><dt>Cancellazione effettuata con successo</dt></dl>");
 }

	$sql =& $link->query("SELECT * FROM Studenti LEFT JOIN Degenze USING (id_studente) LEFT JOIN Registro USING (id_degenza)  WHERE (cognome LIKE '%{$_POST['cognome']}%' OR nome LIKE '%{$_POST['cognome']}%' OR argomenti LIKE '%{$_POST['cognome']}%') GROUP BY cognome");
	/*
	SELECT *, MATCH( cognome ) AGAINST('%{$_POST['cognome']}%') OR MATCH( argomenti ) AGAINST('%{$_POST['cognome']}%') AS score FROM Studenti LEFT JOIN Registro ON Studenti.id_studente = Registro. WHERE MATCH( name ) AGAINST('%{$_POST['cognome']}%') OR MATCH( text ) AGAINST('%{$_POST['cognome']}%') */
  errore_DB($sql);
	$num_righe = $sql->numRows();
	
	if (!$num_righe):
  	echo "<dl><dd>La ricerca non ha individuato alcun elemento</dd></dl>";
  else:
  	print "<h3> La ricerca ha rilevato ";
  	echo ($num_righe>1) ? "$num_righe studenti</h3>" : "uno studente</h3>";
?>

<table class="elenco">
 <tr>
  <th>Cognome</th>
  <th>Nome</th>
  <th>Codice fiscale</th>
  <!--th>Sesso</th-->
	<th>Degente dal</th>
	<th>Sommario</th>
  <th>Data di nascita</th>
  <th colspan="5">Azione</th>
 </tr>
<?
$pari=1;
while ($riga =& $sql->fetchRow(DB_FETCHMODE_ASSOC)):
	$class = ($pari) ? "pari" : "dispari";
	$pari = 1-$pari;
?>
 <tr class="<?=$class?>">
  <td nowrap='nowrap'><?=$riga['cognome']?></td>
  <td nowrap='nowrap'><?=$riga['nome']?></td>
  <td nowrap='nowrap'><?=$riga['CF']?></td>
  <!--td nowrap='nowrap'><?php// echo ($riga['sesso'] == "M") ? "maschio</td>" : "femmina</td>";?> -->
  <td nowrap='nowrap'><?=$riga['data_inizio']?></td>
  <td nowrap='nowrap'><?=$riga['argomenti']?></td>
  <td nowrap='nowrap'><?php $data = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['n_data']); print "$data";?></td>
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
   <form method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="return validazione()">
    <input type="hidden" name="id" value="<?=$riga['id_studente']?>" />
    <input type="hidden" name="cancella" value="1" />
    <input type="image" src="./immagini/button_drop.png" alt="Cancellazione studente" title="Cancellazione studente" />
   </form>
  </td>
 </tr>
<?
 endwhile;
  print "\t</table>\n\n";
 endif;
}

$up="VisualizzaStudenti";
include "Coda.inc";
?>
