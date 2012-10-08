<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  GestioneMaterie.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: consente visualizzazione delle materie già inserite, la 
// loro modifica e l'inserimento di nuove materie
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore 
// ----------------------------------------------------------------------

function incoda($ordine,$materia)
{
	 global $link;
	 $max=$link->getOne("SELECT MAX(ordine) FROM Materie");
	 while ($ordine<$max)
	 {
		  $sql =& $link->query('UPDATE Materie SET ordine = ?  WHERE ( ordine = ? )', array($ordine,$ordine+1));
		  errore_DB($sql);
		  $sql =& $link->query('UPDATE Materie SET ordine = ?  WHERE ( id_materia = ? )', array($ordine+1,$materia));
		  errore_DB($sql);
		  $ordine =+ 1;
	 }
}

$title = "Gestione materie di studio";

include "Testa.inc";
require_once 'HTML/QuickForm.php';
include "FunzioniDB.inc";	
autorizza_ruoli(ID_ADMIN);

echo"<h2>Gestione materie di studio</h2>";
     	
if (isset($_POST['inserisci']))
{
	 if (empty($_POST['nome']))
		  print "<dl><dd>Errore: Campo nome obbligatorio</dd></dl>";
	 else
	 {
		  $sql =& $link->query('INSERT INTO Materie (nome,ordine) select ?, (max(ordine)+1) from Materie', $_POST['nome']);
		  errore_DB($sql);
		  print "<dl><dt> Materia <b>{$_POST['nome']}</b> inserita con successo </dt></dl>";
	 }
}

if (isset($_POST['modifica']))
{
	 $sql =& $link->query('UPDATE Materie SET nome= ? WHERE id_materia= ?', array($_POST['nome'],$_POST['id_materia']));
	 errore_DB($sql);
	 print "<dl><dt> Materia <b>{$_POST['nome']}</b> modificata con successo </dt></dl>";
}
	
