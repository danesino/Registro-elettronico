<?php
// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere - tulip
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) Version 2 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// Please READ carefully the Docs/License.txt file for further details
// Please READ the Docs/credits.txt file for complete credits list
// ----------------------------------------------------------------------
// Editor: tyny_mce
// ----------------------------------------------------------------------
// Nome file:  CompilaRegistro.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: consente la compilazione del registro (Argomenti svolti,
// Valutazione formativa, Valutazione sommativa, Osservazioni)
// 22/7/09 Modifica compilazione a seguito di modifica DB 
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore, operatore, docente (solo se 
// insegna in quel gruppo di lavoro)
// ----------------------------------------------------------------------


$title = "Compilazione registro";
include "Testa.inc";
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN,ID_OPERATORE,ID_OSPEDALIERO,ID_AFFIDATARIO, ID_DOMICILIARE);
require_once 'HTML/QuickForm.php';

function compila_registro ($a) {
	global $link, $REG;
  
    $b = array_slice($a, 0, 3); 
    $b['data'] = $a['data']['Y']."-".$a['data']['M']."-".$a['data']['d'];
	$b['argomenti']=$a['argomenti'];
	$b['valutazione']=$a['valutazione'];
	$res = $link->autoExecute('Registro', $b, DB_AUTOQUERY_INSERT); 
    errore_DB($res);
}

include("autenticazione_db.php"); 
	// Verifica autorizzazione da parte del docente a compilare quel registro
	if ($profile==ID_AFFIDATARIO)
		autorizza_docente_registro ($_POST['id_degenza']-1,$codice_utente);
	if ($profile==ID_OSPEDALIERO)
		autorizza_docente_registro ($_POST['id_degenza'],$codice_utente);
	if ($profile==ID_DOMICILIARE)
		autorizza_docente_registro ($_POST['id_degenza'],$codice_utente);
		
?>
  
<!-- TinyMCE -->
<script type="text/javascript" src="./tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
    mode : "textareas",
    theme : "advanced",
    theme_advanced_buttons1 : "bold,italic,underline,separator,justifyleft,justifycenter,justifyright, justifyfull,separator,bullist,numlist,separator,undo,redo, separator,charmap",
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    //theme_advanced_statusbar_location : "bottom",
    plugins : 'inlinepopups',
    setup : function(ed) {
        // Add a custom button
        ed.addButton('mybutton', {
            title : 'My button',
            image : 'img/example.gif',
            onclick : function() {
				// Add you own code to execute something on click
				ed.focus();
                ed.selection.setContent('<strong>Hello world!</strong>');
            }
        });
    }
});
</script>

<!-- /TinyMCE -->

