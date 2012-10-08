<?php
 
// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  VisualizzaMessaggi.php
$title = "Messaggi";
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: visualizza tutti i nuovi messaggi
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore, operatore
// ----------------------------------------------------------------------

include("Testa.inc");
include("FunzioniDB.inc");
autorizza_ruoli(ID_ADMIN,ID_OPERATORE,ID_AFFIDATARIO,ID_OSPEDALIERO,ID_OSSERVATORE);
if(isset($_POST['cancella'])){
	  $res =& $link->query("DELETE FROM Messaggi WHERE ( id = ? )",$_POST['id']);
		errore_DB($res);
		echo "<dl><dt>Messaggio cancellato con successo</dt></dl>";
}
	
	if($_GET['a'])
		$nuovo = 0;
	else
		$nuovo = 1;
	
	$sql = $link->query("SELECT * FROM Messaggi WHERE ( id_utente_dest = ? and nuovo = ? )",array($CODICE_UTENTE,$nuovo));
	$num_righe = $sql->numRows();

	if (!$num_righe):
		$p=array('vecchi', 'nuovi');
		echo "<dl><dd>Non ci sono {$p[$nuovo]} messaggi per lei <br/>";
		if(!$p) echo "per visualizzare quelli vecchi clicchi <a href='{$_SERVER['PHP_SELF']}?a=1'>qui</a>.";
		else echo "Per tornare alla pagina iniziale cliccare <a href='Login.php'>qui</a>.";
		echo "</dd></dl>";
	else:
		print "<h3>";
		echo ($num_righe>1) ? "Ci sono $num_righe messaggi</h3>" : "C'&egrave; 1 messaggio</h3>";
?>
<?php
 $pari=1;
 while ($riga =& $sql->fetchRow(DB_FETCHMODE_ASSOC)):
  $class = ($pari) ? "pari" : "dispari";
  $pari = 1-$pari;
	$mitt = $link->getOne("SELECT concat(nome,' ',cognome) as utenti from Utenti where (id_utente = ? )", $riga['id_utente_mitt']);
?>
 <table class="elenco" >
  <tr><th colspan="2">OGGETTO: <b><?=$riga['oggetto']?></b></th></tr>
  <tr class='<?=$class?>'>	
  <td nowrap='nowrap'>Da: <strong><?=$mitt?></strong></td><td>
	<?
		echo "<form method=\"post\" action=\"{$_SERVER['PHP_SELF']}\">".
		"<input type=\"hidden\" name=\"id\" value=\"{$riga['id']}\" />".
		"<input type=\"hidden\" name=\"cancella\" value=\"1\" />".
		"<input type=\"image\" src=\"./immagini/button_drop.png\" alt=\"Cancella\" title=\"Cancella\"/></form>";
	?>	
	</td></tr>
  <tr><td><?=$riga['corpo']?></td></tr></table>
	<table><tr><td>
	<? 
  endwhile;
  print "\t</table>\n\n";
 endif;
$res =& $link->query("UPDATE Messaggi SET nuovo='0' WHERE ( id_utente_dest = ? )", $CODICE_UTENTE);
errore_DB($res);

if (($RUOLO==ID_ADMIN)||($RUOLO==ID_OPERATORE))
			$up="indice.php";
else if ($profile==ID_OSPEDALIERO)
			$up="indice_doc.php";
		else
			$up="indice_aff.php";
if(isset($up)) echo "<br><br><center><a href=\"$up\"><img src=\"immagini/menu.png\"></a><br>Pagina Iniziale</center>";
include "Coda.inc";
?>
