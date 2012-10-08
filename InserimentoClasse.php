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
// Nome file:  FormInserimentoClasse.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: modulo per l'inserimento di un nuovo gruppo di lavoro
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore
// ----------------------------------------------------------------------
	
$title = "Inserimento gruppo di lavoro";

include "Testa.inc";
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN);

if(isset($_POST['classe']))
{
	if (empty($_POST['classe']))
  {
		print "<dl><dd>Errore: Campo nome gruppo di lavoro obbligatorio</dd></dl>";
	}
	else
	{
		$sql=& $link->query('SELECT * FROM Classi WHERE ( classe= ? ) AND ( ordine= ? )', array($_POST['classe'], $_POST['ordine']));
	   $num_righe = $sql->numRows();
  	if ($num_righe == 0)
  	{
			$sql =& $link->query("INSERT INTO Classi (classe, ordine) VALUES ( ? , ? )" , array($_POST['classe'], $_POST['ordine']));
			errore_DB($sql);
			die("<dl><dt>Inserimento class {$_POST['classe']} avvenuta con successo<br /><br />Per inserire un altra classe cliccare <a href=\"./InserimentoClasse.php\">qui</a></dt></dl>");
		}
		else
			print("<dl><dd>Gruppo di lavoro {$_POST['classe']} già presente</dd></dl>");
	}
}

?>
<h3>Inserimento nuovo gruppo di lavoro</h3>
<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
 <table border=0>
	<tr> 
	 <td bgcolor="#C1DADF" align="center" >Nome gruppo di lavoro</td>
	 <td><input type="int" class="obb" name="classe" maxlength="30" alt="Inserire il nome del gruppo di lavoro" /></td></tr>
	<tr> 
	 <td bgcolor="#C1DADF" align="center" >Ordine di scuola</td>
	 <td><select name="ordine">
	  <option value="i">Scuola dell'infanzia</option>
	  <option value="1">Scuola primaria</option>
	  <option value="2">Scuola secondaria di primo grado</option>
	  <option value="s">Scuola secondaria</option>			
	  </select></tr>
  <tr><td colspan="2">
	<input type="submit" value="Inserisci" />&nbsp;&nbsp;<input type="reset" value="Annulla" />
  </td></tr>
 </table>
</form>
<?
$up="VisualizzaClassi";
include "Coda.inc";
?>
