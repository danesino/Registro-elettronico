<?php
 
// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  VisualizzaScuole.php
$title = "Elenco Scuole";
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: visualizza elenco scuole con indirizzo e recapito telefonico
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore, operatore
// ----------------------------------------------------------------------

include("Testa.inc");
autorizza_ruoli(ID_ADMIN,ID_OPERATORE);

	$sql = $link->query("SELECT * FROM Scuole ORDER BY nome --");
	$num_righe = $sql->numRows();

	if (!$num_righe):
		echo "<dl><dd>La ricerca non ha individuato nessuna scuola</dd></dl>";
	else:
		print "<h3>La ricerca ha rilevato ";
		echo ($num_righe>1) ? "$num_righe scuole</h3>" : "una scuola</h3>";
?>
 <table class="elenco">
  <tr>
   <th>Nome</th>
   <th>Indirizzo </th>
   <th>Citt&agrave; </th>
   <th>Provincia </th>
   <th>Telefono</th>
   <th colspan="2">Azione</th>
  </tr>
<?php
 $pari=1;
 while ($riga =& $sql->fetchRow(DB_FETCHMODE_ASSOC)):
  $class = ($pari) ? "pari" : "dispari";
  $pari = 1-$pari;
?>
  <tr class='<?=$class?>'>	
   <td nowrap='nowrap'><?=$riga['nome']?></td>
   <td nowrap='nowrap'><?=$riga['indirizzo']?></td>
   <td nowrap='nowrap'><?=$riga['citta']?></td>
   <td nowrap='nowrap'><?=$riga['provincia']?></td>
   <td nowrap='nowrap'><?=$riga['telefono']?></td>
   <td>
    <form method="post" action="ModificaScuole.php">
     <input type="hidden" name="codice_scuola" value="<?=$riga['codice']?>" /> 
     <input type="hidden" name="id_scuola" value="<?=$riga['id_scuola']?>" /> 
     <input type="image" src="./immagini/button_edit.png" alt="Modifica" title="Modifica"/></form></td>

   <!--td nowrap='nowrap'><a href="ModificaScuole.php?codice=<? //=$riga['codice']?>&amp;id_scuola=<? //=$riga['id_scuola']?>">
    <img hspace="7" src="./immagini/button_edit.png" alt="Modifica" title="Modifica" border="0" /></a></td-->
   <td nowrap='nowrap'><a href="VisualizzaScuola.php?id=<?=$riga['id_scuola']?>">
    <img hspace="7" src="./immagini/button_index.png" alt="Informazioni complete" title="Informazioni complete" border="0" /></a></td></tr>
<?php
  endwhile;
  print "\t</table>\n\n";
 endif;
$up="index";
include "Coda.inc";
?>
