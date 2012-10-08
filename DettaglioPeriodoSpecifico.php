<?php
// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere - tulip
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) Version 2 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// Please READ carefully the Docs/License.txt file for further details
// Please READ the Docs/credits.txt file for complete credits list
// ----------------------------------------------------------------------
// Nome file:  DettaglioPeriodoSpecifico.php
// Autore di questo file: Sophia Danesino
// Descrizione: visualizza per un periodo specifico di frequenza ad una scuola 
// ospedaliera tutte le informazioni contenute nel registro
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
	
//if ($profile==ID_OSPEDALIERO)
	// Verifica autorizzazione da parte del docente a compilare quel registro
//	autorizza_docente_degenza($_POST['id_degenza'],$codice_utente);		
		
if ($profile==ID_OSSERVATORE||$profile==ID_AFFIDATARIO||$profile==ID_DOMICILIARE)
	autorizza_affidatario_genitore($_POST['id_studente'],$codice_utente);

// Visualizza dati anagrafici
$sql =& $link->query("SELECT * FROM Studenti WHERE id_studente = ? limit 1",$_POST['id_studente']);
$riga =& $sql->fetchRow();
print "<h2>Prospetto scolastico di {$riga['cognome']} {$riga['nome']}</h2>";

if(!$_POST['anno']) {
?>
	<br><h3>Visualizza prospetto studente per periodo</h3><center>
	
	<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
	<input type="hidden" name="id_studente" value="<?=$_POST['id_studente']?>" />
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
		<td bgcolor="#C1DADF" align="center" >Materia</td>
<?php
    // Possibilità di selezionare un prospetto per singola materia
		if ($profile==ID_ADMIN)
			$sql =& $link->query("SELECT * FROM Materie");
		else if ($profile==ID_AFFIDATARIO)
			$sql =& $link->query("SELECT  DISTINCT Materie.* FROM Materie, CdC, Classe WHERE 
				Materie.id_materia=CdC.id_materia AND  
				(Classe.id_degenza= ?) AND 
				CdC.id_classe=Classe.id_classe AND
				(CdC.id_utente = ? )", array($_POST['id_degenza'],$CODICE_UTENTE));
		else
			$sql =& $link->query("SELECT  DISTINCT Materie.* FROM Materie, CdC, Classe WHERE 
				Materie.id_materia=CdC.id_materia AND  
				(Classe.id_degenza= ?) AND 
				CdC.id_classe=Classe.id_classe AND
				(CdC.id_utente = ? )", array($_POST['id_degenza'],$CODICE_UTENTE));
			errore_DB($sql);
	   	print '<td><select name="id_materia" >';
	   	print '<option name="id_materia" value=0>Tutte le materie</option>';
		while ( $materia =& $sql->fetchRow())
		{
			print "<option name=\"id_materia\" value=\"{$materia['id_materia']}\">{$materia['nome']}</option>";
   		}
		print "</select></td></tr>";
?>
	<tr>
		<td><input type="submit" value="Visualizza"></td></form>
	</tr>	
	</table>
		
	<br><br>
	<h3>Stampa prospetto studente per periodo</h3><center>
	<form action="StampaDettaglioPeriodoSpecifico.php" method="post">
		<input type="hidden" name="id_studente" value="<?=$_POST['id_studente']?>" />
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
		<td bgcolor="#C1DADF" align="center" >Materia</td>
<?php
    // Possibilità di selezionare un prospetto per singola materia
		if ($profile==ID_ADMIN)
			$sql =& $link->query("SELECT * FROM Materie");
		else if ($profile==ID_AFFIDATARIO)
			$sql =& $link->query("SELECT  DISTINCT Materie.* FROM Materie, CdC, Classe WHERE 
				Materie.id_materia=CdC.id_materia AND  
				(Classe.id_degenza= ?) AND 
				CdC.id_classe=Classe.id_classe AND
				(CdC.id_utente = ? )", array($_POST['id_degenza'],$CODICE_UTENTE));
		else
			$sql =& $link->query("SELECT  DISTINCT Materie.* FROM Materie, CdC, Classe WHERE 
				Materie.id_materia=CdC.id_materia AND  
				(Classe.id_degenza= ?) AND 
				CdC.id_classe=Classe.id_classe AND
				(CdC.id_utente = ? )", array($_POST['id_degenza'],$CODICE_UTENTE));
			errore_DB($sql);
	   	print '<td><select name="id_materia" >';
	   	print '<option name="id_materia" value=0>Tutte le materie</option>';
		while ( $materia =& $sql->fetchRow())
		{
			print "<option name=\"id_materia\" value=\"{$materia['id_materia']}\">{$materia['nome']}</option>";
   		}
		print "</select></td></tr>";
?>

	<tr>
		<td><input type="submit" value="Stampa"></td></form>
	</tr>	
	</table>
	</p>
	
<?php
} else {
?>

<h3>Prospetto scolastico</h3>
<table class="elenco">
 <tr>
  <th>Cognome</th>
  <td bgcolor="#DDDDDD" colspan="6" ><?=$riga['cognome']?></td></tr>
 <tr>
  <th>Nome</th>
  <td bgcolor="#DDDDDD" colspan="6" ><?=$riga['nome']?></td></tr>
 
<?php
	// Visualizza scuola di appartenenza

	$sql_scuola =& $link->query("SELECT nome,citta,provincia FROM Scuola,Scuole WHERE Scuola.id_studente = ? AND tipo='p' AND Scuola.id_scuola = Scuole.id_scuola", $_POST['id_studente']);
	$riga_scuola =& $sql_scuola->fetchRow();
	print '<tr><th>Scuola </th>';
	print "<td bgcolor=\"#DDDDDD\" colspan=\"6\" >".$riga_scuola['nome']."&nbsp;-&nbsp;".$riga_scuola['citta']."&nbsp;(".$riga_scuola['provincia'].")</td></tr>";
	
	// Visualizza la classe
	print '<tr><th>Classe</th>';
	switch($riga['ordine']) {
  	case 'M': $ordine='Scuola dell\'infanzia'; break;
  	case 'P': $ordine='Scuola primaria'; break;
  	case 'I': $ordine='Scuola secondaria di primo grado'; break;
  	case 'S': $ordine='Scuola secondaria';
  }
	print "<td bgcolor=\"#DDDDDD\" colspan=\"6\" >".$riga['classe']." ".$ordine."</td>";
	print "</tr></table>";
	
	// Visualizza periodo di frequenza in scuola ospedaliera nel periodo indicato

	print "<h3>Dettaglio del periodo {$_POST['giorno_inizio']}-{$_POST['mese_inizio']}-{$_POST['anno_inizio']}/{$_POST['giorno']}-{$_POST['mese']}-{$_POST['anno']}</h3>";
	$registro_singolo = "registro_".date("dmyHis");

	// Per ogni materia visualizza informazioni didattiche
			$sql = 	"CREATE VIEW $registro_singolo AS SELECT Registro.id_degenza,Registro.id_materia,Registro.argomenti,osservazioni,valutazione, ruolo, data FROM Registro,Materie,Degenze, Studenti WHERE Registro.id_degenza = Degenze.id_degenza AND Studenti.id_studente=Degenze.id_studente AND Studenti.id_studente ='".$_POST['id_studente']."' AND  Materie.id_materia=Registro.id_materia AND (data BETWEEN '".$_POST['anno_inizio']."-".$_POST['mese_inizio']."-".$_POST['giorno_inizio']."' and '".$_POST['anno']."-".$_POST['mese']."-".$_POST['giorno']."')";
			
			$res =& $link->query($sql);
			errore_DB($res);
			if ($_POST['id_materia']==0) {
				if ($profile==ID_ADMIN || $profile==ID_OPERATORE || $profile==ID_AFFIDATARIO || $profile==ID_OSSERVATORE)
					$query_materie="SELECT * FROM Materie ORDER BY ordine";
				else // selezione materie insegnate da quell'insegnante in quel gruppo di lavoro
					$query_materie="SELECT DISTINCT Materie.id_materia, Materie.nome FROM Materie, CdC, Classe WHERE Materie.id_materia=CdC.id_materia AND CdC.id_classe=Classe.id_classe AND  CdC.id_utente=".$CODICE_UTENTE." ORDER BY ordine";
			}
			else 
					$query_materie="SELECT * FROM Materie WHERE Materie.id_materia=".$_POST['id_materia'];
			$sql_materia =& $link->query($query_materie);
		 
			while ($riga_materia =& $sql_materia->fetchRow())
			{ //while01
				$query_rm = "SELECT * FROM $registro_singolo WHERE $registro_singolo.id_materia=".$riga_materia['id_materia']." ORDER BY data";
				$sql_registro_materia =& $link->query($query_rm);
				errore_DB($sql_registro_materia);
				$rigarm =& $sql_registro_materia->fetchRow();
				if ($rigarm) {
					$pari=1; 
					print '<table class="elenco" width="80%"><th colspan="5">'.$riga_materia['nome'].'</th>';
					print '<tr class="dispari" ><td width="10%"><center><strong>Data</strong></center></td>
					<td width="10%"><strong><center>Docente</center></strong></td>
					<td width="40%"><strong><center>Argomenti</center></strong></td>
					<td width="30%"><strong><center>Osservazioni</center></strong></td>
					<td width="10%"><strong><center>Valutazioni</center></strong></td></tr>';

					do { 
						$class = ($pari) ? "pari" : "dispari";
						$pari = 1-$pari;
						$data= preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$rigarm['data']);
				
						switch ($rigarm['ruolo']) {
							case ID_DOMICILIARE: $ruolo = "Domiciliare"; break;
							case ID_ADMIN:
							case ID_OPERATORE:
							case ID_OSPEDALIERO: $ruolo = "Ospedaliero"; break;
							case ID_AFFIDATARIO: $ruolo = "Non ospedaliero";
						}
						print "<tr class='".$class."'><td>".$data."</td><td>".$ruolo."</td>
						<td align='justify' valign='top'>".$rigarm['argomenti']."</td>
						<td align='justify' valign='top'>".$rigarm['osservazioni']."</td>
						<td align='justify' valign='top'>".$rigarm['valutazione']."</td></tr>";					  
					}	
					while ($rigarm =& $sql_registro_materia->fetchRow() );
					print '</table><br/> ';
				}
			}	// end while01
			$sql = 	"DROP VIEW $registro_singolo";
			$res =& $link->query($sql);
			errore_DB($res);
		
  
}
           
if ($profile==ID_OSSERVATORE||$profile==ID_AFFIDATARIO)   
           $up="ListaProspetti.php";
elseif ($profile==ID_OSPEDALIERO)
			$up="indice_doc";
		elseif ($profile==ID_ADMIN ||$profile==ID_OPERATORE)
			$up="indice.php";
		else
			$up="indice_aff.php";
if(isset($up)) echo "<br><br><center><a href=\"$up\"><img src=\"immagini/menu.png\"></a><br>Pagina Iniziale</center>";

include "Coda.inc";
?>