<?php
  
  if (empty($_POST['id_materia']))
	{	
		print "<h3>Gestione registro</h3>";
		print '<form action="CompilaRegistro.php" method="post">';
		print '<input type="hidden" name="id_degenza" value="'; print $_POST['id_degenza']; print '">';
		print '<input type="hidden" name="id_studente" value="'; print $_POST['id_studente']; print '">';
		print '<table border=1><tr><th> Materia </th>';
		if ($profile==ID_ADMIN)
			$sql =& $link->query("SELECT * FROM Materie");
		else if ($profile==ID_AFFIDATARIO)
			$sql =& $link->query("SELECT  DISTINCT Materie.* FROM Materie, CdC, Classe WHERE 
				Materie.id_materia=CdC.id_materia AND  
				(Classe.id_degenza= ?) AND 
				CdC.id_classe=Classe.id_classe AND
				(CdC.id_utente = ? )", array($_POST['id_degenza']-1,$CODICE_UTENTE));
		else
			$sql =& $link->query("SELECT  DISTINCT Materie.* FROM Materie, CdC, Classe WHERE 
				Materie.id_materia=CdC.id_materia AND  
				(Classe.id_degenza= ?) AND 
				CdC.id_classe=Classe.id_classe AND
				(CdC.id_utente = ? )", array($_POST['id_degenza'],$CODICE_UTENTE));
			errore_DB($sql);
	   	print '<td><select name="id_materia" >';
		while ( $materia =& $sql->fetchRow())
		{
			print "<option name=\"id_materia\" value=\"{$materia['id_materia']}\">{$materia['nome']}</option>";
   		}
		print "</select></td></tr>";
		print '<tr><td><input type="submit" value="Seleziona"></form></td></tr></table>';
	
		if ($profile==ID_OSSERVATORE || $profile==ID_AFFIDATARIO)
                	$up="ListaProspetti.php";
		elseif($profile==ID_OSPEDALIERO || $profile==ID_DOMICILIARE)
			$up="Registro.php";
		if(isset($up)) echo "<br><br><center><a href=\"$up\"><img src=\"immagini/menu.png\"></a><br>Pagina Iniziale</center>";
	} // endif
	else // la materia è già stata selezionata
	{
		$sql =& $link->query("SELECT * FROM Studenti WHERE (id_studente= ?)",  $_POST['id_studente']);
		$studente =& $sql->fetchRow();
		
		$sql =& $link->query("SELECT * FROM Registro WHERE (id_degenza= ? ) AND (id_materia= ? )", array($_POST['id_degenza'], $_POST['id_materia']));
		$num_righe = $sql->numRows(); 
		$sql_materia =& $link->query("SELECT * FROM Materie WHERE (id_materia= ?)", $_POST['id_materia']);
		$riga_materia =& $sql_materia->fetchRow();
		print "<h3>Registro "; print $riga_materia['nome']; print " - ";
		print $studente['cognome']; print " "; print $studente['nome']; print "</h3>";
		
	    if ($num_righe!=0) {
			
			print "<table class=\"elenco\">";
			print "<tr>";
			print '<th>Data</th>';
			print '<th>Ruolo</th>';
			print '<th>Argomenti</th>';
			print '<th>Valutazione e osservazioni</th>';
			print '<th>Azione</th>';
			print "</tr>";
		
		
			while ($riga =& $sql->fetchRow())
			{
				print "<tr>";
				print "<td>".$riga['data']."</td>";
				print "<td>".$riga['ruolo']."</td>";
				print "<td>".$riga['argomenti']."</td>";
				print "<td>".$riga['valutazione']."</td>";
				print "<td>";
				//if ($profile == ID_ADMIN || $profile == $riga['ruolo']) {
					//print '<form method="post" action="ModificaRegistro.php">
						//	<input type="hidden" name="id" value="<'.$riga['id_registro'].'>" />
							//<input type="image" src="./immagini/button_edit.png" alt="Modifica" title="Modifica"/>
							//</form>';
							// CANCELLAZIONE!!!!!!!!!!!!!!
				//}
				print "</tr>";
			} // end while
			
			print "</table>";
		} // end if
		
		$form = new HTML_QuickForm('form');
		$form->setRequiredNote ('<em style="font-size:80%; color:#ff0000;">*</em><em style="font-size:80%;"> Campo obbligatorio</em>');
		
		$emptyValue = array ('d'=>'00', 'M'=>'00', 'Y'=>'0000');
		$options = array('language' => 'it', 'format' => 'dMY', 'minYear' => 2011, 'maxYear' => 2012, 'class' => 'obb','emptyOptionText'=>$emptyText, 'emptyOptionValue'=>0, 'addEmptyOption'=>true);
		$form->addElement('date', 'data', 'Data:', $options, false);
		$form->addElement('textarea','argomenti','Argomenti:');
		$form->addElement('textarea','valutazione','Valutazione:');
		$form->addElement('hidden','id_degenza',$_POST['id_degenza']);
		$form->addElement('hidden','id_materia',$_POST['id_materia']);
		$form->addElement('hidden','ruolo',$profile);
		$form->addElement('hidden','id_studente',$_POST['id_studente']);

		$buttons[] = &HTML_QuickForm::createElement('submit', 'btnSubmit', 'Inserisci');
		$buttons[] = &HTML_QuickForm::createElement('reset', 'btnClear', 'Pulisci');
		$form->addGroup($buttons, null, null, '&nbsp;');
		$form->addRule('data', 'Il campo data e\' obbligatorio', 'required', '', 'client');
		$form->display();
		if ($form->validate())
		{
			$form->process('compila_registro', false);
		}
		
}
	
?>
<? include "Coda.inc"; ?>
