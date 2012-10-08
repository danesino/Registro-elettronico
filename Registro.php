<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  Registro.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: selezione del registro elettronico che si desidera
// compilare
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore, operatore e docente di 
// quello studente
// ----------------------------------------------------------------------

$title = "Registro elettronico";

include "Testa.inc";
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN,ID_OPERATORE,ID_OSPEDALIERO,ID_AFFIDATARIO,ID_DOMICILIARE);

print "<h2>Gestione gruppi di lavoro</h2>\n";
print "<table border=0>\n";
print "<tr>\n";

// selezione gruppo di lavoro
if (!isset($POST['id_classe']))
{
	print '<form action="GestioneClasse.php" method="post">';
	print '<tr><th> Gruppo di lavoro </th>';
	print '<td><select name="id_classe" >';
  $sql =& $link->query("SELECT DISTINCT id_classe,id_utente FROM CdC WHERE (CdC.id_utente= ? )",$CODICE_UTENTE);
	errore_DB($sql);
  while ( $classe =& $sql->fetchRow())
	{
		print_r($classe);
		print '<option value="'.$classe['id_classe'].'">';
		$sql_classe =& $link->query("SELECT * FROM Classi WHERE (id_classe= ? )",$classe['id_classe']);
    $id_classe =& $sql_classe->fetchRow();
		print $id_classe['classe'];
		print "&nbsp;&nbsp;&nbsp;&nbsp;";
		switch ($id_classe['ordine'])
		{ 
			case "i": print "(scuola dell'infanzia)"; break;
			case "1": print "(scuola primaria)"; break;
			case "2": print "(scuola secondaria di primo grado)"; break;
			case "s": print "(scuola secondaria)"; break;
		}
		print "</option>";
  }
  print "</select>	</td></tr> ";
  print '<tr><td><input type="submit" value="Seleziona"></form></td></tr>';
	print "</table>";
}
else
{	
	if ($RUOLO==ID_OSPEDALIERO)
	{
	// Verifica autorizzazione da parte del docente a insegnare in quella classe
		include("autenticazione_db.php"); 
		autorizza_docente_classe ($_POST['id_classe'],$CODICE_UTENTE);
	}
		
	// visualizza gruppo di lavoro
	   	
	$sql =& $link->query("SELECT * FROM Classi WHERE ( id_classe = ? )", $_POST['id_classe']);
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
	  print '<td bgcolor="#DDDDDD" >'.$classe['classe']; 
	  }
	  print "</table>";
	  $sql =& $link->query("SELECT * FROM Studenti, Classe,Degenze WHERE (Studenti.id_studente = Degenze.id_studente AND Classe.id_degenza=Degenze.id_degenza AND ( Classe.id_classe = ? ) AND Classe.attivo='S' AND Degenze.data_fine='0000-00-00')", $_POST['id_classe']);
	  $num_righe =& $sql->numRows(); 
      if ($num_righe!=0)
	 {
		print "<h3>Elenco studenti:</h3>\n";
		print "<table border=0>\n";
		print "<tr>\n";
		print '<th>Cognome</th>';
		print '<th>Nome </th>';
		print '<th>Reparto </th>';
		print '<th>Tipo di degenza </th>';
		print '<th>Inizio frequenza </th>'; 
		print '<th>Registro </th></tr>';
		$pari=1;
		while ($riga =& $sql->fetchRow())
		{
			$class = ($pari) ? "pari" : "dispari";
			$pari=1-$pari;
			print '<tr class="'.$class.'"><td nowrap="nowrap">';
			print $riga['cognome']."</td>\n";
			print "<td nowrap='nowrap'>\n";
			print $riga['nome']."</td>\n";
			$sql_reparto =& $link->query("SELECT Reparto.* FROM Reparto,Degenze WHERE (Reparto.id_degenza = ? AND Reparto.attivo='S')", $riga['id_degenza']);
			$reparto =& $sql_reparto->fetchRow(); 
			$sql_reparti =& $link->getOne("SELECT Reparti.nome FROM Reparti WHERE (id_reparto= ? )", $reparto['id_reparto']);
			print "<td nowrap='nowrap'>";
			print $sql_reparti."</td>\n";
	    
			if ($reparto['tipo_degenza']=='DH') print "Day Hospital</td>\n";
			elseif ($reparto['tipo_degenza']=='DO') print "Degenza ordinaria</td>\n"; 
				
			print "<td nowrap='nowrap'>";
			$data_inizio = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['data_inizio']);
			print $data_inizio."</td>\n";
				
			print "<td nowrap='nowrap'>";
			print '<a href="CompilaRegistro.php?id_registro='.$riga['id_registro']."&id_studente=".$riga['id_studente'].'">';
			print '<img hspace="7" width="12" height="13" src="./immagini/certificato.png" alt="Compila registro" title="Compila registro" border="0"></a>';
			print "</td></tr>";		
	    }
	    print "</table>\n";
  }
		
}

	
if (($RUOLO==ID_ADMIN)||($RUOLO==ID_OPERATORE))
	$up="index";
else if ($profile==ID_OSPEDALIERO)
		$up="indice_doc.php";
	else
		$up="indice_aff.php";
if(isset($up)) echo "<br><br><center><a href=\"$up\"><img src=\"immagini/menu.png\"></a><br>Pagina Iniziale</center>";
include "Coda.inc";
?>
