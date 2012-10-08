<?php
	
// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// Please READ carefully the Docs/License.txt file for further details
// Please READ the Docs/credits.txt file for complete credits list
// ----------------------------------------------------------------------
// Nome file:  FormModificaUtente.php
// Autore di questo file: Puria Nafisi
// Descrizione: modulo per la modifica dei dati anagrafici un utente
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore
// ----------------------------------------------------------------------

$title = "Modifica Utente";

include "Testa.inc";
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN);
require_once 'HTML/QuickForm.php';
	

$res =& $link->query("SELECT * FROM Utenti WHERE id_utente={$_POST['id_utente']} --");
$riga = $res->fetchRow();

echo "<h3>Modifica Dati utente</h3>";

$form = new HTML_QuickForm('form');

if (isset($_POST['update'])){
  $form->process('modifica_utente', false);
}

$form->setDefaults(array('id_ruolo' => $riga['id_ruolo'], 'nome' => $riga['nome'], 'cellulare' => $riga['cellulare'], 'telefono' => $riga['telefono'], 'email' => $riga['email'], 'cognome' => $riga['cognome'], 'username' => $riga['username'], 'note' => $riga['note']));
$form->addElement('text', 'cognome', 'Cognome:', array('size' => 30, 'maxlength' => 30, 'alt' => 'Inserire il cognome del docente'));
$form->addElement('text', 'nome', 'Nome:', array('size' => 30, 'maxlength' => 30, 'class' => 'obb'));
$form->addElement('text', 'telefono', 'Telefono:', array('size' => 30, 'maxlength' => 30));
$form->addElement('text', 'cellulare', 'Cellulare :', array('size' => 30, 'maxlength' => 30));
$form->addElement('text', 'email', 'Posta elettronica:', array('size' => 50, 'maxlength' => 255));
$form->addElement('text', 'username', 'Username:', array('size' => 30, 'maxlength' => 35, 'class' => 'obb'));
$form->addElement('textarea','note','Note:',array('rows' => 3, 'cols' =>57));
$select =& $form->addElement('select', 'id_ruolo', 'Ruolo Utente');
$select->loadQuery($link, 'SELECT * from Ruoli', 'descrizione','id_ruolo');
$select->setSelected($riga['id_ruolo']);
$form->addElement('password', 'pwd', 'Password:');
$form->addElement('password', 'pwd_utente2', 'Reinserire la Password:');
$form->addElement('hidden', 'update');
$form->addElement('hidden', 'id_utente', $riga['id_utente']);
$form->addElement('submit', null, 'Modifica');
$form->addRule(array('pwd', 'pwd_utente2'), 'La password non coincide', 'compare', null, 'client');
$form->addRule('email', 'La email immessa non e\' corretta', 'email', '', 'client');
$form->display();

function modifica_utente ($a) {
  global $link;
  array_shift($a);
  $b=array_pop($a);
  if(!empty($a['pwd']))
  $a['pwd']=sha1($b);
  else array_pop($a);
  $res = $link->autoExecute('Utenti', $a, DB_AUTOQUERY_UPDATE, "id_utente='{$a['id_utente']}'");
   errore_DB($res);
  die("<dl><dt>Utente <b>{$a['nome']} {$a['cognome']}</b> modificato con successo<br /><br />Per modificare un altro utente cliccare <a href=\"./VisualizzaUtenti.php\">qui</a></dt></dl>");
}

//$up="VisualizzaUtenti";
include "Coda.inc";
?>
