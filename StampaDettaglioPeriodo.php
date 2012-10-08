<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  StampaDettaglioPeriodo.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: visualizza per un periodo di frequenza ad una scuola 
// ospedaliera tutte le informazioni contenute nel registro; in particolare 
// visualizza i dati anagrafici, scuola di appartenenza, lingue straniere 
// studiate, periodo di frequenza in scuola ospedaliera e per ogni materia 
// visualizza informazioni didattiche. La visualizzazione è predisposta
// per essere stampata come documento ufficiale.
// 23/7/09: modificato il prospetto a seguito variazione DB tabella
//          Registro (Sophia Danesino)
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore, operatore, docenti del 
// consiglio di classe relativo al gruppo di lavoro in cui è inserito lo 
// studente, genitori e docenti affidatari associati allo studente
// ----------------------------------------------------------------------
	
$title = "Visualizzazione dati completi studente";

include "Testa_stampa.inc";
include "FunzioniDB.inc";

autorizza_ruoli(ID_ADMIN,ID_OPERATORE,ID_OSPEDALIERO,ID_OSSERVATORE,ID_AFFIDATARIO, ID_DOMICILIARE);

include("autenticazione_db.php"); 
	
if ($profile==ID_OSPEDALIERO)
	// Verifica autorizzazione da parte del docente a compilare quel registro
	autorizza_docente_degenza($_POST['id_degenza'],$codice_utente);		

if (($profile==ID_OSSERVATORE)||($profile==ID_AFFIDATARIO)||($profile==ID_DOMICILIARE))
	autorizza_affidatario_genitore($_POST['id_studente'],$codice_utente);

 	
	// Intestazione scuola ospedaliera
	
$sql =& $link->query("SELECT * FROM Ospedale");
$riga =& $sql->fetchRow();
print "<center>";
if (!empty($riga['denominazione']))
	print '<b>'.$riga['denominazione'].'&nbsp;'.$riga['nome'].'</b><br />';
print $riga['indirizzo'];
if (!empty($riga['cap']))
	print "&nbsp;,&nbsp;".$riga['cap']."&nbsp;&nbsp;".$riga['citta'];
if (!empty($riga['provincia']))
	print "&nbsp;(".$riga['provincia'].")<br />";
print 'Codice scuola:&nbsp;'.$riga['codice']."<br />";
if (!empty($riga['telefono']))
	print '<br />Telefono: '.$riga['telefono'];
if (!empty($riga['fax']))
	print '<br />Fax: '.$riga['fax'];
if (!empty($riga['email']))
	print '<br />E-mail: '.$riga['email'];
if (!empty($riga['sitoweb']))
	print '<br />Sito web:&nbsp;'.$riga['sitoweb'];

// Visualizza dati anagrafici
	
$sql =& $link->query("SELECT * FROM Studenti WHERE id_studente={$_POST['id_studente']}");
$riga =& $sql->fetchRow();?>

<br /><br /><div align="center"><b><font size="+2">Prospetto scolastico</font></b></div><br /><br />

<table>
 <tr>
  <td  align="left" ><strong>Cognome:&nbsp;</strong></td>
  <td  align="left" ><?=$riga['cognome']?></td></tr>
 <tr>
  <td  align="left" ><strong>Nome:&nbsp;</strong></td>
  <td  align="left" ><?=$riga['nome']?></td></tr>
	
<?	// Visualizza scuola di appartenenza
$sql_scuola =& $link->query("SELECT nome,citta,provincia FROM Scuola,Scuole WHERE Scuola.id_studente = ? AND tipo='p' AND Scuola.id_scuola = Scuole.id_scuola", $_POST['id_studente']);
$riga_scuola =& $sql_scuola->fetchRow();?>
 <tr>
  <td  align="left" ><strong>Scuola:</strong></td>
  <td  align="left" ><?=$riga_scuola['nome']?>&nbsp;-&nbsp;<?=$riga_scuola['citta']?>&nbsp;(<?=$riga_scuola['provincia']?>)</td></tr>

 <tr>
  <td  align="left"><strong>Classe:</strong></td>
