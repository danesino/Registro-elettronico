<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  GestioneReparti.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: visualizza l'elenco dei reparti esistenti, permette la loro
// modifica e la visualizzazione degli studenti per reparto, consente 
// l'inserimento di nuovi reparti
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore
// ----------------------------------------------------------------------
$title = "Gestione Reparti";
include "Testa.inc";	
include "FunzioniDB.inc";
require_once 'HTML/QuickForm.php';
autorizza_ruoli(ID_ADMIN);

js_validazione();

echo "<h2>Gestione reparti ospedalieri</h2>";
if(isset($_POST['cancella']))
{
	 $r =& $link->query("SELECT id_repdeg FROM Reparto WHERE (id_reparto = ? )", $_POST['id_reparto']);
	 $n = $r->numRows();
	 if ( $n != 0)
		  die("<dl><dd>Impossibile cancellare un reparto con studenti gi&agrave; degenti in esso!<br/> <a href='./GestioneReparti.php'>Torna all'elenco</a></dd></dl>");
	 $sql = $link->query("DELETE FROM Reparti WHERE (id_reparto= ? )", $_POST['id_reparto']);
	 errore_DB($sql);
	 exit("<dl><dt>Cancellazione effettuata con successo<br /><br />Per cancellare un altro reparto cliccare <a href=\"./GestioneReparti.php\">qui</a></dt></dl>");
}

if(isset($_POST['nome']))
{
  	 $vettore = array ('nome' => $_POST['nome'], 'descrizione' => $_POST['descrizione']);
}

if (isset($_POST['modifica']))
{
	 $res = $link->autoExecute('Reparti', $vettore, DB_AUTOQUERY_UPDATE, "id_reparto=".$_POST['id_reparto']);
	 if (PEAR::isError($res))
		  box_errore("Errore Modifica_Reparto ".$res->getMessage());
	 else
		  box_successo("Reparto <b>{$vettore['nome']}</b> modificato con successo<br /><br />Per modificare un altro reparto cliccare <a href=\"./GestioneReparti.php\">qui</a>");
}

if (isset($_POST['ricerca']))
{
	 $id_reparto = $link->quoteSmart($_POST['id_reparto']);
	 $sql = $link->query("SELECT * FROM Reparti WHERE id_reparto=$id_reparto");
	 if (PEAR::isError($sql))
		  box_errore("Errore Modifica Reparto ".$sql->getMessage());
	 while ($riga =& $sql->fetchRow(DB_FETCHMODE_ASSOC))
	 {
		  echo "<h3>Modifica dati reparto {$riga['nome']} </h3>";
		  $form = new HTML_QuickForm('form');
		  $form->setRequiredNote ('<em style="font-size:80%; color:#ff0000;">*</em><em style="font-size:80%;"> Campo obbligatorio</em>');
		  $form->setJsWarnings('I dati immessi  non sono validi:','Cortesemente correggere i suddetti campi.');
		  $form->setDefaults(array('nome' => $riga['nome'], 'descrizione' => $riga['descrizione']));
		  $form->addElement('text', 'nome', 'Nome:', array('size' => 30, 'maxlength' => 255, 'alt' => 'Nome reparto','class' => 'obb'));
		  $form->addElement('text', 'descrizione', 'Descrizione:', array('size' => 30, 'maxlength' => 255, 'alt' => 'Descrizione reparto'));
		  $form->addElement('hidden', 'modifica', '1');
		  $form->addElement('hidden', 'id_reparto', $_POST['id_reparto']);
		  $buttons[] = &HTML_QuickForm::createElement('submit', null, 'Modifica');
		  $buttons[] = &HTML_QuickForm::createElement('reset', null, 'Annulla');
		  $form->addGroup($buttons, null, null, '&nbsp;');
		  $form->addRule('nome', 'Il campo del nome e\' obbligatorio', 'required', '', 'client');
		  $form->display();
	 }
}
else
{
	 echo "<h3>Inserimento nuovo reparto</h3>";
	 $form2 = new HTML_QuickForm('form_inserisci');
	 $form2->setRequiredNote ('<em style="font-size:80%; color:#ff0000;">*</em><em style="font-size:80%;"> Campo obbligatorio</em>');
	 $form2->setJsWarnings('I dati immessi  non sono validi:','Cortesemente correggere i suddetti campi.');
	 $form2->addElement('text', 'nome', 'Nome:', array('size' => 30, 'maxlength' => 255, 'class' => 'obb'));
	 $form2->addElement('text', 'descrizione', 'Descrizione:', array('size' => 30, 'maxlength' => 255));	
	 $form2->addRule('nome', 'Il campo del nome e\' obbligatorio', 'required', '', 'client');
	 $form2->addElement('submit', null, 'Aggiungi');
	 if ($form2->validate())
		  $form2->process('inserisci_reparto', false);
	 $form2->display();
}

$sql = $link->query("SELECT * FROM Reparti");
$num_righe = $sql->numRows();
if ($num_righe!=0):?>
	<h3>Elenco reparti</h3>
	<table class="elenco">
	<tr>
	<th>Nome</th>
	<th>Descrizione </th>
	<th colspan="3">Azione </th>
<? 
	 $pari=1;
while ($riga =& $sql->fetchRow(DB_FETCHMODE_ASSOC)):
	 $class = ($pari) ? "pari" : "dispari";
$pari=1-$pari;
?>
<tr class="<?=$class?>"><td nowrap="nowrap"><?=$riga['nome']?></td>
<td nowrap="nowrap"><?=$riga['descrizione']?></td>
<td  align="center" nowrap="nowrap"><form method="post" action="<?=$_SERVER['PHP_SELF']?>">
	 <input type="hidden" name="id_reparto" value="<?=$riga['id_reparto']?>" />
	 <input type="hidden" name="ricerca" value="1" />
	 <input type="image" src="./immagini/button_edit.png" alt="Modifica" title="Modifica"/></form></td>

<td  align="center" nowrap="nowrap"><form method="post" action="VisualizzaStudentiReparto.php">
	 <input type="hidden" name="id_reparto" value="<?=$riga['id_reparto']?>" />
	 <input type="hidden" name="nome_reparto" value="<?=$riga['nome']?>" />
	 <input type="image" src="./immagini/forum.png" alt="Visualizza" title="Visualizza elenco studenti per reparto"/></form></td>


<td  align="center" nowrap="nowrap"><form method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="return validazione()">
	 <input type="hidden" name="id_reparto" value="<?=$riga['id_reparto']?>" />
	 <input type="hidden" name="cancella" value="1" />
	 <input type="image" src="./immagini/button_drop.png" alt="Cancella" title="Cancella"/></form></td></tr>
<?php		
endwhile;
print "</table>";
endif;
include "Coda.inc";?>
