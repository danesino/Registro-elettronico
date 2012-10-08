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
// Nome file:  autenticazione_db.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: verifica l'autorizzazione ad operare su 
// specifici studenti del registro elettronico.
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore
// ----------------------------------------------------------------------


/* Verifica che un utente possa accedere alle informazioni di un particolare studente */
 
 function autorizza_affidatario_genitore($id_studente,$codice_utente) 
 {	
	global $link;
  $sql =& $link->query("SELECT * FROM Esterni WHERE (id_utente= ? ) AND (id_studente= ? )", array($codice_utente, $id_studente));
	if  (!$sql->fetchRow())
	 	errore_autenticazione("Utente non autorizzato a questa funzione.");
 }
  
 /* Verifica che un docente possa compilare il registro di un particolare
    studente: deve essere in una classe in cui insegna  */
    
 function autorizza_docente_degenza ($id_degenza,$codice_utente)
 {
	global $link;
	// classe in cui è inserito lo studente
	$sql =& $link->query("SELECT id_classe FROM Classe WHERE (id_degenza= ? )",$id_degenza);
	$classe =& $sql->fetchRow(); 
		
	// classi in cui è abilitato ad insegnare quel docente

	$sql =& $link->query("SELECT id_classe FROM CdC WHERE (id_utente= ? )",$codice_utente);	
	$verifica_classe=false;
	while ( $classe_doc = $sql->fetchRow())
	{   if ($classe==$classe_doc) 
	   	$verifica_classe=true;
 	}
	if (!$verifica_classe)
	 	errore_autenticazione("Docente non autorizzato a questa funzione.");
 }
 
  /* Verifica che un docente possa compilare il registro di un particolare
    studente: deve essere in una classe in cui insegna  */
    
 function autorizza_docente_registro ($id_degenza,$codice_utente)
 {
	global $link;
	// classe in cui è inserito lo studente
	$sql =& $link->query("SELECT id_classe FROM Classe WHERE (id_degenza= ? )",$id_degenza);
	$classe =& $sql->fetchRow();
		
	// classi in cui è abilitato ad insegnare quel docente
	$sql =& $link->query("SELECT id_classe FROM CdC WHERE (id_utente= ? )",$codice_utente);
	$verifica_classe=false;
	while ( $classe_doc =& $sql->fetchRow())
	{   if ($classe==$classe_doc) 
	   	$verifica_classe=true;
 	}
	if (!$verifica_classe)
		 errore_autenticazione("Docente non autorizzato a questa funzione.");
 }
 
 /* Verifica che un docente possa compilare il registro di un particolare
    studente: deve essere in una classe in cui insegna  */
    
 function autorizza_docente_studente ($id_studente,$codice_utente)
 {
	// verifica che il docente abbia insegnato in una classe in cui è stato 
	// inserito lo studente
	
	$sql="SELECT * FROM Degenze, Classe, CdC WHERE Degenze.id_degenza=Classe.id_degenza AND	CdC.id_utente=".$codice_utente." AND
		CdC.id_classe=Classe.id_classe AND
		Degenze.id_studente=".$id_studente;
	$query = mysql_query($sql);
	if (!mysql_fetch_object($query))
	 	errore_autenticazione("Docente non associato allo studente.");
 }
 
 // autorizza un docente ad esaminare le informazioni relative ad una classe
 
  function autorizza_docente_classe ($id_classe,$codice_utente)
 {
	// verifica che il docente abbia insegnato in quella classe
	
	$sql="SELECT * FROM CdC WHERE 
		id_utente=".$codice_utente." AND
		id_classe=".$id_classe;
	$query = mysql_query($sql);
	if (!mysql_fetch_object($query))
	 	errore_autenticazione("Docente non associato a questa Classe.");
 }
?>
