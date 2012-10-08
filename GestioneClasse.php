<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  CompilaRegistro.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: consente a amministratore e operatore di comporre i gruppi 
// di lavoro inserendo o togliendo studenti; ai docenti appartenenti ad un 
// gruppo di lavoro consente di accedere al registro, al prospetto scolastico
// ed ai documenti di programmazione degli studenti.
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore, operatore, docente (solo se 
// insegna in quel gruppo di lavoro)
// ----------------------------------------------------------------------
	
$title = "Gestione elenchi studenti per gruppo di lavoro";
include "Testa.inc";
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN,ID_OPERATORE,ID_OSPEDALIERO,ID_AFFIDATARIO, ID_DOMICILIARE);

echo "<h2>Gestione gruppi di lavoro</h2>";
// eliminazione studente dal gruppo di lavoro (campo attivo=N)

if (isset($_POST['cancella']))
{
	 if (($RUOLO != ID_ADMIN) && ($RUOLO != ID_OPERATORE))
		  print "<dl><dd>Funzione non accessibile con il profilo <b>".$RUOLO."</b></dd></dl>";
	 else
	 {
		  $sql =& $link->query("UPDATE Classe SET attivo='N' WHERE (id_degenza= ? ) AND	(id_classe= ? )", array($_POST['id_degenza'],$_POST['id_classe']));
		  errore_DB($sql);
	 }
}
	
// inserimento studente nel gruppo di lavoro
	
if (isset($_POST['inserisci']))
{
	 if (($RUOLO != ID_ADMIN) && ($RUOLO != ID_OPERATORE))
		  print "<dl><dd>Funzione non accessibile con il profilo <b>".$RUOLO."</b></dd></dl>";
	 else
	 {
		  $sql =& $link->query("INSERT INTO Classe (id_classe , id_degenza)	VALUES ( ? , ? )", array($_POST['id_classe'],$_POST['id_degenza']));
		  errore_DB($sql);
	 }
}

if (isset($_POST['inserisci_non_degente']))
{
	 if (($RUOLO != ID_ADMIN) && ($RUOLO != ID_OPERATORE))
		  print "<dl><dd>Funzione non accessibile con il profilo <b>".$RUOLO."</b></dd></dl>";
	 else
	 {
		  $sql =& $link->query("INSERT INTO Classe (id_classe , id_degenza)	VALUES ( ? , -1 )", array($_POST['id_classe']));
		  errore_DB($sql);
	 }
}

print "<table border=0>";
print "<tr>";
	// selezione gruppo di lavoro

