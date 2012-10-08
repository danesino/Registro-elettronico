<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file: FormModificaStudente.php
// Autore di questo file: Puria Nafisi
// Descrizione: modulo per la modfica dei dati anagrafici relativi ad uno 
// studente
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore
// ----------------------------------------------------------------------

$title = "Modifica Studente";
	
include "Testa.inc";
require "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN,ID_OPERATORE);
require "HTML/QuickForm.php";


$res =& $link->query("SELECT * FROM Studenti WHERE id_studente={$_POST['id']}");
errore_DB($res);
$riga = $res->fetchRow();

$form = new HTML_QuickForm('form');

if (isset($_POST['update'])){
  $form->process('modifica_studente', false);
}
list($year, $month, $day) = split('[-.-]', $riga['n_data']);
$data=array("d"=>$day,"M"=>$month,"Y"=>$year);

$form->setRequiredNote ('<em style="font-size:80%; color:#ff0000;">*</em><em style="font-size:80%;"> Campo obbligatorio</em>');
$form->setJsWarnings('I dati immessi  non sono validi:','Cortesemente correggere i suddetti campi.');	
$form->setDefaults(array('nome' => $riga['nome'], 'cognome' => $riga['cognome'], 'CF' => $riga['CF'], 'n_citta' => $riga['n_citta'], 
'n_provincia' => $riga['n_provincia'], 'n_stato' => $riga['n_stato'], 'r_citta' => $riga['r_citta'], 'r_provincia' => $riga['r_provincia'], 
'r_stato' => $riga['r_stato'], 'r_via' => $riga['r_via'], 'r_numero' => $riga['r_numero'], 'r_cap' => $riga['r_cap'], 
'cittadinanza' => $riga['cittadinanza'], 'r_telefono' => $riga['r_telefono'], 'r_cellulare' => $riga['r_cellulare'], 
'email' => $riga['email'], 'note' => $riga['note'], 'classe' => $riga['classe'],'HC' => $riga['HC'], 'straniero' => $riga['straniero'], 
'ripetente' => $riga['ripetente'],'RC' => $riga['RC'],'ordine'=>$riga['ordine'],'esame'=>$riga['esame'],
'lingua1' => $riga['lingua1'],'lingua2' => $riga['lingua2'],'lingua3' => $riga['lingua3'],'sesso' => $riga['sesso'], 'n_data' => $data));

