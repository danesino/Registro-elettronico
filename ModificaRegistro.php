<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  ModificaRegistro.php
// Autore di questo file: Sophia Danesino, Puria Nafisi 
// Descrizione: Modifica registro relativo al periodo di degenza di quello
// studente
// 23 luglio 2007: modifica per cancellazione e modifica nuove tabelle DB 
// ----------------------------------------------------------------------
// Autorizzazione: amministratore, operatore, docenti del Consiglio di classe,
// ----------------------------------------------------------------------

$title = "Gestione elenchi studenti per gruppo di lavoro";
include "Testa.inc";
include "FunzioniDB.inc";
require_once 'HTML/QuickForm.php';
autorizza_ruoli(ID_ADMIN,ID_OPERATORE,ID_OSPEDALIERO,ID_DOMICILIARE,ID_AFFIDATARIO);

function ConvertitoreData($data){
          $separa=explode ("-",$data);
          $a=$separa[0];
          $b=$separa[1];
          $c=$separa[2];
          $data_convertita="$c-$b-$a";
          return $data_convertita;
}

function modifica_registro ($a) {
	global $link, $REG;
    
    $b['id_registro']=$a['id_registro']; 
    $b['data'] = $a['data']['Y']."-".$a['data']['M']."-".$a['data']['d'];
	$b['argomenti']=$a['argomenti'];
	$b['osservazioni']=$a['osservazioni'];
	$b['valutazione']=$a['valutazione'];
	$res = $link->autoExecute('Registro', $b, DB_AUTOQUERY_UPDATE,'id_registro ='.$_POST['id_registro']); 
  
    if (PEAR::isError($res)) {die($res->getMessage());}
    else exit('<dl><dt>Modifica registro eseguita con successo<br /><center><form method="post" action="CompilaRegistro.php">
						<input type="hidden" name="id_materia" value="'.$a['id_materia'].'" />
						<input type="hidden" name="id_degenza" value="'.$a['id_degenza'].'" />
						<input type="hidden" name="id_studente" value="'.$a['id_studente'].'" />
						<input type="hidden" name="id_classe" value="'.$_POST['id_classe'].'" />
						<input type="submit" name="invio" value="Torna al registro"></form></center>');
}
?>

<!-- TinyMCE -->
<script type="text/javascript" src="./tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
    mode : "textareas",
    theme : "advanced",
    theme_advanced_buttons1 : "mybutton,bold,italic,underline,separator,justifyleft,justifycenter,justifyright, justifyfull,separator,bullist,numlist,separator,undo,redo, separator,charmap,sub,sup,forecolor,backcolor",
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
	forced_root_block : false,
    //theme_advanced_statusbar_location : "bottom",
    plugins : 'inlinepopups',
    setup : function(ed) {
        // Add a custom button
        ed.addButton('mybutton', {
            title : 'Chiusura quadrimestre',
            image : './tiny_mce/img/quadrimestre.jpg',
            onclick : function() {
				// Add you own code to execute something on click
				ed.focus();
                ed.selection.setContent('<strong>FINE QUADRIMESTRE</strong>');
            }
        });
    }
});
</script>

<!-- /TinyMCE -->

<?php

include("autenticazione_db.php"); 
// Verifica autorizzazione da parte del docente a compilare quel registro
if ($profile==ID_OSPEDALIERO)
	autorizza_docente_registro ($_POST['id_degenza'],$codice_utente);
if ($profile==ID_DOMICILIARE )
	autorizza_affidatario_genitore($_POST['id_studente'],$codice_utente);
	
if ($_POST['azione'] == 'D') {
	// cancellazione elemento del registro
	$sql =& $link->query("DELETE FROM Registro WHERE (id_registro= ? )", array($_POST['id_registro'])); 
	errore_DB($sql);
	print("<dl><dt>Cancellazione effettuata con successo<br /></dt></dl>");
	print '<center><form method="post" action="CompilaRegistro.php">
						<input type="hidden" name="id_materia" value="'.$_POST['id_materia'].'" />
						<input type="hidden" name="id_degenza" value="'.$_POST['id_degenza'].'" />
						<input type="hidden" name="id_studente" value="'.$_POST['id_studente'].'" />
						<input type="hidden" name="id_classe" value="'.$_POST['id_classe'].'" />
						<input type="submit" name="invio" value="Torna al registro">
			</form></center>';
	
}
else {
	// modifica elemento del registro
	
	$sql = 'SELECT * FROM Registro WHERE id_registro ='. $link->quoteSmart($_POST['id_registro']);
	$res = $link->query($sql);
	$riga = $res->fetchRow(DB_FETCHMODE_ASSOC);	
	$form = new HTML_QuickForm('form');
		
	$form->setRequiredNote ('<em style="font-size:80%; color:#ff0000;">*</em><em style="font-size:80%;"> Campo obbligatorio</em>');
	if ($riga['valutazione']=='0.00')
		$riga['valutazione']='';
	$form->setDefaults(array('argomenti' => $riga['argomenti'],'osservazioni' => $riga['osservazioni'], 'valutazione' => $riga['valutazione'],'data' => $riga['data']));

	$options = array('language' => 'it', 'format' => 'dMY', 'minYear' => 2011, 'maxYear' => 2012, 'class' => 'obb','emptyOptionText'=>$emptyText, 'emptyOptionValue'=>0, 'addEmptyOption'=>true);
	$form->addElement('date', 'data', 'Data:', $options, false);
	$form->addElement('text','valutazione','Valutazione:', array('size' => 5, 'maxlength' => 5 ));
	$form->addElement('textarea','argomenti','Argomenti:',array('rows' => 1, 'cols' =>110));
	$form->addElement('textarea','osservazioni','Osservazioni:',array('rows' => 1, 'cols' =>110));
	$form->addElement('hidden', 'id_registro');
	$form->addElement('hidden', 'id_materia');
	$form->addElement('hidden', 'id_studente');
	$form->addElement('hidden', 'id_degenza');
	$form->addElement('hidden', 'id_classe');
	$form->addElement('submit', null, 'Salva');
	$form->addRule('data', 'Il campo data e\' obbligatorio', 'required', '', 'client');
	$form->addRule('valutazione', 'Il campo valutazione &egrave; composto da due numeri separati da un punto (ad esempio 9.25)', 'numeric');
		
	if ($form->validate())
				$form->process('modifica_registro', false);
		$form->display();
		
	}

?>
