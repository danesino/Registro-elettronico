<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL)
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  DocumentiProgrammazione.php
// Autore di questo file: Puria Nafisi Azizi puria@hipatia.net 
// Descrizione: gestione documenti di programmazione e/o materiali didattici
// di uno studente; inserimento, cancellazione, rinomina documenti di uno
// studente.
// ----------------------------------------------------------------------
// Autorizzazione: amministratore, operatore, docenti del Consiglio di classe,
// docenti e genitori associati con lo studente selezionato
// ----------------------------------------------------------------------


$title = "Registro: area programmazione";
include 'FileIcon.inc.php';
include "Testa.inc";
include "FunzioniDB.inc";
require 'HTTP/Upload.php';
#include "File/Find.php";
include "autenticazione_db.php";
autorizza_ruoli(ID_ADMIN,ID_OPERATORE,ID_OSPEDALIERO,ID_OSSERVATORE,ID_AFFIDATARIO,ID_DOMICILIARE);

if ($RUOLO==ID_OSSERVATORE||$RUOLO==ID_AFFIDATARIO||$RUOLO==ID_DOMICILIARE)
	 autorizza_affidatario_genitore($_POST['id_studente'],$codice_utente);

// Verifica autorizzazione da parte del docente a esaminare
// il prospetto scolastico di quello studente

if ($RUOLO==ID_OSPEDALIERO)
	 autorizza_docente_studente($_POST['id_studente'],$codice_utente);

$dir="/var/Scuole/$REG/Studenti/{$_POST['id_studente']}/";

if(isset($_POST['submit']))
{
	 echo "<br>";
	 $upload = new HTTP_Upload('it');       // Language for error messages
	 $file = $upload->getFiles('userfile'); // return a file object or error
	 if (PEAR::isError($file)) {
		  die ($file->getMessage());
	 }
  
	 // Check if the file is a valid upload
	 if ($file->isValid()) {
		  // this method will return the name of the file you moved,
		  // useful for example to save the name in a database
		  if (file_exists($dir.$_FILES['userfile']['name']))
				die("<dd>il file <b>{$_FILES['userfile']['name']}</b> esiste gi&agrave;!</dd><br />");
		  $file_name = $file->moveTo($dir);
		  chmod($dir.'/'.$file_name, 0666);
		  echo "<dt>File <b>$file_name</b> salvato con successo!</dt><br />";
		  if (PEAR::isError($file_name)) {
				die ($file_name->getMessage());
		  }
	 } elseif ($file->isMissing()) {
		  echo "<dd>Nessun file selezionato</dd><br />";
	 } elseif ($file->isError()) {
		  echo $file->errorMsg();
	 }
}

if(isset($_POST['rinomina']))
{
	 echo "<br>";
	 if(!file_exists($dir.$_POST['newname']))
	 {
		  rename($dir.$_POST['oldname'],$dir.$_POST['newname']);
		  echo "<dt>File <b>{$_POST['newname']}</b> rinominato con successo!</dt><br />";
	 }
	 else
		  echo("<dd>il file <b>{$_POST['newname']}</b> esiste gi&agrave;!</dd><br />");
}

if(isset($_POST['delete']))
{
	 echo "<br>";
	 if(!file_exists($dir.$_POST['file']))
	 {
		  echo("<dd>il file <b>{$_POST['file']}</b> non esiste!</dd><br />");
	 }
	 else
		  unlink($dir.$_POST['file']);
	 echo "<dt>File <b>{$_POST['file']}</b> cancellato con successo!</dt><br />";
}

# elenco dei contenuti di una cartella con link
	 $cartella = opendir($dir);