echo "<h3>Modifica dati studente</h3>";
$form->addElement('text', 'cognome', 'Cognome:', array('size' => 30, 'maxlength' => 255, 'class' => 'obb'));
$form->addElement('text', 'nome', 'Nome:', array('size' => 30, 'maxlength' => 255, 'class' => 'obb'));
$radio[] = &HTML_QuickForm::createElement('radio', 'sesso', '', 'M', 'M', '');
$radio[] = &HTML_QuickForm::createElement('radio', 'sesso', '', 'F', 'F', '');
$form->addGroup($radio, "sesso", "Sesso:", ' ' , false);
$form->addElement('text', 'CF', 'Codice Fiscale:', array('size' => 16, 'maxlength' => 16));
$emptyText = array ('d'=>'--', 'M'=>'--', 'Y'=>'----');
$emptyValue = array ('d'=>'00', 'M'=>'00', 'Y'=>'0000');
$options = array('language' => 'it', 'format' => 'dMY', 'minYear' => 1970, 'maxYear' => 2015 , 'emptyOptionText'=>$emptyText, 'emptyOptionValue'=>0, 'addEmptyOption'=>true);
$form->addElement('date', 'n_data', 'Data di nascita:', $options, false);
$n_label = array(' Provincia: ',' Stato: ');
$nascita[] = &HTML_QuickForm::createElement('text', 'n_citta', 'Citta:', array('size' => 30, 'maxlength' => 30));
$nascita[] = &HTML_QuickForm::createElement('text', 'n_provincia', 'Provincia:', array('size' => 2, 'maxlength' => 2));
$nascita[] = &HTML_QuickForm::createElement('text', 'n_stato', 'Stato:', array('size' => 30, 'maxlength' => 30));
$form->addGroup($nascita, null, "Luogo di nascita:", $n_label);
$r_label = array(' Provincia: ',' Stato: ');
$r1_label = array(' Numero: ',' CAP: ');
$residenza[] = &HTML_QuickForm::createElement('text', 'r_citta', 'Citta:', array('size' => 30, 'maxlength' => 30));
$residenza[] = &HTML_QuickForm::createElement('text', 'r_provincia', 'Provincia:', array('size' => 2, 'maxlength' => 2));
$residenza[] = &HTML_QuickForm::createElement('text', 'r_stato', 'Stato:', array('size' => 30, 'maxlength' => 30));
$form->addGroup($residenza, null, "Residenza:", $r_label);
$residenza2[] = &HTML_QuickForm::createElement('text', 'r_via', 'Via:', array('size' => 30, 'maxlength' => 30));
$residenza2[] = &HTML_QuickForm::createElement('text', 'r_numero', 'Numero:', array('size' => 5, 'maxlength' => 5));
$residenza2[] = &HTML_QuickForm::createElement('text', 'r_cap', 'CAP:', array('size' => 5, 'maxlength' => 5));
$form->addGroup($residenza2, null, " ", $r1_label);
$radio_straniero[] = &HTML_QuickForm::createElement('radio', 'straniero', '', 'SI', '1', '');
$radio_straniero[] = &HTML_QuickForm::createElement('radio', 'straniero', '', 'NO', '0', '');
$form->addGroup($radio_straniero, "straniero", "Straniero:", ' ' , false);
$form->addElement('text', 'cittadinanza', 'Cittadinanza:', array('size' => 20, 'maxlength' => 20));
$form->addElement('text', 'r_telefono', 'Telefono:', array('size' => 20, 'maxlength' => 20));
$form->addElement('text', 'r_cellulare', 'Cellulare:', array('size' => 20, 'maxlength' => 20));
$form->addElement('text', 'email', 'Posta elettronica:', array('size' => 50, 'maxlength' => 255));
$radio_HC[] = &HTML_QuickForm::createElement('radio', 'HC', '', 'SI', '1', '');
$radio_HC[] = &HTML_QuickForm::createElement('radio', 'HC', '', 'NO', '0', '');
$form->addGroup($radio_HC, "HC", "HC:", ' ' , false);

//Scuola di appartenenza, genera il select e seleziona il valore dello studente
$select =& $form->addElement('select', 'id_scuola_appartenenza', 'Scuola di appartenenza');
$select->loadQuery($link, "SELECT CONCAT_WS(' - ',nome,citta) as nome_citta, id_scuola from Scuole ORDER BY nome", 'nome_citta', 'id_scuola');
$res =& $link->query("SELECT * FROM Scuola WHERE tipo='p' AND id_studente={$_POST['id']}");
errore_DB($res);
$sel = $res->fetchRow();
$select->setSelected($sel['id_scuola']);

//Scuola affidataria, genera il select e seleziona il valore dello studente
$select =& $form->addElement('select', 'id_scuola_affidataria', 'Scuola affidataria');
$select->loadQuery($link, "SELECT CONCAT_WS(' - ',nome,citta) as nome_citta, id_scuola from Scuole ORDER BY nome", 'nome_citta','id_scuola');
$select->addOption("Nessuna", "0");
$res =& $link->query("SELECT * FROM Scuola WHERE tipo='f' AND id_studente={$_POST['id']}");
errore_DB($res);
$sel = $res->fetchRow(); 
$select->setSelected($sel['id_scuola']);

//Classe
$classe = $form->addElement('select', 'classe', 'Classe:', array(
  '1' => '1',
  '2' => '2',
  '3' => '3',
  '4' => '4',
  '5' => '5'
));
$ordine = $form->addElement('select', 'ordine', 'Ordine di scuola:', array(
  'M' => 'Materna',
  'P' => 'Primaria',
  'I' => 'Secondaria di primo grado',
  'S' => 'Secondaria di secondo grado'
));

// Esame di stato
$radio_esame[] = &HTML_QuickForm::createElement('radio', 'esame', '', 'SI', '1', '');
$radio_esame[] = &HTML_QuickForm::createElement('radio', 'esame', '', 'NO', '0', '');
$form->addGroup($radio_esame, "esame", "Esame di stato:", ' ' , false);

