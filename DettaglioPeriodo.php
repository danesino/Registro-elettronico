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
// Nome file:  DettaglioPeriodo.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: visualizza per un periodo di frequenza ad una scuola 
// ospedaliera tutte le informazioni contenute nel registro
// 23/7/09: modificato il prospetto a seguito variazione DB tabella
//          Registro (Sophia Danesino)
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore, operatore, docente (solo se 
// insegna in quel gruppo di lavoro), insegnante affidatario (solo se 
// associato a quello studente), genitore (solo se associato a quello studente)
// ----------------------------------------------------------------------
	

$title = "Visualizzazione dettaglio periodo frequenza scuola ospedaliera";

include "Testa.inc";
include "DefRuoli.inc";
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN,ID_OPERATORE,ID_OSPEDALIERO,ID_OSSERVATORE,ID_AFFIDATARIO, ID_DOMICILIARE);
	
include("autenticazione_db.php"); 

if ($profile==ID_OSPEDALIERO)
	// Verifica autorizzazione da parte del docente a visualizzare quel registro
	autorizza_docente_degenza($_POST['id_degenza'],$codice_utente);		
		
if ($profile==ID_OSSERVATORE||$profile==ID_AFFIDATARIO||$profile==ID_DOMICILIARE)
	autorizza_affidatario_genitore($_POST['id_studente'],$codice_utente);

// Visualizza dati anagrafici

$sql =& $link->query("SELECT * FROM Studenti WHERE id_studente = ? limit 1",$_POST['id_studente']);
$riga =& $sql->fetchRow();
?>

<h3>Prospetto scolastico</h3>
<table class="elenco">
 <tr>
  <th>Cognome</th>
  <td bgcolor="#DDDDDD" colspan="6" ><?=$riga['cognome']?></td></tr>
 <tr>
  <th>Nome</th>
  <td bgcolor="#DDDDDD" colspan="6" ><?=$riga['nome']?></td></tr>
 <?	
	// Visualizza scuola di appartenenza

	$sql_scuola =& $link->query("SELECT nome,citta,provincia FROM Scuola,Scuole WHERE Scuola.id_studente = ? AND tipo='p' AND Scuola.id_scuola = Scuole.id_scuola", $_POST['id_studente']);
	$riga_scuola =& $sql_scuola->fetchRow();
	print '<tr><th>Scuola </th>';
	print "<td bgcolor=\"#DDDDDD\" colspan=\"6\" >".$riga_scuola['nome']."&nbsp;-&nbsp;".$riga_scuola['citta']."&nbsp;(".$riga_scuola['provincia'].")</td></tr>";
	
	// Visualizza classe frequentata
	print '<tr><th>Classe</th>';
	
	switch($riga['ordine']) {
  	case 'M': $ordine='Scuola dell\'infanzia'; break;
  	case 'P': $ordine='Scuola primaria'; break;
  	case 'I': $ordine='Scuola secondaria di primo grado'; break;
  	case 'S': $ordine='Scuola secondaria';
  }
	print "<td bgcolor=\"#DDDDDD\" colspan=\"6\" >".$riga['classe']." ".$ordine."</td>";
	print "</tr></table>";

	
	// Visualizza periodo di frequenza in scuola ospedaliera

	$sql =& $link->query("SELECT * FROM Degenze WHERE (Degenze.id_degenza = ? )", $_POST['id_degenza']);
	$riga =& $sql->fetchRow();
	$data_inizio = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['data_inizio']);
	$data_fine = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['data_fine']);
	
	print "<h3>Periodo di frequenza: $data_inizio - $data_fine</h3>";
	
	// nome univoco vista 
	$registro_singolo = "registro_".date("dmyHis");

	// Per ogni materia visualizza informazioni didattiche
	
	$sql = "CREATE VIEW $registro_singolo AS SELECT Registro.id_materia, argomenti, osservazioni,valutazione, ruolo, data FROM Degenze,Registro,Materie WHERE ((Degenze.id_degenza =".$_POST['id_degenza']." ) AND  (Materie.id_materia=Registro.id_materia) AND (Registro.id_degenza = ".$_POST['id_degenza']." )) ";
	
	$res =& $link->query($sql);
	errore_DB($res);
	
	if ($profile==ID_ADMIN || $profile==ID_OPERATORE || $profile==ID_AFFIDATARIO || $profile==ID_OSSERVATORE)
		$query_materie="SELECT * FROM Materie ORDER BY ordine";
	else // selezione materie insegnate da quell'insegnante in quel gruppo di lavoro
		$query_materie="SELECT * FROM Materie, CdC, Classe WHERE Materie.id_materia=CdC.id_materia AND CdC.id_classe=Classe.id_classe AND Classe.id_degenza=".$_POST['id_degenza']." AND  CdC.id_utente=".$CODICE_UTENTE." ORDER BY ordine";
	$sql_materia =& $link->query($query_materie);
		
	while ($riga_materia =& $sql_materia->fetchRow())
	{ //while01
						
			$sql_registro_materia =& $link->query("SELECT * FROM $registro_singolo WHERE $registro_singolo.id_materia={$riga_materia['id_materia']} ORDER BY data");
			$pari=1;
			$riga =& $sql_registro_materia->fetchRow();
			if ($riga) {
				print '<table class="elenco" width="80%"><th colspan="5">'.$riga_materia['nome'].'</th>';
				print '<tr class="dispari" ><td width="10%"><center><strong>Data</strong></center></td><td width="10%"><strong><center>Docente</center></strong></td><td width="45%"><strong><center>Argomenti</center></strong></td><td width="30%"><strong><center>Osservazioni</center></strong></td><td width="10%"><strong><center>Valutazioni</center></strong></td></tr>';
				do {
					$class = ($pari) ? "pari" : "dispari";
					$pari = 1-$pari;
					$data= preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['data']);
				
					switch ($riga['ruolo']) {
						case ID_DOMICILIARE: $ruolo = "Domiciliare"; break;
						case ID_ADMIN:
						case ID_OPERATORE:
						case ID_OSPEDALIERO: $ruolo = "Ospedaliero"; break;
						case ID_AFFIDATARIO: $ruolo = "Non ospedaliero";
					}
					print "<tr class='".$class."'><td>".$data."</td><td>".$ruolo."</td><td align='justify' valign='top'>".$riga['argomenti']."</td><td align='justify' valign='top'>".$riga['osservazioni']."</td><td align='center' valign='top'>".$riga['valutazione']."</td></tr>";
				}
				while ($riga =& $sql_registro_materia->fetchRow());
				print '</table><br/> ';
			}
	}	// end while01
	$sql = 	"DROP VIEW $registro_singolo";
	$res =& $link->query($sql);
	
    if ($profile==ID_OSSERVATORE||$profile==ID_AFFIDATARIO)   
	                   $up="indice_aff.php";
	elseif($profile==ID_OSPEDALIERO||$profile==ID_DOMICILIARE)
	                   $up="Registro.php";
	if(isset($up)) echo "<br><br><center><a href=\"$up\"><img src=\"immagini/menu.png\"></a><br>Pagina Iniziale</center>";

	include "Coda.inc";
?>
