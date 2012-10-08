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

function ConvertitoreData($data){
          $separa=explode ("-",$data);
          $a=$separa[0];
          $b=$separa[1];
          $c=$separa[2];
          $data_convertita="$c-$b-$a";
          return $data_convertita;
}

function compila_registro ($a) {
	global $link, $REG;
    $b = array_slice($a, 0, 3); 
    $b['data'] = $a['data']['Y']."-".$a['data']['M']."-".$a['data']['d'];
	$b['argomenti']=$a['argomenti'];
	$b['osservazioni']=$a['osservazioni'];
	$b['valutazione']=$a['valutazione']['voto_int'].".".$a['valutazione']['voto_dec'];
	$res = $link->autoExecute('Registro', $b, DB_AUTOQUERY_INSERT); 
    errore_DB($res);
}

include("autenticazione_db.php"); 
	// Verifica autorizzazione da parte del docente a compilare quel registro
	if ($profile==ID_AFFIDATARIO || $profile==ID_DOMICILIARE)
		autorizza_docente_registro ($_POST['id_degenza'],$codice_utente);
	if ($profile==ID_OSPEDALIERO)
		autorizza_docente_registro ($_POST['id_degenza'],$codice_utente);
		
?>
  
<!-- TinyMCE -->
<script type="text/javascript" src="./tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
    mode : "textareas",
    theme : "advanced",
	forced_root_block : false,
    theme_advanced_buttons1 : "mybutton,bold,italic,underline,separator,justifyleft,justifycenter,justifyright, justifyfull,separator,bullist,numlist,separator,undo,redo, separator,charmap,sub,sup,forecolor,backcolor",
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
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
  
  if (empty($_POST['id_materia']))
	{	
		print "<h3>Gestione registro</h3>";
		print '<form action="CompilaRegistro.php" method="post">';
		print '<input type="hidden" name="id_degenza" value="'; print $_POST['id_degenza']; print '">';
		print '<input type="hidden" name="id_studente" value="'; print $_POST['id_studente']; print '">';
		print '<input type="hidden" name="id_classe" value="'; print $_POST['id_classe']; print '">';
		print '<table border=1><tr><th> Materia </th>';
		if ($profile==ID_ADMIN)
			$sql =& $link->query("SELECT * FROM Materie");
		else if ($profile==ID_AFFIDATARIO)
			$sql =& $link->query("SELECT  DISTINCT Materie.* FROM Materie, CdC, Classe WHERE 
				Materie.id_materia=CdC.id_materia AND  
				(Classe.id_degenza= ?) AND 
				CdC.id_classe=Classe.id_classe AND
				(CdC.id_utente = ? )", array($_POST['id_degenza'],$CODICE_UTENTE));
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
		$sql_materia =& $link->query("SELECT * FROM Materie WHERE (id_materia= ?)", $_POST['id_materia']);
		$riga_materia =& $sql_materia->fetchRow();
		print "<h3>Registro "; print $riga_materia['nome']; print " - ";
		print $studente['cognome']; print " "; print $studente['nome']; print "</h3>";

		$form = new HTML_QuickForm('form');
		
		$form->setRequiredNote ('<em style="font-size:80%; color:#ff0000;">*</em><em style="font-size:80%;"> Campo obbligatorio</em>');
		
		// campo data
		$emptyValue = array ('d'=>date('d'), 'M'=>date('m'), 'Y'=>date('Y'));
		$options = array('language' => 'it', 'format' => 'dMY', 'minYear' => 2011, 'maxYear' => 2012, 'class' => 'obb','emptyOptionText'=>$emptyText, 'emptyOptionValue'=>0, 'addEmptyOption'=>true);
		$form->addElement('date', 'data', 'Data:', $options, false);
		// campo voto
		$valutazione[] = &HTML_QuickForm::createElement('select', 'voto_int', '', array('0'=>'&nbsp','10'=>'10', '9'=>'9','8'=>'8','7'=>'7','6'=>'6','5'=>'5','4'=>'4'));
		$valutazione[] = &HTML_QuickForm::createElement('select', 'voto_dec', '',array('0'=>'&nbsp','25'=>'.25', '50'=>'.50','75'=>'.75','90'=>'.90'));
		$form->addGroup($valutazione,'valutazione','Valutazione: ','&nbsp;');
		// campo argomenti
		$form->addElement('textarea','argomenti','Argomenti:',array('rows' => 1, 'cols' =>110));
		// campo osservazioni
		$form->addElement('textarea','osservazioni','Osservazioni:',array('rows' => 1, 'cols' =>110));
		// campi nascosti
		$form->addElement('hidden','id_degenza',$_POST['id_degenza']);
		$form->addElement('hidden','id_materia',$_POST['id_materia']);
		$form->addElement('hidden','ruolo',$profile);
		$form->addElement('hidden','id_studente',$_POST['id_studente']);
		$form->addElement('hidden','id_classe',$_POST['id_classe']);
	
		$buttons[] = &HTML_QuickForm::createElement('submit', 'btnSubmit', 'Inserisci');
		$buttons[] = &HTML_QuickForm::createElement('reset', 'btnClear', 'Cancella');
		$form->addGroup($buttons, null, null, '&nbsp;');
		$form->addRule('data', 'Il campo data e\' obbligatorio', 'required', '', 'client');
		if ($form->validate()) 	
			$form->process('compila_registro', false);

		$form->setConstants(array(
				'data'=>$emptyValue,
				'argomenti' => '',
				'osservazioni' => '',
				'valutazione' => '',
		));
					
		$form->display();
		
		$sql =& $link->query("SELECT * FROM Registro WHERE (id_degenza= ? ) AND (id_materia= ? ) ORDER BY data", array($_POST['id_degenza'], $_POST['id_materia']));
		$num_righe = $sql->numRows(); 
		
	    if ($num_righe!=0) {
			
			print "<table class=\"elenco\" width=\"80%\" >";
			print '<tr class="<?=$class?>">';
			print '<th>Data</th>';
			print '<th>Ruolo</th>';
			print '<th width="40%">Argomenti</th>';
			print '<th width="30%">Osservazioni</th>';
			print '<th>Valutazione</th>';
			print '<th colspan="2">Azione</th>';
			print "</tr>";
		
			$pari=1;
			while ($riga =& $sql->fetchRow(DB_FETCHMODE_ASSOC)):
			$class = ($pari) ? "pari" : "dispari";
			$pari = 1-$pari;
			
				print "<tr class='".$class."'>	";
				$data=ConvertitoreData($riga['data']);
				print "<td nowrap='nowrap'>".$data."</td>";
				if ($riga['ruolo']==ID_DOMICILIARE)
					print "<td nowrap='nowrap'>Domiciliare</td>";
				else if ($riga['ruolo']==ID_AFFIDATARIO)
					print "<td nowrap='nowrap'>Esterno</td>";
				else
					print "<td nowrap='nowrap'>Ospedaliero</td>";
				print "<td >".$riga['argomenti']."</td>";
				print "<td >".$riga['osservazioni']."</td>";
				if ($riga['valutazione']=='0.00')
						print "<td ></td>";
				else
					print "<td >".$riga['valutazione']."</td>";
				if ($profile == ID_ADMIN || $profile == $riga['ruolo']) {
					print '<td ><form method="post" action="ModificaRegistro.php">
						<input type="hidden" name="id_registro" value="'.$riga['id_registro'].'" />
						<input type="hidden" name="id_materia" value="'.$riga['id_materia'].'" />
						<input type="hidden" name="id_degenza" value="'.$riga['id_degenza'].'" />
						<input type="hidden" name="id_studente" value="'.$_POST['id_studente'].'" />
						<input type="hidden" name="id_classe" value="'.$_POST['id_classe'].'" />
						<input type="hidden" name="azione" value="M" />
					    <input type="image" src="./immagini/button_edit.png" alt="Modifica" title="Modifica elemento registro"/>
						</form></td>';
					print '<td><form method="post" action="ModificaRegistro.php">
						<input type="hidden" name="id_registro" value="'.$riga['id_registro'].'" />
						<input type="hidden" name="id_materia" value="'.$riga['id_materia'].'" />
						<input type="hidden" name="id_degenza" value="'.$riga['id_degenza'].'" />
						<input type="hidden" name="id_studente" value="'.$_POST['id_studente'].'" />
						<input type="hidden" name="id_classe" value="'.$_POST['id_classe'].'" />
						<input type="hidden" name="azione" value="D" />
						<input type="image" src="./immagini/button_drop.png" alt="Cancellazione" title="Cancellazione elemento registro" />
						</form></td>';
				}
				else print "<td></td><td></td>";
				print "</tr>";
				
			 endwhile; // end while
			
			print "</table>";
			
		$sql =& $link->query("SELECT ROUND(AVG(valutazione),2) as media FROM Registro WHERE (id_degenza= ? ) AND (id_materia= ? ) AND valutazione<>0", array($_POST['id_degenza'], $_POST['id_materia']));
		$num_righe = $sql->numRows(); 		
	    if ($num_righe!=0) {
			$voto_medio =& $sql->fetchRow();
			print "<h3>Media: ".$voto_medio['media']."</h3>";
		}

			print '<br><br><center><form method="post" action="CalcoloMediaStudente.php">
						<input type="hidden" name="id_degenza" value="'.$_POST['id_degenza'].'" />
						<input type="hidden" name="id_studente" value="'.$_POST['id_studente'].'" />
						<input type="hidden" name="id_classe" value="'.$_POST['id_classe'].'" />
						<input type="hidden" name="id_materia" value="'.$_POST['id_materia'].'" />
						<input type="submit" name="invio" value="Calcolo media per periodo"></form></center>';
			
			print '<br><center><form method="post" action="CompilaRegistro.php">
						<input type="hidden" name="id_degenza" value="'.$_POST['id_degenza'].'" />
						<input type="hidden" name="id_studente" value="'.$_POST['id_studente'].'" />
						<input type="hidden" name="id_classe" value="'.$_POST['id_classe'].'" />
						<input type="submit" name="invio" value="Compila altra materia dello stesso alunno"></form></center>';
			
			print '<br><center><form method="post" action="GestioneClasse.php">
						<input type="hidden" name="id_materia" value="'.$_POST['id_materia'].'" />
						<input type="hidden" name="id_classe" value="'.$_POST['id_classe'].'" />
						<input type="submit" name="invio" value="Compila la stessa materia di un altro alunno"></form></center>';
						
			print '<br><center><form method="post" action="GestioneClasse.php">
						<input type="hidden" name="id_classe" value="'.$_POST['id_classe'].'" />
						<input type="submit" name="invio" value="Torna al gruppo di lavoro"></form></center>';
		} // end if
		
		if (($RUOLO==ID_ADMIN)||($RUOLO==ID_OPERATORE))
			$up="indice.php";
		else if ($profile==ID_OSPEDALIERO)
			$up="Registro.php";
		else
			$up="indice_aff.php";
		if(isset($up)) echo "<br><br><center><a href=\"$up\"><img src=\"immagini/menu.png\"></a><br>Pagina Iniziale</center>";
		include "Coda.inc";
		
		
	}


	

?>