echo "<style type=\"text/css\">
	 table.sample {
		  border-width: 0px 0px 0px 0px;
		  border-spacing: 5px;
		  border-style: ridge ridge ridge ridge;
		  border-color: gray gray gray gray;
		  border-collapse: separate;
}
table.sample th, table.sample td {
	 border-width: 1px;
	 padding: 5px;	
	 border-style: ridge;
	 border-color: gray;
}
</style>";
if($RUOLO==ID_ADMIN || $RUOLO==ID_OPERATORE || $RUOLO==ID_OSPEDALIERO || $RUOLO==ID_AFFIDATARIO|| $RUOLO==ID_DOMICILIARE):
?>
<center>
	<h3>Upload Nuovo File</h3>
	<form action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data" method="post">
		<table>
		<tr><td>
			<input type="file" name="userfile" size="60"><br>
		</td></tr>
			<input type="hidden" name="id_studente" value="<?=$_POST['id_studente']?>" />
		<tr><td>
			<input type="submit" name="submit" value="Salva File">
		</td></tr>
		</table>
	</form>
	</center>
<?
	 endif;
echo "<h3>Lista dei file</h3><table class=\"sample\"><th>Scarica</th><th>Nome del File</th><th>Dimensione</th><th colspan='2'>Azioni</th>";
while ($file = readdir($cartella)) {
	 $array_file[] = $file;
}
foreach ($array_file as $file) {
	 if ( $file == ".." || $file == ".") {
		  continue;
	 }
	 // get the file
	 $file = new FileIcon($file,$dir);
	 // print the icon plus some data
	 echo '<tr><td><form method="post" action="./Download.php">';
	 echo '<input type="hidden" name="file" value="'.$file->getName().'" />';
	 echo '<input type="hidden" name="dir" value="'.$dir.'" />'.$file -> displayIconForm().'</form></td>'; 
	 if(isset($_POST['rename']) && $file->getName()==$_POST['file'])
	 {
		  echo '<td><form action="'.$_SERVER['PHP_SELF'].'" method="post">';
		  echo '<input type="hidden" name="id_studente" value="'.$_POST['id_studente'].'">';
		  echo '<input type="text" name="newname" value="'.$file -> getName() . '" />';
		  echo '<input type="hidden" name="oldname" value="'.$file->getName().'" />';
		  echo '<input type="submit" name="rinomina" value="Rinomina"></form></td>';
	 }
	 else
		  echo '<td>'.$file -> getName() . '</td>';
	 echo '<td>'. $file -> getSize() .'</td>';
	 if ($RUOLO==ID_ADMIN || $RUOLO==ID_OPERATORE || $RUOLO==ID_OSPEDALIERO || $RUOLO==ID_AFFIDATARIO || $RUOLO==ID_DOMICILIARE){
		  echo '<td><form method="post" action="'.$_SERVER['PHP_SELF'].'">';
		  echo '<input type="hidden" name="id_studente" value="'.$_POST['id_studente'].'">';
		  echo '<input type="hidden" name="file" value="'.$file->getName().'" />';
		  echo '<input type="hidden" name="rename" value="1">';
		  echo '<input type="image" src="./immagini/filesaveas.png" alt="Rinomina" title="Rinomina"/></form></td>';
		  echo '<td><form method="post" action="'.$_SERVER['PHP_SELF'].'">';
		  echo '<input type="hidden" name="id_studente" value="'.$_POST['id_studente'].'">';
		  echo '<input type="hidden" name="file" value="'.$file->getName().'" />';
		  echo '<input type="hidden" name="delete" value="1">';
		  echo '<input type="image" src="./immagini/button_drop.png" alt="Elimina" title="Elimina"/></form></td></tr>';
	 }
	 // set the icon url
	 $file -> setIconUrl('icons/');
}
echo "</table>";

if (($RUOLO==ID_ADMIN)||($RUOLO==ID_OPERATORE))
			$up="index";
else if ($profile==ID_OSPEDALIERO)
			$up="indice_doc";
		else
			$up="indice_aff";
if(isset($up)) echo "<br><br><center><a href=\"$up\"><img src=\"immagini/menu.png\"></a><br>Pagina Iniziale</center>";
include "Coda.inc";
?>
