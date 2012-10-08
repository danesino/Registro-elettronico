<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  VisualizzaClassi.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: visualizza l'elenco e la descrizione dei gruppi di lavoro
// esistenti 
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore, operatore
// ----------------------------------------------------------------------
	
$title = "Visualizza gruppi di lavoro";

include "Testa.inc";
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN,ID_OPERATORE);

print "
<script type=\"text/javascript\">
function validazione(){
  if (confirm(\"Sei proprio sicuro di voler cancellare il gruppo di lavoro?\"))
  {
    return true;
  }
  else
  {
    return false;
  }
}</script>
";

if(isset($_POST['cancella'])){
  $r =& $link->query("SELECT * FROM Classe WHERE (id_classe= ? )", $_POST['id_classe']);
  $n =& $r->numRows();
  if ( $n != 0)
   die("<dl><dd>Registro - Impossibile cancellare un gruppo di lavoro contenente studenti</dd></dl>");
  $sql = $link->query("DELETE FROM Classi WHERE (id_classe= ? )", $_POST['id_classe']);
  errore_DB($sql);
  print("<dl><dt>Cancellazione effettuata con successo</dt></dl>");
}


$sql =& $link->query("SELECT * FROM Classi ORDER BY classe");
$num_righe = $sql->numRows();

if (!$num_righe)
	print "<dl><dd>La ricerca non ha individuato alcun elemento</dd></dl>";
else
{ 	
	print "<h3>La ricerca ha rilevato $num_righe ";
	echo ($num_righe>1) ? "elementi</h3>" : "elemento</h3>";
	print "<table class=\"elenco\">";
	print "<tr>";
  print '<th>Nome gruppo</th>';
  print '<th>Ordine </th>';
  print '<th colspan="4">Azione</th>';
	print "</tr>";

	$pari=1;
	while ($riga =& $sql->fetchRow()) 
	{
		$class = ($pari) ? "pari" : "dispari";
    $pari=1-$pari;
		echo "<tr class='$class'>";
    echo "<td nowrap='nowrap'>".$riga['classe']."</td>";
    echo "<td nowrap='nowrap'>";
		switch ($riga['ordine'])
		{ 
			case "i": print "Scuola dell'infanzia</td>"; break;
			case "1": print "Scuola primaria</td>"; break;
			case "2": print "Scuola secondaria di primo grado</td>"; break;
			case "s": print "Scuola secondaria</td>"; break;
		}
	
    echo "<td nowrap='nowrap'>";
		echo '<form method="post" action="ModificaClasse.php"">
    <input type="hidden" name="id_classe" value="'.$riga['id_classe'].'" />
    <input type="image" src="./immagini/button_edit.png" alt="Modifica" title="Modifica" />
    </form></td>';
   		
    echo "<td nowrap='nowrap'>";
    if ($RUOLO!=2){
    echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'" onSubmit="return validazione()">
     <input type="hidden" name="id_classe" value="'.$riga['id_classe'].'" />
     <input type="hidden" name="cancella" value="1" />
     <input type="image" src="./immagini/button_drop.png" alt="Cancellazione" title="Cancellazione" />
    </form>';}
		print "</td></tr>";
	}
	print "</table>";
}
$up="index";
include "Coda.inc";
?>
