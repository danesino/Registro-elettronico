<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  GestioneCdC.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: consente di comporre i consigli di classe associati ad un
// gruppo di lavoro inserendo o togliendo i docenti e le materie insegnate
// 22/7/09 Aggiunto ruolo docente (Sophia Danesino)
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore
// ----------------------------------------------------------------------
	
$title = "Gestione Consiglio di Classe";

include "Testa.inc";
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN);

echo "<h2>Assegnazione docenti al Consiglio di Classe</h2>";

	
if(isset($_POST['inserisci']))
{
	$sql =& $link->query("INSERT INTO CdC (id_classe , id_utente, id_materia ) VALUES ( ? , ? , ? )", array($_POST['id_classe'], $_POST['id_utente'], $_POST['id_materia']));
	errore_DB($sql);
}
	 
if (isset($_POST['cancella']))
{
	$sql =& $link->query("DELETE FROM CdC WHERE (id_classe= ? ) AND (id_utente= ? ) AND (id_materia= ? )", array($_POST['id_classe'], $_POST['id_utente'], $_POST['id_materia']));
  errore_DB($sql);
}
	
if (!isset($_POST['id_classe']))
{
	print '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<table><tr> <td bgcolor="#C1DADF" align="center" > Gruppo di lavoro </td>';
	print '<td><select name="id_classe">';
  $sql =& $link->query("SELECT * FROM Classi ORDER BY classe");
  while ( $id_classe =& $sql->fetchRow())
	{
		print '<option value="'.$id_classe['id_classe'].'">'.$id_classe['classe'];	
		switch ($id_classe['ordine'])
		{
			case "i": print "&nbsp;(Scuola dell'infanzia)"; break;
			case "1": print "&nbsp;(Scuola primaria)"; break;
			case "2": print "&nbsp;(Scuola secondaria di primo grado)"; break;
			case "s": print "&nbsp;(Scuola secondaria)"; break;
		}
		print "</option>\n";
  }
	mysql_close($link);

  print "</select></td></tr>\n";
  print '<tr><td><input type="submit" value="Seleziona"></td></tr>';
print "</table></form>";
}
else
{
	print '<table class="elenco"><tr><td bgcolor="#C1DADF" align="center" > Consiglio di classe </td>';
	$sql =& $link->query("SELECT * FROM Classi WHERE (id_classe= ? )", $_POST['id_classe']);
	$n = $sql->numRows();
	if(!$n)
	{
		 die("<dl><dd>Nessun Gruppo di lavoro inserito per inserirne uno cliccare <a href='InserimentoClasse.php'>qui</a></dd></dl>");
	}
	 while ( $classe =& $sql->fetchRow())
	{
  	print '<td bgcolor="#DDDDDD" ><strong>'.$classe['classe']."  ";
		switch ($classe['ordine'])
		{
			case "i": print "(Scuola dell'infanzia)"; break;
			case "1": print "(Scuola primaria)"; break;
			case "2": print "(Scuola secondaria di primo grado)"; break;
			case "s": print "(Scuola secondaria)"; break;
		}
		print "</strong></td></tr>";
	}
	print "</table>";

	$sql =& $link->query("SELECT * FROM Utenti, Ruoli, CdC	WHERE ( Utenti.id_ruolo=Ruoli.id_ruolo AND Utenti.id_utente = CdC.id_utente AND CdC.id_classe = ? ) ORDER BY cognome",$_POST['id_classe']);
	$num_righe = $sql->numRows();
	if ($num_righe!=0)
	{
		print "<h3>Composizione del consiglio di classe:</h3>";
		print "<table class=\"elenco\">";
		print "<tr>";
		print '<th>Cognome</th>';
		print '<th>Nome </th>';
		print '<th>Ruolo</th>';
		print '<th>Materia </th>';
		print '<th colspan="2">Azione</th></tr>';
		$pari=1;
		while ($riga =& $sql->fetchRow())
		{
			$class = ($pari) ? "pari" : "dispari";
			$pari=1-$pari;
			print "<tr class='$class'><td nowrap='nowrap'>".$riga['cognome']."</td>";
			print "<td nowrap='nowrap'>".$riga['nome']."</td>";
			print "<td nowrap='nowrap'>".$riga['descrizione']."</td>";
			print "<td nowrap='nowrap'>";
		 	$sql_materia =& $link->query("SELECT * FROM  Materie WHERE (Materie.id_materia= ? )", $riga['id_materia']);
			$riga_materia =& $sql_materia->fetchRow(); 
			print $riga_materia['nome']."</td>";
			print "<td nowrap='nowrap'>";
			print "<form method=\"post\" action=\"{$_SERVER['PHP_SELF']}\">".
            "<input type=\"hidden\" name=\"id_classe\" value=\"{$riga['id_classe']}\" />".
            "<input type=\"hidden\" name=\"id_utente\" value=\"{$riga['id_utente']}\" />".
            "<input type=\"hidden\" name=\"id_materia\" value=\"{$riga['id_materia']}\" />".
            "<input type=\"hidden\" name=\"cancella\" value=\"1\" />".
            "<input type=\"image\" src=\"./immagini/button_drop.png\" alt=\"Elimina\" title=\"Elimina dal consiglio di classe\"/></form></td>";
			print "</td></tr>";
		}
		print "</table>";
	}

	// possono far parte del Consiglio di Classe gli utenti con ruolo di docente, amministratore e operatore
	$sql =& $link->query("SELECT * FROM Utenti, Ruoli WHERE (Utenti.id_ruolo=Ruoli.id_ruolo AND Utenti.id_ruolo!=".ID_OSSERVATORE." ) ORDER BY cognome");
  errore_DB($sql);
	$titolo=1;
	while ($riga =& $sql->fetchRow()) 
	{
		if ($titolo)
		{
			print "<h3>Inserisci nuovo docente nel Consiglio di Classe</h3>";
			print "<table class=\"elenco\">";
			print "<tr>";
			print '<th>Cognome</th>';
			print '<th>Nome </th>';
			print '<th>Ruolo</th>';
			print '<th>Materia</th>';
			print '<th>Azione</th>';
			$titolo=0;
		}
		print '<form action="GestioneCdC.php" method="post">';
		print '<input type="hidden" name="id_classe" value="'.$_POST['id_classe'].'">';
		print '<input type="hidden" name="id_utente" value="'.$riga['id_utente'].'">';
		print '<input type="hidden" name="inserisci" value="1">';
		print '<tr><td bgcolor="#DDDDDD" nowrap="nowrap">'.$riga['cognome']."</td>";
		print '<td bgcolor="#DDDDDD" nowrap="nowrap">'.$riga['nome']."</td>";
		
		print '<td bgcolor="#DDDDDD" nowrap="nowrap">'.$riga['descrizione']."</td>";
		print '<td align="right">';
    
		$sql_materie =& $link->query("SELECT * FROM  Materie");
		$num_righe = $sql_materie->numRows();
		if ($num_righe!=0)
		{ 	
			print '<select name="id_materia">';
			while ($riga_materia =& $sql_materie->fetchRow()) 
			{   	
				$sql_mat =& $link->query("SELECT * FROM CdC WHERE CdC.id_utente= ? AND CdC.id_classe= ? AND	CdC.id_materia= ?", array($riga['id_utente'],$_POST['id_classe'],$riga_materia['id_materia']));
				if (!$sql_mat->fetchRow())
				{
					print '<option value="'.$riga_materia['id_materia'].'">'.$riga_materia['nome'].'</option>';
				}
			}
			print "</select></td>";
		}
		else
			print "Inserire prima le materie (Gestione materie)</td>"; 
		print '<td><input type="submit" value="aggiungi"></td></form>';
	}
	print "</table>"; 
}
$up="index";
include "Coda.inc";
?>