if (isset($_POST['cancella']))
{
	 print "
		  <script type=\"text/javascript\">
function validazione(){
	 if (confirm(\"Sei proprio sicuro di voler cancellare la materia?\"))
	 {
		  return true;
}
else
{
	 return false;
}
}</script>";


$res =& $link->query("SELECT * FROM  CdC WHERE (id_materia= ? )",$_POST['id_materia']);
$num_righe = $res->numRows();
if ( $num_righe != 0)
{
	 die("<dl><dd>Registro - Impossibile cancellare una materia gi&agrave; associata ad un docente</dd></dl>");
}
else
{
	 $ordine = $link->getOne("SELECT ordine FROM Materie WHERE (id_materia = ? )", $_POST['id_materia']);
	 $res =& $link->query("DELETE FROM Materie WHERE (id_materia= ? )",$_POST['id_materia']);
   errore_DB($res);
	 $res =& $link->query("update Materie set ordine = ordine-1 where ordine > $ordine");
   errore_DB($res);
	}
echo "<dl><dt>Materia cancellata con successo</dt></dl>";
}

/* 
if(isset($_POST['sali']) || isset($_POST['scendi']))
{
	 @$x = ($_POST['sali']) ? $_POST['ordine']-1 : $_POST['ordine']+1;
	 $sql =& $link->query('UPDATE Materie SET ordine = ?  WHERE ( ordine = ? )', array($_POST['ordine'],$x));
	 errore_DB($sql);
	 $sql =& $link->query('UPDATE Materie SET ordine = ?  WHERE ( id_materia = ? )', array($x,$_POST['id_materia']));
	 errore_DB($sql);
}*/
if(isset($_POST['scendi'])){
	$res =& $link->query('UPDATE Materie set ordine = ? where id_materia = ?', array($_POST['ordine']+1,$_POST['id_materia']));
  errore_DB($res);
	$res =& $link->query('UPDATE Materie set ordine = ordine-1 where ordine = ? and id_materia <> ?', array($_POST['ordine']+1,$_POST['id_materia']));
  errore_DB($res);
}

if(isset($_POST['sali'])){
	$res =& $link->query('UPDATE Materie set ordine = ? where id_materia = ?', array($_POST['ordine']-1,$_POST['id_materia']));
  errore_DB($res);
	$res =& $link->query('UPDATE Materie set ordine = ordine+1 where ordine = ? and id_materia <> ?', array($_POST['ordine']-1,$_POST['id_materia']));
  errore_DB($res);
}
	 
if (isset($_POST['edit']))
{

	 $nome = $link->getOne("SELECT nome FROM Materie WHERE (id_materia = ? )", $_POST['id_materia']);
	 print "<h3>Modifca materia $nome</h3>";
	 
	 $form = new HTML_QuickForm('form');
	 $form->setRequiredNote ('<em style="font-size:80%; color:#ff0000;">*</em><em style="font-size:80%;"> Campo obbligatorio</em>');
	 $form->setJsWarnings('I dati immessi  non sono validi:','Cortesemente correggere i suddetti campi.');
	 $form->setDefaults(array('nome' => $nome));
	 $form->addElement('text', 'nome', 'Nome:', array('size' => 30, 'maxlength' => 255, 'alt' => 'Nome reparto','class' => 'obb'));
	 $form->addElement('hidden', 'modifica', '1');
	 $form->addElement('hidden', 'id_materia', $_POST['id_materia']);
	 $buttons[] = &HTML_QuickForm::createElement('submit', null, 'Modifica');
	 $buttons[] = &HTML_QuickForm::createElement('reset', null, 'Annulla');
	 $form->addGroup($buttons, null, null, '&nbsp;');
	 $form->addRule('nome', 'Il campo del nome e\' obbligatorio', 'required', '', 'client');
	 $form->display();

}

print "<h3>Inserimento nuova materia</h3>";
$sql =& $link->query("SELECT * FROM Materie ORDER BY ordine");
$num_righe = $sql->numRows();
$next_riga=$num_righe+1;

$form2 = new HTML_QuickForm('form_inserisci');
$form2->setRequiredNote ('<em style="font-size:80%; color:#ff0000;">*</em><em style="font-size:80%;"> Campo obbligatorio</em>');
$form2->setJsWarnings('I dati immessi  non sono validi:','Cortesemente correggere i suddetti campi.');
$form2->addElement('text', 'nome', 'Nome materia:', array('size' => 30, 'maxlength' => 255, 'class' => 'obb'));
$form2->addElement('hidden', 'inserisci', '1');
$form2->addElement('hidden', 'ordine', $next_riga);
$form2->addRule('nome', 'Il campo del nome e\' obbligatorio', 'required', '', 'client');
$form2->addElement('submit', null, 'Aggiungi');
$form2->display();

if ($num_righe)
{
	 print "<h3>Elenco materie ordinate come nel prospetto scolastico</h3>";
	 print "<table class=\"elenco\">\n<tr>";
	 print "<th>Nome</th>\n";
	 print '<th colspan="4">Azione </th>';
	 $pari=1;
	 while ($riga =& $sql->fetchRow())
	 {
		  $class = ($pari) ? "pari" : "dispari";
		  $pari=1-$pari;
		  print "<tr class='$class'>";
		  print '<td nowrap="nowrap">'.$riga['nome']."</td>\n";
		  print '<td nowrap="nowrap">';
		  print "<form method=\"post\" action=\"{$_SERVER['PHP_SELF']}\">".
				"<input type=\"hidden\" name=\"id_materia\" value=\"{$riga['id_materia']}\" />".
				"<input type=\"hidden\" name=\"edit\" value=\"1\" />".
				"<input type=\"image\" src=\"./immagini/button_edit.png\" alt=\"Modifica\" title=\"Modifica\"/></form></td>";		  
		  print '<td nowrap="nowrap">';
		  print "<form method=\"post\" action=\"{$_SERVER['PHP_SELF']}\">".
				"<input type=\"hidden\" name=\"id_materia\" value=\"{$riga['id_materia']}\" />".
				"<input type=\"hidden\" name=\"ordine\" value=\"{$riga['ordine']}\" />".
				"<input type=\"hidden\" name=\"cancella\" value=\"1\" />".
				"<input type=\"image\" src=\"./immagini/button_drop.png\" alt=\"Cancella\" title=\"Cancella\"/></form></td>";
		  print '<td nowrap="nowrap">';
		  $min= $link->getOne("SELECT MIN(ordine) FROM Materie");
		  if ($riga['ordine']!=$min)
		  {
				print "<form method=\"post\" action=\"{$_SERVER['PHP_SELF']}\">".
					 "<input type=\"hidden\" name=\"id_materia\" value=\"{$riga['id_materia']}\" />".
					 "<input type=\"hidden\" name=\"sali\" value=\"1\" />".
					 "<input type=\"hidden\" name=\"ordine\" value=\"{$riga['ordine']}\" />".
					 "<input type=\"image\" src=\"./immagini/up.gif\" alt=\"Sali di Posizione\" title=\"Sali di una posizione\"/></form>";
		  }
		  print "</td>";
		  print '<td nowrap="nowrap">';
		  $max= $link->getOne("SELECT MAX(ordine) FROM Materie");
		  if ($riga['ordine']!=$max)
		  {
				print "<form method=\"post\" action=\"{$_SERVER['PHP_SELF']}\">".
					 "<input type=\"hidden\" name=\"id_materia\" value=\"{$riga['id_materia']}\" />".
					 "<input type=\"hidden\" name=\"scendi\" value=\"1\" />".
					 "<input type=\"hidden\" name=\"ordine\" value=\"{$riga['ordine']}\" />".
					 "<input type=\"image\" src=\"./immagini/down.gif\" alt=\"Scendi\" title=\"Scendi di posizione\"/></form>";
		  }
		  print "</td>";
	 }
	 print "</table>";
}

$up="index";
include "Coda.inc";
?>