<?	
	// Visualizza classe frequentata
	switch($riga['ordine']) {
  	case 'M': $ordine='Scuola dell\'infanzia'; break;
  	case 'P': $ordine='Scuola primaria'; break;
  	case 'I': $ordine='Scuola secondaria di primo grado'; break;
  	case 'S': $ordine='Scuola secondaria';
  }
	print "<td  align='left' >".$riga['classe']." ".$ordine."</td>";
	print "</tr></table>";


	// Visualizza periodo di frequenza in scuola ospedaliera

	$sql =& $link->query("SELECT * FROM Degenze WHERE (Degenze.id_degenza = ? )", $_POST['id_degenza']);
	$riga =& $sql->fetchRow();
	$data_inizio = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['data_inizio']);
	$data_fine = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['data_fine']);
	
	print "<h3>Periodo di frequenza: $data_inizio - $data_fine</h3>";
	print "</center>";
	
	// Per ogni materia visualizza informazioni didattiche
	$registro_singolo = "registro_".date("dmyHis");

	$sql = 	"CREATE VIEW $registro_singolo AS SELECT Registro.id_materia, argomenti, osservazioni, valutazione, ruolo, data FROM Degenze,Registro,Materie WHERE (Degenze.id_degenza =".$_POST['id_degenza']." ) AND  Materie.id_materia=Registro.id_materia AND (Registro.id_degenza = ".$_POST['id_degenza']." ) ";
	$res =& $link->query($sql);
	errore_DB($res);
	if ($profile==ID_ADMIN || $profile==ID_OPERATORE || $profile==ID_AFFIDATARIO || $profile==ID_OSSERVATORE)
		$query_materie="SELECT * FROM Materie ORDER BY ordine";
	else // selezione materie insegnate da quell'insegnante in quel gruppo di lavoro
		$query_materie="SELECT * FROM Materie, CdC, Classe WHERE Materie.id_materia=CdC.id_materia AND CdC.id_classe=Classe.id_classe AND Classe.id_degenza=".$_POST['id_degenza']." AND  CdC.id_utente=".$CODICE_UTENTE." ORDER BY ordine";
	$sql_materia =& $link->query($query_materie);
		
	while ($riga_materia =& $sql_materia->fetchRow())
	{ //while01
						
			$sql_registro_materia =& $link->query("SELECT * FROM $registro_singolo WHERE $registro_singolo.id_materia={$riga_materia['id_materia']}");
			$pari=1;
			$riga =& $sql_registro_materia->fetchRow();
			if ($riga) {
				print '<p><font size=2"><br><table width="100%" style="border:0px solid; border-collapse:collapse;" ><tr><th colspan="5"><center><font size="3">'.$riga_materia['nome'].'</font></center><br></th></tr>';
				print '<tr><td width="15%" style="border:1px solid;"><b>Data</b></td><td width="15%" style="border:1px solid;"><b>Docente</b></td><td width="45%" style="border:1px solid;"><b>Argomenti</b></td><td width="20%" style="border:1px solid;"><b>Osservazioni</b></td><td width="10%" style="border:1px solid;"><b>Valutazioni</b></td></tr>';

				do {
					$data= preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['data']);
				
					switch ($riga['ruolo']) {
						case ID_DOMICILIARE: $ruolo = "Domiciliare"; break;
						case ID_ADMIN:
						case ID_OPERATORE:
						case ID_OSPEDALIERO: $ruolo = "Ospedaliero"; break;
						case ID_AFFIDATARIO: $ruolo = "Non ospedaliero";
					}
					print "<tr style='border:1px solid'><td valign='top'  style='border:1px solid'>".$data."</td><td align='justify' valign='top' style='border:1px solid'>".$ruolo."</td><td align='left' valign='top' style='border:1px solid'>".$riga['argomenti']."</td><td align='left' valign='top' style='border:1px solid'>".$riga['osservazioni']."</td><td align='center' valign='top' style='border:1px solid'>".$riga['valutazione']."</td></tr>";
				}
				while ($riga =& $sql_registro_materia->fetchRow());
				print '</table></font></p> ';
			}
	}	// end while01
	$sql = 	"DROP VIEW $registro_singolo";
	$res =& $link->query($sql);
	//print "<p align='left'><strong>Legenda: <br></strong><strong>D</strong>:&nbsp;Docente domiciliare<br><strong>O</strong>:&nbsp;Docente ospedaliero<br><strong>N</strong>:&nbsp;Docente non ospedaliero</p>";
?>
</div>
</body>
</html>
