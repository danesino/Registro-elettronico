<?php
	
// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  FormModificaClasse.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: form per la modifica dei relativi ad un gruppo di lavoro
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore
// ----------------------------------------------------------------------

$title = "Modifica Gruppo di Lavoro";

include "Testa.inc";
require "FunzioniDB.inc";	
require "HTML/QuickForm.php";
autorizza_ruoli(ID_ADMIN);

if(isset($_POST['modifica']))
{
	$sql =& $link->query("UPDATE  Classi  SET classe= ? , ordine= ? WHERE (id_classe= ? )", array($_POST['classe'], $_POST['ordine'], $_POST['id_classe']));
	errore_DB($sql);
  die("<dl><dt>Modifica gruppo di lavoro <b>{$_POST['classe']}</b> effettuata con successo<br /><br />Per modificare un altra classe cliccare <a href=\"./VisualizzaClassi.php\">qui</a> </dt></dl>");
}

$res =& $link->query("SELECT * FROM Classi WHERE (id_classe= ? )", $_POST['id_classe']);
errore_DB($res);
$riga =& $res->fetchRow();

print "<h3>Modifica dati gruppo di lavoro</h3>";

$form = new HTML_QuickForm('form');
$form->setDefaults(array('ordine' => $riga['ordine'], 'classe' => $riga['classe']));
$form->addElement('text', 'classe', 'Nome gruppo di lavoro');
$lista=array('i'=>'Scuola dell\'infanzia', '1'=>'Scuola primaria', '2'=>'Scuola secondaria di primo grado', 's'=>'Scuola secondaria');
$select =& $form->addElement('select', 'ordine', 'Ordine di scuola', $lista);
$select->setSelected($riga['ordine']);
$form->addElement('hidden', 'id_classe', $riga['id_classe']);
$form->addElement('submit', 'modifica', 'Modifica');
$form->display();
$up="VisualizzaClassi";
include "Coda.inc";
?>
