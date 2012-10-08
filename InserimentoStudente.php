<?
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL)
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// Please READ carefully the Docs/License.txt file for further details
// Please READ the Docs/credits.txt file for complete credits list
// ----------------------------------------------------------------------
// Nome file:  FormInserimentoStudente.php
// Autore di questo file: Puria Nafisi
// Modifica 22/7/09: aggiunto campo classe (Sophia Danesino)
// Descrizione: modulo per l'inserimento di una nuova scuola
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore
// ----------------------------------------------------------------------

$title = "Inserimento Studente";

include "Testa.inc";
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN,ID_OPERATORE);
require_once 'HTML/QuickForm.php';

function inserisci_studente ($a) {
  global $link, $REG;
  
  if ( (in_DB('Studenti', 'nome', $a['nome'])) && (in_DB('Studenti', 'cognome', $a['cognome'])) )
  {
    echo "<dl><dt>Registro - Studente <b>{$a['nome']} {$a['cognome']}</b> gi&agrave; presente</dt></dl>";
    echo "<dl><dd>Attenzione: presenza di due utenti con stesso nome e cognome</dd></dl>";
  }
    $b = array_slice($a, 0, 18); $b['note'] = $a['note']; 
    $b['classe'] = $a['classe']['classe'];$b['ordine'] = $a['classe']['ordine']; $b['HC'] = $a['HC']; 
    $b['n_data'] = $a['n_data']['Y']."-".$a['n_data']['M']."-".$a['n_data']['d']; $b['email'] = $a['email']; 
    $b['RC'] = $a['RC']; $b['esame'] = $a['esame'];
    $b['lingua1'] = $a['lingua1'];  $b['lingua2'] = $a['lingua2'];  $b['lingua3'] = $a['lingua3']; 
    $b['ripetente'] = $a['R']; 
    $res = $link->autoExecute('Studenti', $b, DB_AUTOQUERY_INSERT); 
    errore_DB($res);
    $id = mysql_insert_id();
    $res = $link->query("INSERT INTO Scuola (id_studente, id_scuola, tipo) VALUES ( '$id ', '{$a['id_scuola_appartenenza']}' , 'p')");
    errore_DB($res);
    $res = $link->query("INSERT INTO Scuola (id_studente, id_scuola, tipo) VALUES ( '$id ', '{$a['id_scuola_affidataria']}' , 'f')");
    errore_DB($res);
    $path_cartella_studente="/var/Scuole/".$REG."/Studenti/".$id;
    mkdir($path_cartella_studente,0777);
    exit("<dl><dt>Studente <b>{$a['nome']} {$a['cognome']}</b> inserito con successo<br />Per inserire un altro studente cliccare <a href=\"./InserimentoStudente.php\">qui</a><br/>
    Per mandare lo studente in degenza
       <form method=\"post\" action=\"GestioneDegenze.php\">
           <input type=\"hidden\" name=\"id\" value=\"$id\" />
	       <input type=\"image\" src=\"./immagini/button_insert.png\" alt=\"Degenze\" title=\"Degenze\"/>
	          </form>
		  </dt></dl>");
  }

echo "<h2>Inserimento dati studente</h2>";
$form = new HTML_QuickForm('form');
$form->setRequiredNote ('<em style="font-size:80%; color:#ff0000;">*</em><em style="font-size:80%;"> Campo obbligatorio</em>');
$form->setJsWarnings('I dati immessi  non sono validi:','Cortesemente correggere i suddetti campi.');
$form->setDefaults(array('cittadinanza' => 'Italiana', 'sesso' => 'M', 'HC' => '0', 'straniero' => '0', 'RC' => '1' ,'esame' => '0' ,'classe' => '1' ,'ordine' => 'M' ,'R' => '0','lingua1' => 'Inglese','id_scuola_affidataria' => '0'));
$form->addElement('text', 'cognome', 'Cognome:', array('size' => 30, 'maxlength' => 255, 'class' => 'obb'));
$form->addElement('text', 'nome', 'Nome:', array('size' => 30, 'maxlength' => 255, 'class' => 'obb'));
$radio[] = &HTML_QuickForm::createElement('radio', 'sesso', '', 'M', 'M', '');
$radio[] = &HTML_QuickForm::createElement('radio', 'sesso', '', 'F', 'F', '');
$form->addGroup($radio, "sesso", "Sesso:", ' ', false);
$form->addElement('text', 'CF', 'Codice Fiscale:', array('size' => 16, 'maxlength' => 16 ));
$emptyText = array ('d'=>'--', 'M'=>'--', 'Y'=>'----');
$emptyValue = array ('d'=>'00', 'M'=>'00', 'Y'=>'0000');
$options = array('language' => 'it', 'format' => 'dMY', 'minYear' => 1970, 'maxYear' => 2015 , 'class' => 'obb','emptyOptionText'=>$emptyText, 'emptyOptionValue'=>0, 'addEmptyOption'=>true);
$form->addElement('date', 'n_data', 'Data di nascita:', $options, false);
$n_label = array(' Provincia: ',' Stato: ');
$nascita[] = &HTML_QuickForm::createElement('text', 'n_citta', 'Citt&agrave;:', array('size' => 30, 'maxlength' => 30));
$nascita[] = &HTML_QuickForm::createElement('text', 'n_provincia', 'Provincia:', array('size' => 2, 'maxlength' => 2));
$nascita[] = &HTML_QuickForm::createElement('text', 'n_stato', 'Stato:', array('size' => 30, 'maxlength' => 30));
$form->addGroup($nascita, null, "Luogo di nascita:", $n_label);
$r_label = array(' Provincia: ',' Stato: ');
$r1_label = array(' Numero: ',' CAP: ');
$residenza[] = &HTML_QuickForm::createElement('text', 'r_citta', 'Citt&agrave;:', array('size' => 30, 'maxlength' => 30));
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

$select =& $form->addElement('select', 'id_scuola_appartenenza', 'Scuola di appartenenza:');
$select->loadQuery($link, "SELECT CONCAT_WS(' - ',nome,citta) as nome_citta, id_scuola from Scuole ORDER BY id_scuola", 'nome_citta', 'id_scuola');
$select =& $form->addElement('select', 'id_scuola_affidataria', 'Scuola affidataria:');
$select->loadQuery($link, "SELECT CONCAT_WS(' - ',nome,citta) as nome_citta, id_scuola from Scuole ORDER BY id_scuola", 'nome_citta','id_scuola');
$select->addOption("Nessuna", "0");
$classe[] = &HTML_QuickForm::createElement('select', 'classe', 'Classe:', array(
  '1' => '1',
  '2' => '2',
  '3' => '3',
  '4' => '4',
  '5' => '5'
));
$classe[] = &HTML_QuickForm::createElement('select', 'ordine', '', array(
  'M' => 'Scuola dell\'infanzia',
  'P' => 'Scuola primaria',
  'I' => 'Scuola secondaria di I grado',
  'S' => 'Scuola secondaria di II grado'
));
$form->addGroup($classe, 'classe', "Classe", " ");
$radio_esame[] = &HTML_QuickForm::createElement('radio', 'esame', '', 'SI', '1', '');
$radio_esame[] = &HTML_QuickForm::createElement('radio', 'esame', '', 'NO', '0', '');
$form->addGroup($radio_esame, "esame", "Esame di stato:", ' ' , false);

$radio_RC[] = &HTML_QuickForm::createElement('radio', 'RC', '', 'SI', '1', '');
$radio_RC[] = &HTML_QuickForm::createElement('radio', 'RC', '', 'NO', '0', '');
$form->addGroup($radio_RC, "RC", "Studio Religione cattolica:", ' ' , false);
$radio_R[] = &HTML_QuickForm::createElement('radio', 'R', '', 'SI', '1', '');
$radio_R[] = &HTML_QuickForm::createElement('radio', 'R', '', 'NO', '0', '');
$form->addGroup($radio_R, "R", "Ripetente:", ' ' , false);
$form->addElement('text', 'lingua1', 'Prima lingua straniera:', array('size' => 20, 'maxlength' => 20));
$form->addElement('text', 'lingua2', 'Seconda lingua straniera:', array('size' => 20, 'maxlength' => 20));
$form->addElement('text', 'lingua3', 'Terza lingua straniera:', array('size' => 20, 'maxlength' => 20));
$form->addElement('textarea','note','Note:',array('rows' => 10, 'cols' =>90));
$buttons[] = &HTML_QuickForm::createElement('submit', 'btnSubmit', 'Inserisci');
$buttons[] = &HTML_QuickForm::createElement('reset', 'btnClear', 'Pulisci');
$form->addGroup($buttons, null, null, '&nbsp;');
$form->addRule('CF', "Il CF non &egrave; di 16 caratteri", 'rangelength', array(16, 16));
$form->addRule('cognome', 'Il campo del cognome e\' obbligatorio', 'required', '', 'client');
$form->addRule('nome', 'Il campo del nome e\' obbligatorio', 'required', '', 'client');
$form->addRule('sesso', 'Il campo del sesso e\' obbligatorio', 'required', '', 'client');
$form->addRule('straniero', 'Il campo straniero e\' obbligatorio', 'required', '', 'client');
$form->addRule('HC', 'Il campo HC e\' obbligatorio', 'required', '', 'client');
$form->addRule('classe', 'Il campo classe e\' obbligatorio', 'required', '', 'client');
$form->addRule('codice_scuola', 'Il campo del codice e\' obbligatorio', 'required', '', 'client');
$form->addRule('email', 'La email immessa non e\' corretta', 'email', '', 'client');
$form->addRule('n_cap', 'Il cap immesso non &egrave; corretto', 'rangelength', array(5, 5));
$form->addRule('n_cap', 'Il cap e\' invalido', 'numeric', '', 'client');
$form->addRule('r_cap', 'Il cap immesso non &egrave; corretto', 'rangelength', array(5, 5));
$form->addRule('r_cap', 'Il cap e\' invalido', 'numeric', '', 'client');


if ($form->validate())
{
	$form->process('inserisci_studente', false);
}

$form->display();

$up="VisualizzaStudenti";
include "Coda.inc";
?>
