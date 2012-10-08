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
// Nome file:  FormInserimentoUtente.php
// Autore di questo file: Puria Nafisi
// Descrizione: form per l'inserimento di un nuovo utente
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore
// ----------------------------------------------------------------------
	
$title = "Inserimento Utente";

include "Testa.inc";
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN);
require_once 'HTML/QuickForm.php';

echo "<h3>Inserimento nuovo utente</h3>";

$form = new HTML_QuickForm('form');

$form->setRequiredNote ('<em style="font-size:80%; color:#ff0000;">*</em><em style="font-size:80%;"> Campo obbligatorio</em>');
$form->setJsWarnings('I dati immessi  non sono validi:','Cortesemente correggere i suddetti campi.');
$form->addElement('text', 'cognome', 'Cognome:', array('size' => 30, 'class' => 'obb', 'maxlength' => 30, 'alt' => 'Inserire il cognome del docente'));
$form->addElement('text', 'nome', 'Nome:', array('size' => 30, 'maxlength' => 30));
$form->addElement('text', 'telefono', 'Telefono:', array('size' => 30, 'maxlength' => 30));
$form->addElement('text', 'cellulare', 'Cellulare :', array('size' => 30, 'maxlength' => 30));
$form->addElement('text', 'email', 'Posta elettronica:', array('size' => 50, 'maxlength' => 255));
$form->addElement('text', 'username', 'Username:', array('size' => 10, 'maxlength' => 10, 'class' => 'obb'));
$form->addElement('textarea','note','Note:',array('rows' => 3, 'cols' =>57));
$select =& $form->addElement('select', 'id_ruolo', 'Ruolo Utente');
$select->loadQuery($link, 'SELECT * from Ruoli', 'descrizione','id_ruolo');
$form->addElement('password', 'pwd', 'Password:', array('size' => 30, 'maxlength' => 30, 'class' => 'obb'));
$form->addElement('password', 'pwd_utente2', 'Reinserire la Password:', array('size' => 30, 'maxlength' => 30, 'class' => 'obb'));
$form->addElement('submit', null, 'Salva');

$form->addRule('cognome', 'Il campo del Cognome e\' obbligatorio', 'required', '', 'client');
$form->addRule('username', 'Il campo della username e\' obbligatoria', 'required', '', 'client');
$form->addRule('pwd', 'Il campo della password e\' obbligatoria', 'required', '', 'client');
$form->addRule('pwd_utente2', 'Il campo della password secondaria e\' obbligatoria', 'required', '', 'client');
$form->addRule(array('pwd', 'pwd_utente2'), 'La password non coincide', 'compare', null, 'client');
$form->addRule('email', 'La email immessa non e\' corretta', 'email', '', 'client');

if ($form->validate()){
$form->process('inserisci_utente', false);
}
$form->display();

function inserisci_utente($a){
	 global $link;
	 $a['nome']=htmlentities($a['nome'],ENT_QUOTES);
	 $a['cognome']=htmlentities($a['cognome'],ENT_QUOTES);
if (in_DB('Utenti', 'nome', $a['nome']) && (in_DB('Utenti', 'cognome', $a['cognome'])))
  {
    echo "<dl><dd>Utente <b>{$a['nome']} {$a['cognome']}</b> gi&agrave; presente</dd></dl>";
  }
elseif (in_DB('Utenti', 'username', $a['username']))
	{
    echo "<dl><dd>Username <b>{$a['username']}</b> gi&agrave; presente</dd></dl>";
  }
  else
  {
	$b=array_pop($a);
   $a['pwd']=sha1($b);
   $res = $link->autoExecute('Utenti', $a, DB_AUTOQUERY_INSERT);
   errore_DB($res);
   die("<dl><dt>Utente <b>{$a['username']}</b> inserito con successo<br /><br />Per inserire un altro utente cliccare <a href=\"./InserimentoUtente.php\">qui</a></dt></dl>");
  }

}
$up="VisualizzaUtenti";
include "Coda.inc";
?>
