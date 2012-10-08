<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file: FormModificaScuole.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: Modulo per la modifica delle informazioni relative ad una
// scuola
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore
// ----------------------------------------------------------------------
	
include "Testa.inc";
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN,ID_OPERATORE);

$title = "Modifica Scuole";

if (isset($_POST['nome']) && isset($_POST['codice_scuola']))
{
 if (empty($_POST['nome']))
    {
      echo ("<dl><dd>Errore: Campo nome obbligatorio</dd></dl>");
    }
    else
    {
    $update = array('codice' => $_POST['codice_scuola'], 'nome' => $_POST['nome'],
        'telefono' => $_POST['telefono'], 'indirizzo' => $_POST['indirizzo'], 'fax' => $_POST['fax'],
        'email' => $_POST['email'], 'denominazione' => $_POST['denominazione'], 'sitoweb' => $_POST['sitoweb'],
        'cap' => $_POST['cap'], 'citta' => $_POST['citta'], 'provincia' => $_POST['provincia'] );
    $res = $link->autoExecute('Scuole', $update, DB_AUTOQUERY_UPDATE, 'id_scuola ='.$_POST['id_scuola']);

    if (PEAR::isError($res)) {die($res->getMessage());}
    else exit("<dl><dt>Modifica della scuola <b>{$_POST['nome']}</b> eseguita con successo<br /><br />Clicca <b><a href=\"./VisualizzaScuole.php\">qui</a></b> per modificare altre scuole</dt></dl>"); 
    }
}
else
{

$sql = 'SELECT * FROM Scuole WHERE id_scuola ='. $link->quoteSmart($_POST['id_scuola']);
$res = $link->query($sql);
$riga = $res->fetchRow(DB_FETCHMODE_ASSOC);
require_once 'HTML/QuickForm.php';
echo "<h3>Modifica dati scuola</h3>";
$form = new HTML_QuickForm('firstForm');
$form->setRequiredNote ('<em style="font-size:80%; color:#ff0000;">*</em><em style="font-size:80%;"> Campo obbligatorio</em>');
$form->setJsWarnings('I dati immessi  non sono validi:','Cortesemente correggere i suddetti campi.');

$form->setDefaults(array('id_scuola' => $riga['id_scuola'], 'nome' => $riga['nome'], 'denominazione' => $riga['denominazione'], 'indirizzo' => $riga['indirizzo'], 'cap' => $riga['cap'], 'citta' => $riga['citta'], 'provincia' => $riga['provincia'], 'telefono' => $riga['telefono'], 'fax' => $riga['fax'], 'email' => $riga['email'], 'sitoweb' => $riga['sitoweb'], 'codice_scuola' => $_POST['codice_scuola']));

$form->addElement('text', 'codice_scuola', 'Codice Scuola:', array('size' => 50, 'maxlength' => 255, 'class' => 'obb'));
$form->addElement('text', 'nome', 'Nome:', array('size' => 50, 'maxlength' => 255, 'class' => 'obb'));
$form->addElement('text', 'denominazione', 'Denominazione:', array('size' => 50, 'maxlength' => 255));
$form->addElement('text', 'indirizzo', 'Indirizzo:', array('size' => 50, 'maxlength' => 255));
$form->addElement('text', 'cap', 'CAP :', array('size' => 5, 'maxlength' => 5));
$form->addElement('text', 'citta', 'Citt&agrave;:', array('size' => 30, 'maxlength' => 35));
$form->addElement('text', 'provincia', 'Provincia:', array('size' => 2, 'maxlength' => 2));
$form->addElement('text', 'telefono', 'Telefono:', array('size' => 20, 'maxlength' => 20));
$form->addElement('text', 'fax', 'Fax:', array('size' => 20, 'maxlength' => 20));
$form->addElement('text', 'email', 'Posta elettronica:', array('size' => 50, 'maxlength' => 255));
$form->addElement('text', 'sitoweb', 'Sito web:', array('size' => 50, 'maxlength' => 255));
$form->addElement('hidden', 'id_scuola');
$form->addElement('submit', null, 'Salva');
$form->addRule('nome', 'Il campo del nome e\' obbligatorio', 'required', '', 'client');
$form->addRule('codice_scuola', 'Il campo del codice scuola e\' obbligatorio', 'required', '', 'client');
$form->addRule('email', 'La email immessa non e\' corretta', 'email', '', 'client');
$form->addRule('cap', 'Il cap immesso non &egrave; corretto', 'rangelength', array(5, 5));
$form->addRule('cap', 'Il cap non e\' valido', 'numeric', '', 'client');

$form->display();

//$up="VisualizzaScuole";
}
include "Coda.inc"; 
?>
