<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title>Installazione Registro Elettronico</title>
  <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
  <style type="text/css">
   @import url(registro.css);
  </style>
  <link rel="shortcut icon" href="favicon.ico" />
 </head>
 <body>

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
// Nome file:  Installazione.php
// Autore di questo file: Puria Nafisi Azizi (pna) - puria@hipatia.net
// lug/09: modificato da Sophia Danesino per aggiornamento a versione 3
// lug/12: modificato da Sophia Danesino per aggiornamento a versione 4
// Descrizione: installazione registro elettronico per una scuola
// ospedaliera (creazione tabelle, caricamento valori iniziali, creazione
// file di configurazione e predisposizione area per documenti studenti)
// ----------------------------------------------------------------------

if (isset($_POST['inserisci']))
{
// verifica inserimento campi obbligatori
 if ( empty($_POST['nome']) || empty($_POST['codice'])|| empty($_POST['indirizzo'])|| empty($_POST['citta'])||
      empty($_POST['cod_amm'])|| empty($_POST['cod_reg'])|| empty($_POST['cognome_amm'])|| empty($_POST['pwd_amm']))
  {
	 echo "<dd>Errore: I campi in rosso sono Obbligatori:<br />Codice Scuola, Nome, Indirizzo, Citt&agrave;, Codice registro<br /> Cognome amministratore, Codice amministratore e la Password</dd>";
  }
 else
 {
   $nome_db="Registro_".$_POST['cod_reg'];
   $path_scuola="/var/Scuole/".$_POST['cod_reg'];
   mkdir($path_scuola,0777);
   $path_studenti="/var/Scuole/".$_POST['cod_reg']."/Studenti";
   mkdir($path_studenti,0777);
   $path_nome="/var/Scuole/".$_POST['cod_reg']."/Configurazione".$nome_db.".php";
   if (is_file($path_nome))
   {
     print "<dl><dd>Registro gi&agrave; esistente</dd></dl>";
   }
   else
   {
   	 // creazione file ConfigurazioneRegistro_'codice'.php
     touch($path_nome);
     if ($fp=fopen($path_nome,"w"))
     {
			$body = 'require_once "DB.php";
			require_once "/etc/registro.conf";
			$db="'.$nome_db.'";
			$dsn = "mysql://$u:$p@localhost/$db";
			if (!$link)
			{
				$link =& DB::connect($dsn);
				if (PEAR::isError($link))
				{
					die($link->getMessage());
				}
			}
			$link->setFetchMode(DB_FETCHMODE_ASSOC);';
			fwrite($fp,"<?php\n");
			fwrite($fp, $body);
			fwrite($fp,"?>");
			fclose($fp);
	 }
	 chmod($path_nome, 0400);
	 // creazione directory per programmazione studenti

	 require "DB.php";
	 require "/etc/registro.conf";
	 include "FunzioniDB.inc";
	 exec("echo 'CREATE DATABASE $nome_db;' | mysql --user='$u' --password='$p'");
	 exec("mysql --user='$u' --password='$p' --database='$nome_db' < db.sql");
	 
     $dsn="mysql://$u:$p@localhost/".$nome_db;
     $link =& DB::connect($dsn);
     if (PEAR::isError($link))
     {
       die("<dd>Morto".$link->getMessage()."</dd>");
     }
     else 
     {
		$pwd_sha1=sha1($_POST['pwd_amm']);
		$a=array('cognome', 'nome', 'username', 'pwd', 'id_ruolo');
		$b=array($_POST['cognome_amm'], $_POST['nome_amm'], $_POST['cod_amm'], $pwd_sha1, '1');
		autoinsert('Utenti',$a,$b);
		$c=array('cod_reg', 'codice', 'nome', 'telefono', 'indirizzo', 'fax', 'email', 'denominazione', 'cap', 'citta', 'provincia', 'sitoweb');
		$d=array($_POST['cod_reg'], $_POST['codice'], $_POST['nome'], $_POST['telefono'], $_POST['indirizzo'], $_POST['fax'], $_POST['email'], $_POST['denominazione'], $_POST['cap'], $_POST['citta'], $_POST['provincia'], $_POST['sitoweb']);
		autoinsert('Ospedale',$c,$d);
		echo "<dl><dt>Scuola {$_POST['nome']} inserita con sucesso!<br /> 
        Per autenticarsi bisogna inserire {$_POST['cod_amm']}@{$_POST['cod_reg']} e la vostra password scelta<br />
        <a href=\"Login.php\">Accedi al registro</a><br><br>Per importare dati da un registro precedente cliccare  <a href=\"InstallerImport.php?cod_reg={$_POST['cod_reg']}\">qui</a></dt></dl>";
	 }

    } 
  }
}


