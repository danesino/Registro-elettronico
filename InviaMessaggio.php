<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file: InviaMessaggio.php 
// Autore di questo file: Puria Nafisi
// Descrizione: form per la modifica dei relativi ad un gruppo di lavoro
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore
// ----------------------------------------------------------------------

$title = "Invia Messaggio";

include "Testa.inc";
require "FunzioniDB.inc";
require "HTML/QuickForm.php";
autorizza_ruoli(ID_OPERATORE,ID_AFFIDATARIO,ID_OSSERVATORE,ID_OSPEDALIERO,ID_ADMIN,ID_DOMICILIARE);

echo "<h3>Invio Messaggi</h3>";

$form = new HTML_QuickForm('form');
$form->addElement('text', 'oggetto', 'Oggetto:', array('size' => 50, 'maxlength' => 255,'class' => 'obb' ));
$form->addElement('textarea', 'corpo', 'Testo Messaggio', array( 'class' => 'obb', 'cols' => 48, 'rows' => 6));
$form->setRequiredNote ('<em style="font-size:80%; color:#ff0000;">*</em><em style="font-size:80%;"> Campo obbligatorio</em>');
$form->setJsWarnings('I dati immessi  non sono validi:','Cortesemente correggere i suddetti campi.');
$select =& $form->addElement('select', 'id_utente_dest', 'Utente Destinatario');
$select->setMultiple(true);

// if ($RUOLO == 1 )
$select->loadQuery($link, "SELECT id_utente,concat(nome,' ',cognome) as utenti from Utenti order by cognome", 'utenti','id_utente');
// else
// $select->loadQuery($link, "SELECT id_utente,concat(nome,' ',cognome) as utenti from Utenti where id_utente in (select id_utente from CdC where id_classe in (select distinct id_classe from CdC where id_utente=$CODICE_UTENTE)) order by cognome", 'utenti','id_utente');

$form->addElement('hidden', 'id_utente_mitt', $CODICE_UTENTE);
$form->addElement('hidden', 'nuovo', '1');
$form->addElement('submit', null, 'Invia');
$form->addRule('id_utente_dest', 'Il campo del Destinatario e\' obbligatorio', 'required', '', 'id_utente_dest');
$form->addRule('oggetto', 'Il campo dell\' Oggetto e\' obbligatorio', 'required', '', 'oggetto');
$form->addRule('corpo', 'Il campo del corpo del Messaggio e\' obbligatorio', 'required', '', 'corpo');
if ($form->validate()){
	$form->process('send', false);
}
// Output the form
$form->display();

function send($a){
	global $link;
	//$b = array_slice($a, 0, 3); 
	$b['oggetto']=$a['oggetto'];
	$b['corpo']= $a['corpo'];
	$b['nuovo']= $a['nuovo'];
	$b['id_utente_mitt']= $a['id_utente_mitt'];
	$num_dst= count($a['id_utente_dest']);
	for ($i=0;$i<$num_dst;$i++) {
		$b['id_utente_dest']=$a['id_utente_dest'][$i];
		$res = $link->autoExecute('Messaggi', $b, DB_AUTOQUERY_INSERT);
		if (PEAR::isError($res))
			box_errore("Errore Invio Messaggio".$res->getMessage());
	}
	box_successo("Messaggio inviato correttamente!<br/>Per mandare un nuovo messaggio clicca <a href='./InviaMessaggio.php'>qui</a>");

}

if (($RUOLO==ID_ADMIN)||($RUOLO==ID_OPERATORE))
			$up="indice.php";
else if ($profile==ID_OSPEDALIERO)
			$up="indice_doc.php";
		else
			$up="indice_aff.php";
if(isset($up)) echo "<br><br><center><a href=\"$up\"><img src=\"immagini/menu.png\"></a><br>Pagina Iniziale</center>";
include "Coda.inc";
?>