if (!isset($_POST['id_classe']))
{ 
	 print '<form action="'.$_SERVER['PHP_SELF'].'" method="post"><tr><th>Gruppo di lavoro</th><td><select name="id_classe" >'; 
   	 if ($RUOLO == ID_OPERATORE) {
		 $sql =& $link->query("SELECT id_classe,classe,ordine FROM Classi WHERE id_classe in (select distinct id_classe from CdC where id_utente = $CODICE_UTENTE) ORDER BY classe");
	 }
	 else
		$sql =& $link->query("SELECT id_classe,classe,ordine FROM Classi ORDER BY classe");
	 errore_DB($sql);
	 while ( $id_classe  =& $sql->fetchRow())
	 {
		  print '<option value="'.$id_classe['id_classe'].'">'.$id_classe['classe'];
		  switch ($id_classe['ordine'])
		  { 
		  case "i": print " (scuola dell'infanzia)"; break;
		  case "1": print " (scuola primaria)"; break;
		  case "2": print " (scuola secondaria di primo grado)"; break;
		  case "s": print " (scuola secondaria)"; break;
		  }
		  print "</option>";
	 }
	 print "</select>	</td></tr> ".
		  '<tr> <td align="center"> <input type="submit" value="Seleziona"></td></tr>'.
		  "</table></form>";
}
else
{
 	 // verifica accesso alla classe da parte di quel docente
	 if ($RUOLO==ID_OSPEDALIERO || $RUOLO==ID_AFFIDATARIO || $RUOLO==ID_DOMICILIARE)
	 {
		  include("autenticazione_db.php"); 
		  autorizza_docente_classe ($_POST['id_classe'],$CODICE_UTENTE);	
	 }
	 // visualizza gruppo di lavoro
	 $sql =& $link->query("SELECT * FROM Classi WHERE (id_classe= ? )",$_POST['id_classe']);
	 errore_DB($sql); 
	 echo "<table class=\"elenco\">";
	 while ( $classe =& $sql->fetchRow())
	 {
		  print '<tr> <td bgcolor="#C1DADF" align="center" > Ordine </td>';
		  print '<td bgcolor="#DDDDDD" >';
		  switch ($classe['ordine'])
		  { 
		  case "i": print "Scuola dell'infanzia</td>"; break;
		  case "1": print "Scuola primaria</td>"; break;
		  case "2": print "Scuola secondaria di primo grado</td>"; break;
		  case "s": print "Scuola secondaria</td>"; break;
		  }
		  print "</tr>";	
		  print '<tr> <td bgcolor="#C1DADF" align="center" > Gruppo di lavoro </td>';
		  print '<td bgcolor="#DDDDDD" >'; print $classe['classe']; 
	 }
	 print "</table>";
	  if (($RUOLO==ID_DOMICILIARE)|| ($RUOLO==ID_AFFIDATARIO )){
	 		$sql =& $link->query("
			SELECT DISTINCT (Degenze.id_degenza) as id_degenza,nome,cognome,Studenti.id_studente,id_classe 
			FROM Studenti, Classe, Degenze WHERE 
			Studenti.id_studente = Degenze.id_studente 
			AND Classe.id_degenza=Degenze.id_degenza
			AND (Classe.id_classe = ? )
			AND attivo = 'S'
			and Studenti.id_studente in (select distinct id_studente from Esterni where id_utente = $CODICE_UTENTE )
      ORDER BY cognome", $_POST['id_classe'] );
  	} 
		else{
		  $sql =& $link->query("SELECT * FROM Studenti, Classe, Degenze, Reparto WHERE 
		  Studenti.id_studente = Degenze.id_studente 
		  AND Classe.id_degenza=Degenze.id_degenza 
		  AND Reparto.id_degenza=Degenze.id_degenza
		  AND (Classe.id_classe = ? )
		  AND Reparto.attivo = 'S'
		  AND Degenze.data_fine='0000-00-00' ORDER BY cognome", $_POST['id_classe']);
			}
	     errore_DB($sql);	
		  $flag=1;
		  $num_righe=$sql->numRows();
		  while ($riga =& $sql->fetchRow())
		  {
		  if($flag){
		  print "<h3>Elenco studenti<br>Numero studenti: ".$num_righe."</h3>";
		  print "<table class=\"elenco\">";
		  print "<tr>";
		  print '<th>Cognome</th>';
		  print '<th>Nome </th>';
		  print '<th>Classe </th>';
		  print '<th>Ordine </th>';
		  print '<th>Straniero </th>';
		  print '<th>HC </th>';
		  print '<th>Ripetente </th>';
		  print '<th>Scuola </th>';
		  if (($RUOLO == ID_ADMIN) || ($RUOLO == ID_OPERATORE) || ($RUOLO==ID_OSPEDALIERO))
		  {
				print '<th>Reparto </th>';
				print '<th>Tipo di degenza </th>';
				print '<th>Inizio frequenza </th>'; // bottone modifica/cancellazione
		  }
		  
		  if (($RUOLO==ID_ADMIN) || ($RUOLO == ID_OPERATORE))
				print '<th colspan="7">Azioni </th></tr>';
		  elseif (($RUOLO==ID_OSPEDALIERO) || ($RUOLO==ID_AFFIDATARIO) || ($RUOLO==ID_DOMICILIARE))
				print '<th colspan="5">Azioni </th></tr>';
		  else  //insegnante affidatario o genitore
				print '<th colspan="3">Azioni </th></tr>';
		  $pari=1;
		  $flag=0;
		  }
				$class = ($pari) ? "pari" : "dispari";
				$pari=1-$pari;
				switch($riga['ordine']) {
  					case 'M': $ordine='Scuola dell\'infanzia'; break;
  					case 'P': $ordine='Scuola primaria'; break;
  					case 'I': $ordine='Scuola secondaria di primo grado'; break;
  					case 'S': $ordine='Scuola secondaria';
 				 }
				print "<tr class='$class'>";
				print "<td nowrap='nowrap'>".$riga['cognome']."</td>";
				print "<td nowrap='nowrap'>".$riga['nome']."</td>";
				print "<td nowrap='nowrap'>".$riga['classe']."</td>";
				print "<td nowrap='nowrap'>".$ordine."</td>";
				$straniero = ($riga['straniero'] == "0") ? "NO" : "SI";
				print "<td nowrap='nowrap'>".$straniero."</td>";
				$HC = ($riga['HC'] == "0") ? "NO" : "SI";
				print "<td nowrap='nowrap'>".$HC."</td>";
				$ripetente = ($riga['ripetente'] == "0") ? "NO" : "SI";
				print "<td nowrap='nowrap'>".$ripetente."</td>";
				//Scuola di appartenenza
				$sql_scuola =& $link->query("SELECT nome, Scuola.id_scuola FROM Scuola,Scuole WHERE (Scuola.id_studente = ?  AND Scuola.tipo='p' AND Scuola.id_scuola=Scuole.id_scuola)", $riga['id_studente']);
				errore_DB($sql_scuola); 
	   		$riga_scuola =& $sql_scuola->fetchRow();
  				print "<td nowrap='nowrap'>".$riga_scuola['nome']."</td>";						

				// Informazioni sulla degenza
				if (($RUOLO == ID_ADMIN) || ($RUOLO == ID_OPERATORE) || ($RUOLO==ID_OSPEDALIERO))
				{							
					 $sql_reparto =& $link->query("SELECT Reparto.* FROM Reparto,Degenze WHERE (Reparto.id_degenza = ?) AND Reparto.attivo='S'", $riga['id_degenza']);
					 errore_DB($sql_reparto);
					 $reparto =& $sql_reparto->fetchRow();	 
					 $sql_reparti =& $link->query("SELECT Reparti.nome FROM Reparti WHERE id_reparto= ? ", $reparto['id_reparto']);
					 errore_DB($sql_reparti);
					 $riga_reparti =& $sql_reparti->fetchRow(); 
					 print "<td nowrap='nowrap'>".$riga_reparti['nome']."</td>";
					 print "<td nowrap='nowrap'>";
					 if ($reparto['tipo_degenza']=='DH')  print "Day Hospital</td>";
					 elseif ($reparto['tipo_degenza']=='DO')  print "Degenza ordinaria</td>"; 
					 print "<td nowrap='nowrap'>";
					 $data_inizio = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['data_inizio']);
					 print $data_inizio."</td>";
				} 
				
				// Informazioni complete studente 
				print "<td nowrap='nowrap'>";
				print '<form method="post" action="VisualizzaStudente.php">';
    		 	print "<input type='hidden' name='id' value='".$riga['id_studente']."'>";
    			print '<input type="image" src="./immagini/button_index.png" alt="Informazioni complete studente" title="Informazioni complete studente">';
   			print " </form></td>";
   			
   			// Informazioni complete scuola di appartenenza studente 
				print "<td nowrap='nowrap'>";
				print '<form method="post" action="VisualizzaScuola.php?id='.$riga_scuola['id_scuola'].'">';
    			print '<input type="image" src="./immagini/scuola.png" alt="Informazioni complete scuola" title="Informazioni complete scuola">';
   			print " </form></td>";

				// Compilazione registro
				if (($RUOLO==ID_ADMIN) || ($RUOLO == ID_OPERATORE) || ($RUOLO==ID_OSPEDALIERO) || ($RUOLO==ID_AFFIDATARIO)  || ($RUOLO==ID_DOMICILIARE))
				{
					 print "<td nowrap='nowrap'>";
										
					 if (!isset($_POST['id_materia'])) 
					    print "<form method=\"post\" action=\"CompilaRegistro.php\">".
						  "<input type=\"hidden\" name=\"id_degenza\" value=\"{$riga['id_degenza']}\" />".
						  "<input type=\"hidden\" name=\"id_studente\" value=\"{$riga['id_studente']}\" />".
						   "<input type=\"hidden\" name=\"id_classe\" value=\"{$_POST['id_classe']}\" />".
						  "<input type=\"image\" src=\"./immagini/certificato.png\" alt=\"Compila registro\" title=\"Compila registro\"/></form></td>";
					 else 
					    print "<form method=\"post\" action=\"CompilaRegistro.php\">".
						  "<input type=\"hidden\" name=\"id_degenza\" value=\"{$riga['id_degenza']}\" />".
						  "<input type=\"hidden\" name=\"id_studente\" value=\"{$riga['id_studente']}\" />".
						   "<input type=\"hidden\" name=\"id_materia\" value=\"{$_POST['id_materia']}\" />".
						   "<input type=\"hidden\" name=\"id_classe\" value=\"{$_POST['id_classe']}\" />".
						  "<input type=\"image\" src=\"./immagini/certificato.png\" alt=\"Compila registro\" title=\"Compila registro\"/></form></td>";

				}
				// Prospetto scolastico
				print "<td nowrap='nowrap'>";
				print "<form method=\"post\" action=\"ProspettoScolastico.php\">".
					 "<input type=\"hidden\" name=\"id\" value=\"{$riga['id_studente']}\" />".
					 "<input type=\"image\" src=\"./immagini/prospetto.png\" alt=\"Prospetto scolastico\" title=\"Prospetto scolastico\"/></form></td>";
				print "<td nowrap='nowrap'>";
				print "<form method=\"post\" action=\"DocumentiProgrammazione.php\">".
					 "<input type=\"hidden\" name=\"id_studente\" value=\"{$riga['id_studente']}\" />".
					 "<input type=\"image\" src=\"./immagini/fileopen.png\" alt=\"Documenti programmazione\" title=\"Documenti programmazione\"/></form></td>";
		  
		  	// Eliminazione dal gruppo di lavoro
				if (($RUOLO==ID_ADMIN) || ($RUOLO == ID_OPERATORE))
				{				
					 print "<td nowrap='nowrap'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
					 print "<td nowrap='nowrap'>";
					 print "<form method=\"post\" action=\"{$_SERVER['PHP_SELF']}\">".
						  "<input type=\"hidden\" name=\"id_classe\" value=\"{$riga['id_classe']}\" />".
						  "<input type=\"hidden\" name=\"id_degenza\" value=\"{$riga['id_degenza']}\" />".
						  "<input type=\"hidden\" name=\"id_studente\" value=\"{$riga['id_studente']}\" />".
						  "<input type=\"hidden\" name=\"cancella\" value=\"1\" />".
						  "<input type=\"image\" src=\"./immagini/button_drop.png\" alt=\"Cancella\" title=\"Cancella\"/></form></td>";
				}
		  print "</tr>";

			}
		  print "</table>";
	 if (($RUOLO==ID_ADMIN) || ($RUOLO == ID_OPERATORE))
	 {				
		  // modifica gruppo di lavoro: possibile inserimento/cancellazione
		  $titolo=1;
		  $sql =& $link->query("SELECT * FROM Studenti, Degenze, Reparto WHERE Studenti.id_studente = Degenze.id_studente AND Reparto.attivo='S' AND Reparto.id_degenza=Degenze.id_degenza");
		  errore_DB($sql);
		  $num = $sql->numRows();
		  if(!$num)
		  {
		  echo "<dl><dd>Non ci sono studenti associabili.<br/>Possibili Cause:<ul><li>Nessuno studente inserito</li><li>Nessuno studente in degenza</li><li>Tutti gli studenti in degenza sono gi&agrave; stati inseriti in altri gruppi di lavoro</li></ul></dd></dl>";
		  }
		  else{
		  while ($riga =& $sql->fetchRow())
		  {
				$sql_attivo=& $link->query("SELECT * FROM Classe WHERE Classe.id_degenza= ? ", $riga['id_degenza']);
				errore_DB($sql_attivo);
				$num_attivo = $sql_attivo->numRows();
				if ($num_attivo == 0) 
				{
					 if ($titolo)
					 {
						  print "<h3>Inserimento nuovi studenti nel gruppo di lavoro</h3>";
						  print "<table class=\"elenco\">";
						  print "<tr>";
						  print '<th>Cognome</th>';
						  print '<th>Nome </th>';
						  print '<th>Reparto </th>';
						  print '<th>Tipo degenza</th>';
						  print '<th>Azione </th>';
						  $titolo=0;
					 }
					 print '<form action="GestioneClasse.php" method="POST">';
					 print '<input type="hidden" name="id_studente" value="'; print $riga['id_studente']; print '">';				
					 print '<input type="hidden" name="id_classe" value="'; print $_POST['id_classe']; print '">';
					 print '<input type="hidden" name="id_degenza" value="'; print $riga['id_degenza']; print '">';
					 print '<input type="hidden" name="inserisci" value="1">';
					 print '<tr><td bgcolor="#DDDDDD" nowrap="nowrap">';
					 print "{$riga['cognome']}</td>";
					 print '<td bgcolor="#DDDDDD" nowrap="nowrap">';
					 print "{$riga['nome']}</td>";
					 print '<td bgcolor="#DDDDDD" nowrap="nowrap">';
					 $sql_reparti = $link->query("SELECT * FROM Reparti WHERE id_reparto= ? ", $riga['id_reparto']);
					 errore_DB($sql_reparti);
					 $reparto  =  $sql_reparti->fetchRow();
					 print $reparto['nome'];
					 print "</td>";
					 print '<td bgcolor="#DDDDDD" nowrap="nowrap">';
					 switch ($riga['tipo_degenza'])
					 { 
					 case "DH": print 'Day Hospital'; break;
					 case "DO": print 'Degenza ordinaria'; break;
					 default: print '';
					 }
					 print '</td><td><div align="center"><input type="submit" value="aggiungi"></div></td></tr>';
					 print '</form>';
					 print "</td>";
				}
		  }
		  }
		  print "</table>";
	 }	 
	         if ($profile==ID_OSPEDALIERO || $profile==ID_DOMICILIARE)   
                $up="Registro.php";
        elseif($profile==ID_OSSERVATORE||$profile==ID_AFFIDATARIO)
                $up="indice_aff.php";
if(isset($up)) echo "<br><br><center><a href=\"$up\"><img src=\"immagini/menu.png\"></a><br>Pagina Iniziale</center>";

	 include "Coda.inc";
}
?>