?>
<div class="main">
<h2>Installazione registro scuola ospedaliera</h2>
<br />
<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
<input type="hidden" name="inserisci" value="1" />
<table border=0>
<tr>
 <th>Codice scuola</th>
 <td><input class="obb" type="text" name="codice" maxlength="10" size="10" alt="Inserire il codice della scuola" /></td></tr>
<tr>
 <th>Nome</th>
 <td><input class="obb" type="text" name="nome" maxlength="30" size="30" alt="Inserire il nome della scuola" /></td></tr>
<tr>
 <th>Denominazione</th>
 <td><input type="text" name="denominazione" maxlength="50"  size="50" alt="Inserire la denominazione della scuola" /></td></tr>
<tr>
 <th>Indirizzo</th>
 <td><input class="obb" type="text" name="indirizzo" maxlength="30" size="30" alt="Inserire l'indirizzo della scuola" /></td></tr>
<tr>
 <th>CAP</th>
 <td><input type="text" name="cap" maxlength="5" size="5" alt="Inserire il CAP della scuola" /></td></tr>
<tr> 
 <th>Citta</th>
 <td><input class="obb" type="text" name="citta" maxlength="30" size="30" alt="Inserire la citt&agrave; della scuola" /></td></tr>
<tr> 
 <th>Provincia</th>
 <td><input class="obb" type="text" name="provincia" maxlength="2" size="3" alt="Inserire la provincia della scuola" /></td></tr>
<tr>
 <th>Telefono</th>
 <td><input type="text" name="telefono" maxlength="20" size="20" alt="Inserire il telefono della scuola" /></td></tr>
<tr>
 <th>Fax</th>
 <td><input type="text" name="fax" maxlength="20" size="20" alt="Inserire il numero di fax della scuola" /></td></tr>
<tr>
 <th>Indirizzo di posta elettronica</th>
 <td><input type="text" name="email" maxlength="50" size="50" alt="Inserire l'email della scuola" /></td></tr>
<tr>
 <th>Sito web</th>
 <td><input type="text" name="sitoweb" maxlength="50" size="50" alt="Inserire l'URL del sito web della scuola" /></td></tr>
<tr>
 <th>Codice registro</th>
 <td><input class="obb" type="text" name="cod_reg" maxlength="15" size="15" alt="Inserire il codice di accesso al registro" /> <small><em>nome del dominio</em></small></td></tr>
<tr>
 <th>Cognome amministratore</th>
 <td><input class="obb" type="text" name="cognome_amm" maxlength="30" size="30" alt="Inserire il cognome dell'amministratore" /></td></tr>
<tr> 
 <th>Nome amministratore</th>
 <td><input type="text" name="nome_amm" maxlength="30" size="30" alt="Inserire il nome dell'amministratore" /></td></tr>
<tr>
 <th>Codice amministratore</th>
 <td><input class="obb" type="text" name="cod_amm" maxlength="10" size="10" alt="Inserire il codice dell'amministratore del registro" /></td></tr>
<tr>
 <th>Password amministratore</th>
 <td><input class="obb" type="password" name="pwd_amm" maxlength="10" size="10" alt="Inserire la password dell'amministratore del registro" /></td></tr>
<tr>
 <th>Reinserire Password</th>
 <td><input class="obb" type="password" name="pwd_amm2" maxlength="10" size="10" alt="Inserire la password dell'amministratore del registro" /></td></tr>
<tr>
 <td colspan="2" align="center"><br /><br/><br/><input type="submit" value="Installa" /></td></tr></table></form>

 </div>
 </body>
</html>
