<?php
// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere 
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) Version 2 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// Please READ carefully the Docs/License.txt file for further details
// Please READ the Docs/credits.txt file for complete credits list
// ----------------------------------------------------------------------
// Nome file:  calcoloMediaStudente.php
// Autore di questo file: Sophia Danesino
// Descrizione: calcola la media di uno studente per periodo specifico
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore, operatore, docente (solo se 
// insegna in quel gruppo di lavoro), insegnante affidatario (solo se 
// associato a quello studente), genitore (solo se associato a quello studente)
// ----------------------------------------------------------------------
	

$title = "Visualizzazione dettaglio per periodo specifico scuola ospedaliera";

include "Testa.inc";
include "FunzioniDB.inc";
include("data.inc");

autorizza_ruoli(ID_ADMIN,ID_OPERATORE,ID_OSPEDALIERO,ID_OSSERVATORE,ID_AFFIDATARIO, ID_DOMICILIARE);

include("autenticazione_db.php"); 
	
if ($profile==ID_OSPEDALIERO)
	// Verifica autorizzazione da parte del docente a compilare quel registro
   autorizza_docente_degenza($_POST['id_degenza'],$codice_utente);		
		
if ($profile==ID_OSSERVATORE||$profile==ID_AFFIDATARIO||$profile==ID_DOMICILIARE)
	autorizza_affidatario_genitore($_POST['id_studente'],$codice_utente);

// Visualizza dati anagrafici
$sql =& $link->query("SELECT * FROM Studenti WHERE id_studente = ? limit 1",$_POST['id_studente']);
$riga =& $sql->fetchRow();
$sql_materia =& $link->query("SELECT * FROM Materie WHERE (id_materia= ?)", $_POST['id_materia']);
$riga_materia =& $sql_materia->fetchRow();
		
print "<h2>Studente: {$riga['cognome']} {$riga['nome']} - Materia: {$riga_materia['nome']}</h2>";
	

if($_POST['anno']) {
		$query_media="SELECT ROUND(AVG(valutazione),2) as media FROM Registro WHERE (id_degenza=".$_POST['id_degenza'].") AND (id_materia= ".$_POST['id_materia']." ) AND valutazione<>0 AND (data BETWEEN '".$_POST['anno_inizio']."-".$_POST['mese_inizio']."-".$_POST['giorno_inizio']."' and '".$_POST['anno']."-".$_POST['mese']."-".$_POST['giorno']."')";
		$sql =& $link->query($query_media);
		$num_righe = $sql->numRows(); 		
	   if ($num_righe!=0) {
			$voto_medio =& $sql->fetchRow();
			print "<h3>Media del periodo {$_POST['giorno_inizio']}-{$_POST['mese_inizio']}-{$_POST['anno_inizio']}/{$_POST['giorno']}-{$_POST['mese']}-{$_POST['anno']}: ".$voto_medio['media']."</h3>";
		}
}
?>
	<br><h3>Calcolo media per periodo specifico</h3><center>
	
	<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
	<input type="hidden" name="id_studente" value="<?=$_POST['id_studente']?>" />
	<input type="hidden" name="id_degenza" value="<?=$_POST['id_degenza']?>" />
	<input type="hidden" name="id_studente" value="<?=$_POST['id_studente']?>" />
	<input type="hidden" name="id_classe" value="<?=$_POST['id_classe']?>" />
	<input type="hidden" name="id_materia" value="<?=$_POST['id_materia']?>" />
	<input type="hidden" name="inserisci" value="1" />
	<table>
	<tr>
		<td bgcolor="#C1DADF" align="center" >Inizio periodo</td>
		<td><?data_inizio_anno_scolastico()?>	</td>
	</tr>
	<tr>
		<td bgcolor="#C1DADF" align="center" >Fine periodo</td>
		<td><?data_odierna()?>	</td>
	</tr>		
	<tr>
		<td><input type="submit" value="Visualizza"></td></form>
	</tr>	
	</table>
	
	<?php
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
	
		if (($RUOLO==ID_ADMIN)||($RUOLO==ID_OPERATORE))
			$up="indice.php";
		else if ($profile==ID_OSPEDALIERO)
			$up="Registro.php";
		else
			$up="indice_aff.php";
		if(isset($up)) echo "<br><br><center><a href=\"$up\"><img src=\"immagini/menu.png\"></a><br>Pagina Iniziale</center>";
		include "Coda.inc";
?>
		