// Ripetente
$radio_R[] = &HTML_QuickForm::createElement('radio', 'ripetente', '', 'SI', '1', '');
$radio_R[] = &HTML_QuickForm::createElement('radio', 'ripetente', '', 'NO', '0', '');
$form->addGroup($radio_R, "ripetente", "Ripetente:", ' ' , false);

// Lingue straniere
$form->addElement('text', 'lingua1', 'Prima lingua straniera:', array('size' => 20, 'maxlength' => 20));
$form->addElement('text', 'lingua2', 'Seconda lingua straniera:', array('size' => 20, 'maxlength' => 20));
$form->addElement('text', 'lingua3', 'Terza lingua straniera:', array('size' => 20, 'maxlength' => 20));

// Reglione cattolica/Alternativa
$radio_RC[] = &HTML_QuickForm::createElement('radio', 'RC', '', 'SI', '1', '');
$radio_RC[] = &HTML_QuickForm::createElement('radio', 'RC', '', 'NO', '0', '');
$form->addGroup($radio_RC, "RC", "Studio Religione cattolica:", ' ' , false);

// Note
$form->addElement('textarea','note','Note:',array('rows' => 10, 'cols' =>90));


$form->addElement('hidden', 'id', $riga['id_studente']);
$form->addElement('hidden', 'update');

$buttons[] = &HTML_QuickForm::createElement('submit', 'btnSubmit', 'Modifica');
$buttons[] = &HTML_QuickForm::createElement('reset', 'btnClear', 'Annulla');
$form->addGroup($buttons, null, null, '&nbsp;');
$form->addRule('CF', "Il CF non &egrave; di 16 caratteri", 'rangelength', array(14, 16));
//$form->addRule('CF', 'Il campo del codice fiscale e\' obbligatorio', 'required', '', 'client');
$form->addRule('cognome', 'Il campo del cognome e\' obbligatorio', 'required', '', 'client');
$form->addRule('nome', 'Il campo del nome e\' obbligatorio', 'required', '', 'client');
$form->addRule('codice_scuola', 'Il campo del codice e\' obbligatorio', 'required', '', 'client');
$form->addRule('email', 'La email immessa non e\' corretta', 'email', '', 'client');
$form->addRule('n_cap', 'Il cap immesso non &egrave; corretto', 'rangelength', array(5, 5));
$form->addRule('n_cap', 'Il cap e\' invalido', 'numeric', '', 'client');
$form->addRule('r_cap', 'Il cap immesso non &egrave; corretto', 'rangelength', array(5, 5));
$form->addRule('r_cap', 'Il cap e\' invalido', 'numeric', '', 'client');


$form->display();


function modifica_studente ($a) {
  global $link;
  
    $b = array_slice($a, 2, 19);
    $b['n_data'] = $a['n_data']['Y']."-".$a['n_data']['M']."-".$a['n_data']['d'];
	 $b['classe'] = $a['classe']; $b['HC'] = $a['HC']; $b['note'] = $a['note']; 
	 $b['ripetente'] = $a['ripetente']; $b['straniero'] = $a['straniero'];
	 $b['ordine'] = $a['ordine']; $b['esame'] = $a['esame'];
	 $b['lingua1'] = $a['lingua1']; $b['lingua2'] = $a['lingua2']; $b['lingua3'] = $a['lingua3'];
	 $b['RC'] = $a['RC'];
    $res = $link->autoExecute('Studenti', $b, DB_AUTOQUERY_UPDATE, "id_studente='{$a['id']}'");
    errore_DB($res);
    $res = $link->query("UPDATE Scuola SET id_scuola='{$a['id_scuola_appartenenza']}' WHERE id_studente='{$a['id']}' AND tipo='p'");
    errore_DB($res);
    
    $res = $link->query("UPDATE Scuola SET id_scuola='{$a['id_scuola_affidataria']}' WHERE id_studente='{$a['id']}' AND tipo='f'");
    errore_DB($res);
    exit("<dl><dt>Studente <b>{$a['nome']} {$a['cognome']}</b> modificato con successo<br /><br />Per modificare un altro studente cliccare <a href=\"./VisualizzaStudenti.php\">qui</a></dt></dl>");
}



$up="VisualizzaStudenti";
include "Coda.inc";
?>
