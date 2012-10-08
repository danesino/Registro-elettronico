<?php

/*------------------------------------------------------------------------
  autenticazione.php
  Ferdinando Ricchiuti
	
  Questo file implementa le funzionalita' di autenticazione e
  autorizzazione del registro elettronico.
------------------------------------------------------------------------*/


/*-----------------------------------------------------------------------
  Configurazione
------------------------------------------------------------------------*/

include "/etc/registro.conf";
include "DefRuoli.inc";

/* La variabile RETRY viene letta da sessione e poi da richiesta.
   Da sessione, serve per gestire il login. Da richiesta serve
   per far dimenticare (logout) la password al browser.*/
if (isset($_SESSION['retry_auth'])) $RETRY=$_SESSION['retry_auth'];
else $RETRY=0;
$_SESSION['retry_auth']=($RETRY)?0:1;

/*------------------------------------------------------------------------
  Definizione delle funzioni.
------------------------------------------------------------------------*/

/* 
  errore_autenticazione($messaggio) 
  
  Questa funzione visualizza una pagina inserendo il messaggio di
  errore specificato. Fatto questo termina lo script.
  E' una funzione ad utilizzo interna a questo modulo.
*/
function errore_autenticazione($messaggio)
{
	 $MYSELF=$_SERVER['PHP_SELF'];
	 print "<dl><dd>$messaggio<br /><br /><a href='.'>Riprova</a></dd></dl></div>";
	 exit(1);
} 
//* errore_autenticazione() */


/*
  autorizza_ruoli($ruolo[,...$ruolo])
  
  Se l'utente attuale non ha nessuno dei ruoli indicati
  in questa funzione, lo script termina con una pagina di errore.
*/
function autorizza_ruoli()
{
  global $RUOLO;
  $nr=func_num_args();
  for ($i=0;$i<$nr;$i++)
    if ($RUOLO==func_get_arg($i)) return;
    
  errore_autenticazione("Il ruolo attuale non permette l'accesso a questa funzione.");
} /* autorizza_ruoli() */



/*------------------------------------------------------------------------
  MAIN
  Corpo principale del modulo di autenticazione. Questo
  si occupa di verificare le credenziali presentate dall'utente.
------------------------------------------------------------------------*/	

/* Controllo se https e' attivo */
//if (@$_SERVER['HTTPS'] != 'on') 
  //errore_autenticazione('Il sistema di protezione HTTPS non risulta attivo.<br />Forse l\'URI &egrave; incorretto prova https:// al posto di http://');
  
/* Verifico se le variabili sono state esportate */
//if (!isset($_SERVER['SSL_CLIENT_VERIFY']))
  //errore_autenticazione('Impossibile verificare i parametri di protezione HTTPS.');

/* Verifica delle credenziali */
//if ($_SERVER['SSL_CLIENT_VERIFY'] == 'SUCCESS' || 
  //  $_SERVER['SSL_CLIENT_VERIFY'] == 'GENEROUS')
//{
  /* L'utente ha presentato un certificato */
  //if ($_SERVER['SSL_CLIENT_S_DN_CN']=='')
    //errore_autenticazione('Il certificato non contiene il nome utente.');
    
  //list ($UID,$REG)=explode('@',$_SERVER['SSL_CLIENT_S_DN_CN']);

  //if (!include("/var/Scuole/$REG/ConfigurazioneRegistro_$REG.php"))
    //errore_autenticazione("Il registro '$REG', contenuto nel certificato, non esiste");
 // else
  //{
    //include_once "/var/Scuole/$REG/ConfigurazioneRegistro_$REG.php";
  //}

  //$r =& $link->query("SELECT * from Utenti WHERE username='$UID'");
//    if ($elenco->numRows())  exit("<h4>Registro - Scuola {$dati->nome} gi&agrave; presente</h4>");

  //if (PEAR::isError($r))
    //errore_autenticazione('Errore del database: '.mysql_error());
  //elseif ($r->numRows()!=1)
    //errore_autenticazione("L'utente specificato nel certificato non esiste.");
  //else $r->fetchInto($obj);
//}
//else
//{
  /* L'utente non ha presentato il certificato */
  
  if ($RETRY || !isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']))
  {
    /* Senza utente/password occorre scatenare l'apposita finestra */
	
    if ($RETRY==0) $_SESSION['retry_auth']=0;
    header('WWW-Authenticate: Basic realm="registro"');
    header('HTTP/1.0 401 Unauthorized');
    header('status: 401 Unauthorized');  
    errore_autenticazione('Occorre specificare un utente e una password.');
  }
  
  /* Verifico ed includo il file di configurazione del registro */   
  if (!preg_match('/[\w\d]+\@[\w\d]+/',$_SERVER['PHP_AUTH_USER']))
    errore_autenticazione("La sintassi del nome utente risulta non valida");

  /* Il codice utente e' composto da due parti separate dal
     dal carattere @. La prima parte e' lo uid la seconda invece
     risulta essere il codice del registro. */
  list ($UID,$REG)=explode('@',$_SERVER['PHP_AUTH_USER']);
  
  if (@!include("/var/Scuole/$REG/ConfigurazioneRegistro_$REG.php"))
    errore_autenticazione("Il registro '$REG' non esiste");

  /* Occorre ora effettuare la verifica della password su DB */
  $pwd=sha1($_SERVER['PHP_AUTH_PW']);
  $r =& $link->query("SELECT * from Utenti WHERE username='$UID' AND pwd='$pwd'");
  
  if (PEAR::isError($r))
    errore_autenticazione('Errore del database: '.mysql_error());
  if ($r->numRows()!=1)
    errore_autenticazione('Nome utente o password non corretti '.$UID);
    $r->fetchInto($obj);
//}

unset($_SESSION['retry_auth']);

/* VARIABILI UTILIZZABILI */
$RUOLO=$obj['id_ruolo'];     		// Codice del ruolo dell'utente
$NOME=$obj['nome'];         		// Nome dell'utente
$COGNOME=$obj['cognome'];   		// Cognome dell'utente 
$CODICE_UTENTE=$obj['id_utente'];   	// Codice dell'utente 

/* Compatibilita' verso la versione originale */
$profile=$RUOLO;
$codice=$REG;
$codice_utente=$CODICE_UTENTE;

?>
