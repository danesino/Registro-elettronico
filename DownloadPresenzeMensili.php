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
// Nome file:  DownloadPresenzeMensili.php
// Autore di questo file: Sophia Danesino
// Descrizione: visualizza/download presenze di uno studente per mese
// 9/7/2010: modifica selezione gruppo di lavoro e visualizzazione per mese
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore, operatore, docente (solo se 
// insegna in quel gruppo di lavoro), insegnante affidatario (solo se 
// associato a quello studente), genitore (solo se associato a quello studente)
// ----------------------------------------------------------------------
	

$title = "Visualizzazione presenze mensili";
include "Testa.inc";
autorizza_ruoli(ID_ADMIN,ID_OPERATORE);

include "FunzioniDB.inc";
include("autenticazione_db.php"); 
require_once 'HTML/QuickForm.php';

function calendario ($a) {
	global $link, $REG, $RUOLO, $CODICE_UTENTE;
	
	$mese_= (int)$a['data']['M'];
	$anno_= (int)$a['data']['Y'];
	$human_month = array("error", "Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre" ); 
	$giorni      = date("t",mktime(0, 0, 0, $mese, 1, $anno));  //giorni del mese in questione
	$primo_lunedi= date("w",mktime(0, 0, 0, $mese, 1, $anno));  //Array_parte da 0

	$dir = "/var/Scuole/$REG/";
	$file = "Presenze-".$human_month[(int)$a['data']['M']]."-".$a['data']['Y'];
	if (($RUOLO == ID_ADMIN) &&($_POST['id_classe']!=0))
		$file=$file."-gruppo".$_POST['id_classe'];
	$file=$file.".doc";
	$path = $dir.$file; 
	
	if (file_exists($path))
		 unlink($path);
	$fp = fopen($path, 'w+');
	


	// Intestazione scuola ospedaliera
	
	$sql =& $link->query("SELECT * FROM Ospedale");
	$riga =& $sql->fetchRow();
	fwrite($fp, "<center>");
	if (!empty($riga['denominazione']))
		fwrite($fp, '<b>'.$riga['denominazione'].' '.$riga['nome'].'</b><br />');
	fwrite($fp,  $riga['indirizzo']);
	if (!empty($riga['cap']))
		fwrite($fp,  "  ".$riga['cap']."  ".$riga['citta']);
	if (!empty($riga['provincia']))
		fwrite($fp,  " (".$riga['provincia'].")<br />");
	fwrite($fp,  'Codice scuola: '.$riga['codice']."<br />");
	if (!empty($riga['telefono']))
		fwrite($fp, '<br />Telefono: '.$riga['telefono']);
	if (!empty($riga['fax']))
		fwrite($fp, '<br />Fax: '.$riga['fax']);
	if (!empty($riga['email']))
		fwrite($fp, '<br />E-mail: '.$riga['email']);
	if (!empty($riga['sitoweb']))
		fwrite($fp, '<br />Sito web: '.$riga['sitoweb']);
	fwrite($fp,  "<center>");
	
	
	if($primo_lunedi==0){
		$primo_lunedi = 7;  //siamo mica americani
	}

	print "<br><strong><center>Presenze ".$human_month[(int)$a['data']['M']]." ".$a['data']['Y']."</center></strong><br>"; //mese/anno
	fwrite($fp, "<br><strong><center>Presenze ".$human_month[(int)$a['data']['M']]." ".$a['data']['Y']."</center></strong><br>"); //mese/anno
    $a_mese=$a['data']['M'];
    $a_anno=$a['data']['Y'];
	if (($RUOLO == ID_ADMIN) &&($_POST['id_classe']!=0)){
		$sql_classe =& $link->query("SELECT classe, ordine FROM Classe,Classi WHERE Classe.id_classe=Classi.id_classe AND Classe.id_classe= ?", $_POST['id_classe']);
		errore_DB($sql);
		$classe  =& $sql_classe->fetchRow();
		print "<center>Gruppo di lavoro <strong>".$classe['classe'];
		switch ($classe['ordine'])
		{ 
		  case "i": $ordine=" (scuola dell'infanzia)"; break;
		  case "1": $ordine=" (scuola primaria)"; break;
		  case "2": $ordine=" (scuola secondaria di primo grado)"; break;
		  case "s": $ordine=" (scuola secondaria)"; break;
		}
		print $ordine."</strong></center><br><br>";
		fwrite($fp, "<center>Gruppo di lavoro <strong>".$classe['classe'].$ordine."</strong></center><br><br>");
	}
	
	print("<table class='elenco' width='80%'>"); //table 
	fwrite($fp,"<table BORDER=1 BORDERCOLOR='#000000' CELLPADDING=4 CELLSPACING=0>");
	print("<tr ><th><strong>Cognome</strong></th><th><strong>Nome</strong></th><th><strong>Classe</strong></th><th><strong>Ripetente</strong></th><th><strong>Straniero</strong></th><th><strong>HC</strong></th>");
	fwrite($fp,"<thead><tr><th><strong>Cognome</strong></th><th><strong>Nome</strong></th><th><strong>Classe</strong></th><th><strong>Ripetente</strong></th><th><strong>Straniero</strong></th><th><strong>HC</strong></th>");
	for($i = 1; $i<$giorni+$primo_lunedi; $i++){
		if($i>=$primo_lunedi) {
			$giorno_= $i-($primo_lunedi-1);
			$a = strtotime(date($anno_."-".$mese_."-".$giorno_));
			// salto i giorni festivi
			if (strftime("%w",$a)!=0) {
				print("<th><strong>".$giorno_."</strong></th>");
				fwrite($fp,"<th><strong>".$giorno_."</strong></th>");
			}
		}
	}                     
	print("<th><strong>Totale</strong></th></tr>"); 
	fwrite($fp,"<th><strong>Totale</strong></th></tr></thead><tbody>"); 
	                                               
	if ($RUOLO == ID_OPERATORE) 
		$query="SELECT DISTINCT cognome,nome, classe, ordine, HC, ripetente, straniero FROM Studenti,Degenze,Registro,Classe WHERE Classe.id_degenza=Degenze.id_degenza AND id_classe in (select distinct id_classe from CdC where id_utente = ".$CODICE_UTENTE.") AND Studenti.id_studente = Degenze.id_studente AND Degenze.id_degenza=Registro.id_degenza AND (Registro.ruolo=3 OR Registro.ruolo=1 OR Registro.ruolo=2) ";
	else  
		$query="SELECT DISTINCT cognome,nome, classe, ordine, HC, ripetente, straniero FROM Studenti,Degenze,Registro,Classe WHERE Classe.id_degenza=Degenze.id_degenza AND Studenti.id_studente=Degenze.id_studente AND Degenze.id_degenza=Registro.id_degenza AND (Registro.ruolo=3 OR Registro.ruolo=1 OR Registro.ruolo=2) ";
    
    // se è stato specificato un gruppo di lavoro modifico la query
	if ($_POST['id_classe']!=0)
		$query=$query." AND Classe.id_classe=".$_POST['id_classe'];
		
	$query=$query." AND (data_fine='0000-00-00' OR data_fine>='".$anno."-".$mese."-00')";
		
	$query=$query." ORDER BY cognome"; 
	
	$sql =& $link->query($query);
	errore_DB($sql);
	$pari=1;
	$num_righe=0;
	while ($riga =& $sql->fetchRow())
	{ 
		// controllo che ci siano presenze in quel mese, altrimenti non faccio comparire quello studente
		$sql_check =& $link->query("SELECT * FROM Studenti,Degenze,Registro WHERE Studenti.cognome=\"".$riga['cognome']."\" AND Studenti.nome='".$riga['nome']."' AND Studenti.id_studente = Degenze.id_studente AND Degenze.id_degenza=Registro.id_degenza AND (Registro.ruolo=3 OR Registro.ruolo=1 OR Registro.ruolo=2) AND (Registro.data BETWEEN '".$a_anno."-".$a_mese."-00' and '".$a_anno."-".$a_mese."-31')");
		
		if ($riga_check=& $sql_check->fetchRow()) {
			//rilevata almeno una presenza	
			$tot_presenze=0;
			$num_righe++;				
			$class = ($pari) ? "pari" : "dispari";
			$pari = 1-$pari;
			switch($riga['ordine']) {
  				case 'M': $ordine='Scuola dell\'infanzia'; break;
  				case 'P': $ordine='Scuola primaria'; break;
  				case 'I': $ordine='Scuola secondaria di primo grado'; break;
  				case 'S': $ordine='Scuola secondaria';
  			}
			print "<tr class='".$class."'><td>".$riga['cognome']."</td><td nowrap='nowrap'>".$riga['nome']."</td><td nowrap='nowrap'>".$riga['classe']." ".$ordine."</td><td nowrap='nowrap'>".($riga['ripetente'] == "0" ? "NO" : "SI")."</td><td nowrap='nowrap'>".($riga['straniero'] == "0" ? "NO" : "SI")."</td><td nowrap='nowrap'>".($riga['HC'] == "0" ? "NO" : "SI")."</td>";
			fwrite($fp, "<tr><td>".$riga['cognome']."</td><td >".$riga['nome']."</td><td nowrap='nowrap'>".$riga['classe']." ".$ordine."</td><td nowrap='nowrap'>".($riga['ripetente'] == "0" ? "NO" : "SI")."</td><td nowrap='nowrap'>".($riga['straniero'] == "0" ? "NO" : "SI")."</td><td nowrap='nowrap'>".($riga['HC'] == "0" ? "NO" : "SI")."</td>");
			for($i = 1; $i<$giorni+$primo_lunedi; $i++){
				if($i>=$primo_lunedi) {
					$giorno_= $i-($primo_lunedi-1);		
					$a = strtotime(date($anno_."-".$mese_."-".$giorno_));
					if (strlen($mese_)==1)
						$mese_="0".$mese_; 
					if (strlen($giorno_)==1)
						$giorno_="0".$giorno_; 
					$data_cerca= $anno_."-".$mese_."-".$giorno_; 
					// salto la domenica      
					if (strftime("%w",$a)!=0) {
						$sql_data =& $link->query("SELECT DISTINCT data FROM Studenti,Degenze,Registro WHERE Studenti.cognome=\"".$riga['cognome']."\" AND Studenti.nome='".$riga['nome']."' AND Studenti.id_studente = Degenze.id_studente AND Degenze.id_degenza=Registro.id_degenza AND (Registro.ruolo=3 OR Registro.ruolo=1 OR Registro.ruolo=2) AND data='".$data_cerca."'");
						if ($riga_data=& $sql_data->fetchRow()) {
							print("<td nowrap='nowrap'><center>P</center></td>");
							fwrite($fp,"<td ><center>P</center></td>");
							$tot_presenze++;
						}
						else {
							print("<td  nowrap='nowrap'></td>");
							fwrite($fp,"<td > </td>");
						}
					}
				}
			}                     
                        print "<td><center>".$tot_presenze."</center></td>";
                        fwrite($fp, "<td><center>".$tot_presenze."</center></td>");
			print("</tr>");
			fwrite($fp,"</tr>");
		}
	}
	print("</table>");
	fwrite($fp,"</tbody></table>");
	print("<br><center>Numero complessivo studenti: <b>".$num_righe."</b></center>");
	fwrite($fp,"<br>Numero complessivo studenti: <b>".$num_righe."</b>");
	fclose($fp);
	echo '<form method="post" action="./Download.php">';
	 echo '<input type="hidden" name="file" value="'.$file.'" />';
	 echo '<input type="hidden" name="dir" value="'.$dir.'" /><br><br><center><input type="submit" value="Scarica file presenze"></center><br><br></form><br><br>'; 
	//print "<a href='./Download?dir=".$dir."&file=".$file.">Download</a>";
	//die("");
 
} // end calendario

print "<h3>Presenze mensili</h3><br>";
$form = new HTML_QuickForm('form');
$emptyValue = array ('M'=>date('m'), 'Y'=>date('Y'));
$options = array('language' => 'it', 'format' => 'MY', 'minYear' => 2012, 'maxYear' => 2013, 'class' => 'obb','emptyOptionText'=>$emptyText, 'emptyOptionValue'=>0, 'addEmptyOption'=>true);
$form->addElement('date', 'data', 'Data:', $options, false);
$select =& $form->addElement('select', 'id_classe', 'Gruppo di lavoro');
$select->loadQuery($link, "SELECT classe, id_classe from Classi ", 'classe', 'id_classe');
$select->addOption("Tutti", "0");
$form->addElement('submit', null, 'Visualizza calendario');
$form->addRule('date', 'Selezionare il mese richiesto', 'required', '', 'client');
if ($form->validate())
	$form->process('calendario', false);
$form->display();

if (($RUOLO==ID_ADMIN)||($RUOLO==ID_OPERATORE))
			$up="indice.php";
if(isset($up)) echo "<br><br><center><a href=\"$up\"><img src=\"immagini/menu.png\"></a><br>Pagina Iniziale</center>";
		include "Coda.inc";
?>
