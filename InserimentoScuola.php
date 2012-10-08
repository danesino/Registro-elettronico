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
// Nome file:  FormInserimentoScuola.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: modulo per l'inserimento di una nuova scuola
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore
// ----------------------------------------------------------------------
// Modifiche: Puria Nafisi
// usati i pear per il db
// sato il pear HTML/QuickForm per la generazione del form
// output dell'esito dell'azione nella stessa pagina
// xhtml valido
// menu grafico
	
$title = "Inserimento Scuola"; 

include "Testa.inc";
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN,ID_OPERATORE);
require_once 'HTML/QuickForm.php';

if (isset($_POST['nome']))
{
 if (empty($_POST['nome']))
    {
      echo ("<dl><dd>Errore: Campo nome obbligatorio</dd></dl>");
      print('<div align="center"><a href="FormInserimentoScuola.html" ><img src="immagini/undo.png" title="Indietro" border="0"></a></div>');
    }
    else
	 {
		  $codice_scuola = strtoupper($_POST['codice_scuola']);
		  $nome = ucwords(strtolower($_POST['nome']));
		  $indirizzo = ucwords(strtolower($_POST['indirizzo']));
		  $sitoweb = strtolower($_POST['sitoweb']);
		  $citta = ucwords(strtolower($_POST['citta']));
		  $provincia = strtoupper($_POST['provincia']);


    $insert = array('id_scuola' => 'NULL', 'codice' =>  $codice_scuola, 'nome' => $nome,
        'telefono' => $_POST['telefono'], 'indirizzo' => $indirizzo, 'fax' => $_POST['fax'],
        'email' => $_POST['email'], 'denominazione' => $_POST['denominazione'], 'sitoweb' => $sitoweb,
		  'cap' => $_POST['cap'], 'citta' => $citta, 'provincia' => $provincia );
		  foreach ($insert as $v) {
		  $v = htmlentities($v,ENT_QUOTES);
		  }
		  inserisci_scuola($insert);
	 }
}
echo "<br/>";
$form = new HTML_QuickForm('form');
$form->setRequiredNote ('<em style="font-size:80%; color:#ff0000;">*</em><em style="font-size:80%;"> Campo obbligatorio</em>');
$form->setJsWarnings('I dati immessi  non sono validi:','Cortesemente correggere i suddetti campi.');
$form->addElement('text', 'codice_scuola', 'Codice Scuola:', array('size' => 50, 'maxlength' => 255,'class' => 'obb' ));
$form->addElement('text', 'nome', 'Nome:', array('size' => 50, 'maxlength' => 255, 'class' => 'obb'));
$form->addElement('text', 'indirizzo', 'Indirizzo:', array('size' => 50, 'maxlength' => 255));
$form->addElement('text', 'denominazione', 'Denominazione:', array('size' => 50, 'maxlength' => 255));
$form->addElement('text', 'cap', 'CAP :', array('size' => 5, 'maxlength' => 5));
$form->addElement('text', 'citta', 'Citt&agrave;:', array('size' => 30, 'maxlength' => 35));
$form->addElement('text', 'provincia', 'Provincia:', array('size' => 2, 'maxlength' => 2));
$form->addElement('text', 'telefono', 'Telefono:', array('size' => 20, 'maxlength' => 20));
$form->addElement('text', 'fax', 'Fax:', array('size' => 20, 'maxlength' => 20));
$form->addElement('text', 'email', 'Posta elettronica:', array('size' => 50, 'maxlength' => 255));
$form->addElement('text', 'sitoweb', 'Sito web:', array('size' => 50, 'maxlength' => 255));
$form->addElement('submit', null, 'Salva');

$form->addRule('nome', 'Il campo del nome e\' obbligatorio', 'required', '', 'client');
$form->addRule('codice_scuola', 'Il campo del codice e\' obbligatorio', 'required', '', 'client');
$form->addRule('email', 'La email immessa non e\' corretta', 'email', '', 'client');
$form->addRule('cap', 'Il cap e\' invalido', 'numeric', '', 'client');


// Output the form
$form->display();
include "Coda.inc";
?>
