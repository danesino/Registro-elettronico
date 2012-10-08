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
// Nome file:  Statistiche.php
// Autore di questo file: Sophia Danesino
// Descrizione: visualizza per un periodo specifico di frequenza,
// reparto e classe
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore, operatore, docente (solo se 
// insegna in quel gruppo di lavoro), insegnante affidatario (solo se 
// associato a quello studente), genitore (solo se associato a quello studente)
// ----------------------------------------------------------------------
	

$title = "Visualizzazione statistiche per periodo specifico scuola ospedaliera";

include "Testa.inc";
include "FunzioniDB.inc";
include("data.inc");

autorizza_ruoli(ID_ADMIN,ID_OPERATORE);

include("autenticazione_db.php"); 
		
// Visualizza dati anagrafici
print "<h2>Generazione statistiche</h2>";

if(!$_POST['anno']) {
?>
	<center>
	<form action="Statistiche.php" method="post">
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
		<td bgcolor="#C1DADF" align="center" >Gruppo di lavoro</td><td>
	<?	$sql =& $link->query("SELECT id_classe,classe,ordine FROM Classi ORDER BY classe");
		errore_DB($sql);
		print "<select name='id_classe'>";	
		print "<option value='0' selected>Tutti i gruppi</option>";	
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
		?>
		</select></td></tr>
		<tr>
		<td bgcolor="#C1DADF" align="center" >Reparto</td>
		<td  nowrap="nowrap">
		<?
		@$reparto = selectall("Reparti");
		print '<select  name="id_reparto" >';
		if($reparto == 0){
			die("<dd>Non sono stati inseriti Reparti, per inserirne uno Clicca <a href='GestioneReparti.php'>qui</a></dd>");
		}
		else
		{
			print "<option value='0' selected>Tutti i reparti</option>";
			foreach ($reparto as $reparto)
			print "\t<option name=\"id_reparto\" value=\"{$reparto['id_reparto']}\">{$reparto['nome']}</option>\n";
		}
		print "</select>";
	?>
	</td></tr>
	<tr><td><input type="radio" checked="checked" name="tipo_report" value="V">Visualizza</td><td><input type="radio" name="tipo_report" value="S">Stampa</td></tr>
	<tr><td><input type="submit" value="Genera report"></td></tr>
	
	</table>
	</p></form>
	
<?php
} 
else 
{
	print "<h3>Periodo dal ".$_POST['giorno_inizio']."-".$_POST['mese_inizio']."-".$_POST['anno_inizio']." al ".$_POST['giorno']."-".$_POST['mese']."-".$_POST['anno']."<br>";

	if ($_POST['id_classe']=='0')	{
		$sql =& $link->query("SELECT * FROM Classi");
		print "Tutti i gruppi di lavoro<br>";
	}
	else 
		$sql =& $link->query("SELECT * FROM Classi WHERE id_classe=?",$_POST['id_classe']);
	errore_DB($sql);
	$riga =& $sql->fetchRow();
   if ($_POST['id_classe']!='0')		
		print "Gruppo di lavoro: ".$riga['classe']."<br>"; $classe=$riga['classe'];
	if ($_POST['id_reparto']!='0')	{
		$sql =& $link->query("SELECT * FROM Reparti WHERE id_reparto=?",$_POST['id_reparto']);
		errore_DB($sql);
		$riga =& $sql->fetchRow();
		print "Reparto: ".$riga['nome']."</h3><br>";
	}
	else 
		print "Tutti i reparti"."</h3><br>";
	if ($_POST["tipo_report"]=="S") {
		$dir = "/var/Scuole/$REG/";
		$file = "Statistiche-".$_POST['giorno_inizio'].$_POST['mese_inizio'].$_POST['anno_inizio']."-".$_POST['giorno'].$_POST['mese'].$_POST['anno'];
		$file=$file."-".$classe;
		$file=$file."-".$riga['nome'];
		$file=$file.".doc";
		$path = $dir.$file; 
		if (file_exists($path))
			unlink($path);
		$fp = fopen($path, 'w+');
		fwrite($fp,"<HTML><HEAD><STYLE TYPE='text/css'><!--");
		fwrite($fp,"@page { size: landscape }");
		fwrite($fp,"-->");
		fwrite($fp,"</STYLE></HEAD><BODY LANG='it-IT'>");
		fwrite($fp,"<h3>Periodo dal ".$_POST['giorno_inizio']."-".$_POST['mese_inizio']."-".$_POST['anno_inizio']." al ".$_POST['giorno']."-".$_POST['mese']."-".$_POST['anno']."<br>");
		if ($_POST['id_classe']=='0')	
			fwrite($fp,"Tutti i gruppi di lavoro<br>");
		else
			fwrite($fp,"Gruppo di lavoro: ".$classe."<br>");
		if ($_POST['id_reparto']!='0')
			fwrite($fp,"Reparto: ".$riga['nome']."</h3><br>");
		else 
			fwrite($fp,"Tutti i reparti</h3><br>");
	}
	if ($_POST['id_classe']=='0')	{ // tutti i gruppi
			if ($_POST['id_reparto']=='0')	 // tutti i gruppi e tutti i reparti
				$sql =& $link->query("CREATE VIEW stat (id_studente, cognome, nome_stud, sesso, n_stato, straniero, ripetente, classe, ordine, esame, HC, data_inizio, data_fine, id_degenza, tipo_degenza) AS SELECT Studenti.id_studente, cognome, Studenti.nome, sesso, n_stato, straniero, ripetente, Studenti.classe, Studenti.ordine, esame, HC, data_inizio, data_fine,  Degenze.id_degenza, tipo_degenza FROM Studenti, Degenze, Reparti, Reparto, Classi, Classe WHERE Studenti.id_studente = Degenze.id_studente AND Reparto.id_degenza = Degenze.id_degenza AND Reparto.id_reparto = Reparti.id_reparto AND Classi.id_classe = Classe.id_classe AND Classe.id_degenza = Degenze.id_degenza AND Degenze.data_inizio <= '".$_POST['anno']."-".$_POST['mese']."-".$_POST['giorno']."' AND (Degenze.data_fine = '0000-00-00' OR Degenze.data_fine >='".$_POST['anno_inizio']."-".$_POST['mese_inizio']."-".$_POST['giorno_inizio']."') ORDER BY cognome");
			else // tutti i gruppi e un reparto
				$sql =& $link->query("CREATE VIEW stat (id_studente, cognome, nome_stud, sesso, n_stato, straniero, ripetente, classe, ordine, esame, HC, data_inizio, data_fine, id_degenza, tipo_degenza) AS SELECT Studenti.id_studente, cognome, Studenti.nome, sesso, n_stato, straniero, ripetente, Studenti.classe, Studenti.ordine, esame, HC, data_inizio, data_fine,  Degenze.id_degenza, tipo_degenza FROM Studenti, Degenze, Reparti, Reparto, Classi, Classe WHERE Studenti.id_studente = Degenze.id_studente AND Reparto.id_degenza = Degenze.id_degenza AND Reparto.id_reparto = Reparti.id_reparto AND Reparti.id_reparto = {$_POST['id_reparto']} AND Classi.id_classe = Classe.id_classe AND Classe.id_degenza = Degenze.id_degenza AND Degenze.data_inizio <= '".$_POST['anno']."-".$_POST['mese']."-".$_POST['giorno']."' AND (Degenze.data_fine = '0000-00-00' OR Degenze.data_fine >='".$_POST['anno_inizio']."-".$_POST['mese_inizio']."-".$_POST['giorno_inizio']."') ORDER BY cognome");
	}	
	else { // gruppo di lavoro richiesto
		if ($_POST['id_reparto']!='0')	// un gruppo e un reparto
			$sql =& $link->query("CREATE VIEW stat (id_studente, cognome, nome_stud, sesso, n_stato, straniero, ripetente, classe, ordine, esame, HC, data_inizio, data_fine, id_degenza, tipo_degenza) AS SELECT Studenti.id_studente, cognome, Studenti.nome, sesso, n_stato, straniero, ripetente, Studenti.classe, Studenti.ordine, esame, HC, data_inizio, data_fine,  Degenze.id_degenza, tipo_degenza FROM Studenti, Degenze, Reparti, Reparto, Classi, Classe WHERE Studenti.id_studente = Degenze.id_studente AND Reparto.id_degenza = Degenze.id_degenza AND Reparto.id_reparto = Reparti.id_reparto AND Reparti.id_reparto = {$_POST['id_reparto']} AND Classi.id_classe = Classe.id_classe AND Classe.id_degenza = Degenze.id_degenza AND Classe.id_classe = {$_POST['id_classe']} AND Degenze.data_inizio <= '".$_POST['anno']."-".$_POST['mese']."-".$_POST['giorno']."' AND (Degenze.data_fine = '0000-00-00' OR Degenze.data_fine >='".$_POST['anno_inizio']."-".$_POST['mese_inizio']."-".$_POST['giorno_inizio']."') ORDER BY cognome");
		else // un gruppo e tutti i reparti
			$sql =& $link->query("CREATE VIEW stat (id_studente, cognome, nome_stud, sesso, n_stato, straniero, ripetente, classe, ordine, esame, HC, data_inizio, data_fine, id_degenza, tipo_degenza) AS SELECT Studenti.id_studente, cognome, Studenti.nome, sesso, n_stato, straniero, ripetente, Studenti.classe, Studenti.ordine, esame, HC, data_inizio, data_fine,  Degenze.id_degenza, tipo_degenza FROM Studenti, Degenze, Reparti, Reparto, Classi, Classe WHERE Studenti.id_studente = Degenze.id_studente AND Reparto.id_degenza = Degenze.id_degenza AND Reparto.id_reparto = Reparti.id_reparto AND Classi.id_classe = Classe.id_classe AND Classe.id_degenza = Degenze.id_degenza AND Classe.id_classe = {$_POST['id_classe']} AND Degenze.data_inizio <= '".$_POST['anno']."-".$_POST['mese']."-".$_POST['giorno']."' AND (Degenze.data_fine = '0000-00-00' OR Degenze.data_fine >='".$_POST['anno_inizio']."-".$_POST['mese_inizio']."-".$_POST['giorno_inizio']."') ORDER BY cognome");
	}
	errore_DB($sql);
	$data_odierna=date("Y-m-d");
	$sql =& $link->query("SELECT id_studente, cognome, nome_stud, sesso, straniero, ripetente, classe, ordine, esame, HC, data_inizio, data_fine, datediff(data_fine, data_inizio) AS durata, id_degenza, tipo_degenza FROM stat");
	errore_DB($sql);
	$num_deg_giorno=0;
	$num_deg_brevi=0;
	$num_deg_medie=0;
	$num_deg_lunghe=0;
	
	if ($_POST["tipo_report"]=="V") {
?>
	<table class="elenco">
	<tr>
		<th>Cognome</th>
		<th>Nome</th>
		<th>Sesso</th>
		<th>Straniero</th>
		<th>Classe</th>
		<th>Ripetente</th>
		<th>HC</th>
		<th>Data inizio</th>
		<th>Data fine</th>
		<th>Durata</th>
		<th>Presenze</th>
		<th>Tipo</th>
		<th>Note</th>
	</tr>
<? }
   else { 
 	fwrite($fp,"<table BORDER=1 BORDERCOLOR='#000000' CELLPADDING=4 CELLSPACING=0>");
 	fwrite($fp,"<tr><th>Cognome</th><th>Nome</th><th>Sesso</th><th>Straniero</th><th>Classe</th><th>Ripetente</th><th>HC</th><th>Data inizio</th><th>Data fine</th><th>Durata</th><th>Presenze</th><th>Tipo</th><th>Note</th></tr>");
   }

	$pari=1; 
	// Per ogni studente visualizza le statistiche
	while ($riga =& $sql->fetchRow())
	{ //while01
		$class = ($pari) ? "pari" : "dispari";
		$pari = 1-$pari;
		$data_inizio= preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['data_inizio']);
		$data_fine= preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['data_fine']);
		if ($data_fine=='00-00-0000') { 
			$data_fine="Frequentante"; 		
			list($giorno, $mese, $anno) = explode("-",$data_inizio); 
			// calcolo la differenza tra il timestamp della data definita e la data attuale
			// il risultato deve essere diviso per 86400 (il numero di secondi in un giorno)
			// e arrotondato con floor 
			$giorni = ((time()-mktime (0,0,0,$mese,$giorno,$anno))/86400);
			$riga['durata']=floor($giorni);
		}
		$riga['durata']++;
		if (!$frequenze[$riga['id_studente']])
					$frequenze[$riga['id_studente']]=$riga['durata'];
			else $frequenze[$riga['id_studente']]+=$riga['durata'];
				
		// calcolo presenze
		$sql_presenze =& $link->query("SELECT COUNT(DISTINCT data) as tot_presenze FROM Studenti,Degenze,Registro WHERE Studenti.id_studente=\"".$riga['id_studente']."\" AND Studenti.id_studente = Degenze.id_studente AND Degenze.id_degenza=Registro.id_degenza AND (Registro.ruolo=3 OR Registro.ruolo=1 OR Registro.ruolo=2) AND (Registro.data BETWEEN '".$_POST['anno_inizio']."-".$_POST['mese_inizio']."-".$_POST['giorno_inizio']."' and '".$_POST['anno']."-".$_POST['mese']."-".$_POST['giorno']."')");
		errore_DB($sql);
		$riga_presenze =& $sql_presenze->fetchRow();
		$tot_presenze=$riga_presenze['tot_presenze']; 
			
		// Verifico che lo studente non abbia cambiato reparto
		$nota="";
		$sql_reparto =& $link->query("SELECT * FROM Reparto,Reparti WHERE
				 	Reparto.id_degenza={$riga['id_degenza']} AND 
				 	Reparto.id_reparto=Reparti.id_reparto ORDER BY Reparto.id_repdeg");
		$num_righe_reparto = $sql_reparto->numRows();
		if ( $num_righe_reparto != 1)
		{
			$nota="Lo studente &egrave; stato nei seguenti reparti:";
			//inserisco una nota
			 while ($riga_reparto =& $sql_reparto->fetchRow())
			 {
				$nota=$nota."&nbsp;{$riga_reparto['nome']}";			
				if ($riga_reparto['data_cambio'] != '0000-00-00') {
						$data_cambio = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga_reparto['data_cambio']);
						$nota=$nota." (dal ".$data_cambio.") ";
				}
				else    $nota=$nota." (inizio frequenza) ";
			 }
		} // end if
		switch($riga['ordine']) {
  					case 'M': $ordine='Scuola dell\'infanzia'; break;
  					case 'P': $ordine='Scuola primaria'; break;
  					case 'I': $ordine='Scuola secondaria di primo grado'; break;
  					case 'S': $ordine='Scuola secondaria';
 				 }
		if ($_POST["tipo_report"]=="V") 
			print "<tr class='".$class."'><td>".$riga['cognome']."</td><td>".$riga['nome_stud']."</td><td>".$riga['sesso']."</td><td>".($riga['straniero'] == "0" ? "NO" : "SI")."</td><td>".$riga['classe']." ".$ordine."</td><td>".($riga['ripetente'] == "0" ? "NO" : "SI")."</td><td>".($riga['HC'] == "0" ? "NO" : "SI")."</td><td>".$data_inizio."</td><td>".$data_fine."</td><td>".$riga['durata']."</td><td>".$tot_presenze."</td><td>".$riga['tipo_degenza']."</td><td>".$nota."</td></tr>";
		else
		   fwrite($fp,"<tr class='".$class."'><td>".$riga['cognome']."</td><td>".$riga['nome_stud']."</td><td>".$riga['sesso']."</td><td>".($riga['straniero'] == "0" ? "NO" : "SI")."</td><td>".$riga['classe']." ".$ordine."</td><td>".($riga['ripetente'] == "0" ? "NO" : "SI")."</td><td>".($riga['HC'] == "0" ? "NO" : "SI")."</td><td>".$data_inizio."</td><td>".$data_fine."</td><td>".$riga['durata']."</td><td>".$tot_presenze."</td><td>".$riga['tipo_degenza']."</td><td>".$nota."</td></tr>");
	}	// end while01
	
	if ($_POST["tipo_report"]=="V") 
		print '</table>';
	else
		fwrite($fp,"</table>");
		
	$sql =& $link->query("SELECT COUNT(DISTINCT id_studente) as num_tot FROM stat");
	errore_DB($sql);
	$riga =& $sql->fetchRow();
	$num_tot=$riga['num_tot']; 
	 
	$sql =& $link->query("SELECT COUNT(DISTINCT id_studente) as num_maschi FROM stat WHERE sesso='M'");
	errore_DB($sql);
	$riga =& $sql->fetchRow();
	$num_maschi=$riga['num_maschi'];
	
	$sql =& $link->query("SELECT COUNT(DISTINCT id_studente) as num_fem FROM stat WHERE sesso='F'");
	errore_DB($sql);
	$riga =& $sql->fetchRow();
	$num_femmine=$riga['num_fem'];
	
	$sql =& $link->query("SELECT COUNT(DISTINCT id_studente) as num_HC FROM stat WHERE HC='1'");
	errore_DB($sql);
	$riga =& $sql->fetchRow();
	$num_HC=$riga['num_HC'];
	
	$sql =& $link->query("SELECT COUNT(DISTINCT id_studente) as num_stranieri FROM stat WHERE straniero='1'");
	errore_DB($sql);
	$riga =& $sql->fetchRow();
	$num_stranieri=$riga['num_stranieri'];
	
	$sql =& $link->query("SELECT COUNT(*) as num_DH FROM stat WHERE tipo_degenza='DH'");
	errore_DB($sql);
	$riga =& $sql->fetchRow();
	$num_DH=$riga['num_DH'];
	
	$sql =& $link->query("SELECT COUNT(*) as num_DO FROM stat WHERE tipo_degenza='DO'");
	errore_DB($sql);
	$riga =& $sql->fetchRow();
	$num_DO=$riga['num_DO'];
	
	if ($_POST["tipo_report"]=="V") {
?>	 
	<h3>Statistiche riassuntive</h3>
	<table class="elenco">
	<tr>
		<th>Frequenze di un giorno</th>
		<th>Frequenze brevi</th>
		<th>Frequenze medie</th>
		<th>Frequenze lunghe</th>
		<th>Maschi</th>
		<th>Femmine</th>
		<th>Totale studenti</th>
		<th>Stranieri</th>
		<th>HC</th>
	</tr>
<?
	}
	else
		fwrite ($fp,"<h3>Statistiche riassuntive</h3><table BORDER=1 BORDERCOLOR='#000000' CELLPADDING=4 CELLSPACING=0><tr><th>Frequenze di un giorno</th><th>Frequenze brevi</th><th>Frequenze medie</th><th>Frequenze lunghe</th><th>Maschi</th><th>Femmine</th><th>Totale studenti</th><th>Stranieri</th><th>HC</th></tr>");
	for ($i=0; $i<count($frequenze); $i++) {
		$cur=current($frequenze);
		if($cur==1)
			$num_deg_giorno++;
		else
		   if($cur>1 && $cur<8) 
		   	$num_deg_brevi++;
			else 
				if($cur>7 && $cur<16) 
					$num_deg_medie++;
				else 
					$num_deg_lunghe++;
		next($frequenze);	
	}
	
	if ($_POST["tipo_report"]=="V")
		print "<tr class='pari'><td>".$num_deg_giorno."</td><td>".$num_deg_brevi."</td><td>".$num_deg_medie."</td><td>".$num_deg_lunghe."</td><td>".$num_maschi."</td><td>".$num_femmine."</td><td>".$num_tot."</td><td>".$num_stranieri."</td><td>".$num_HC."</td></tr></table>";
	else
		fwrite($fp,"<tr><td>".$num_deg_brevi."</td><td>".$num_deg_brevi."</td><td>".$num_deg_medie."</td><td>".$num_deg_lunghe."</td><td>".$num_maschi."</td><td>".$num_femmine."</td><td>".$num_tot."</td><td>".$num_stranieri."</td><td>".$num_HC."</td></tr></table>");
	
	// Calcolo statistiche numero studenti suddivisi per classe e ordine di scuola	
	if ($_POST["tipo_report"]=="V") {
?>	 
	<br /><table class="elenco">
	<tr>
		<th>Ordine di scuola</th>
		<th>Classe</th>
		<th>Numero totale studenti</th>
	</tr>
<?
	}
	else
		fwrite ($fp,"<br /><table BORDER=1 BORDERCOLOR='#000000' CELLPADDING=4 CELLSPACING=0><tr><th>Ordine di scuola</th><th>Classe</th><th>Numero totale studenti</th></tr>");

	for ($i = 1; $i <= 3; $i++) {
		$sql =& $link->query("SELECT COUNT(*) as num FROM stat WHERE classe='".$i."' AND ordine='M'");
		errore_DB($sql);
		$riga =& $sql->fetchRow();
		if ($_POST["tipo_report"]=="V")
			print "<tr class='pari'><td>Scuola dell'infanzia</td><td>".$i."</td><td>".$riga['num']."</td></tr>";
		else
			fwrite($fp,"<tr><td>Scuola dell'infanzia</td><td>".$i."</td><td>".$riga['num']."</td></tr>");
	}
	for ($i = 1; $i <= 5; $i++) {
		$sql =& $link->query("SELECT COUNT(*) as num FROM stat WHERE classe='".$i."' AND ordine='P'");
		errore_DB($sql);
		$riga =& $sql->fetchRow();
		if ($_POST["tipo_report"]=="V")
			print "<tr class='pari'><td>Scuola primaria</td><td>".$i."</td><td>".$riga['num']."</td></tr>";
		else
			fwrite($fp,"<tr><td>Scuola primaria</td><td>".$i."</td><td>".$riga['num']."</td></tr>");
	}
	for ($i = 1; $i <= 3; $i++) {
		$sql =& $link->query("SELECT COUNT(*) as num FROM stat WHERE classe='".$i."' AND ordine='I'");
		errore_DB($sql);
		$riga =& $sql->fetchRow();
		if ($_POST["tipo_report"]=="V")
			print "<tr class='pari'><td>Scuola secondaria di primo grado</td><td>".$i."</td><td>".$riga['num']."</td></tr>";
		else
			fwrite($fp,"<tr><td>Scuola secondaria di primo grado</td><td>".$i."</td><td>".$riga['num']."</td></tr>");
	}
	for ($i = 1; $i <= 5; $i++) {
		$sql =& $link->query("SELECT COUNT(*) as num FROM stat WHERE classe='".$i."' AND ordine='S'");
		errore_DB($sql);
		$riga =& $sql->fetchRow();
		if ($_POST["tipo_report"]=="V")
			print "<tr class='pari'><td>Scuola secondaria</td><td>".$i."</td><td>".$riga['num']."</td></tr>";
		else
			fwrite($fp,"<tr><td>Scuola secondaria</td><td>".$i."</td><td>".$riga['num']."</td></tr>");
	}
		if ($_POST["tipo_report"]=="V")
			print "</table>";
		else
			fwrite($fp,"</table>");
	
// Calcolo statistiche numero studenti seguiti all'esame di stato
	if ($_POST["tipo_report"]=="V") {
?>	 
	<br />
	<table class="elenco">
	<tr>
		<th>Ordine di scuola</th>
		<th>Numero totale studenti seguiti all'esame di stato</th>
	</tr>
<?
	}
	else
		fwrite ($fp,"<br /><table BORDER=1 BORDERCOLOR='#000000' CELLPADDING=4 CELLSPACING=0><tr><th>Ordine di scuola</th><th>Numero totale studenti</th></tr>");

		$sql =& $link->query("SELECT COUNT(*) as num FROM stat WHERE esame='1' AND ordine='I'");
		errore_DB($sql);
		$riga =& $sql->fetchRow();
		if ($_POST["tipo_report"]=="V")
			print "<tr class='pari'><td>Scuola secondaria di primo grado</td><td>".$riga['num']."</td></tr>";
		else
			fwrite($fp,"<tr><td>Scuola secondaria di primo grado</td><td>".$riga['num']."</td></tr>");
		$sql =& $link->query("SELECT COUNT(*) as num FROM stat WHERE esame='1' AND ordine='S'");
		errore_DB($sql);
		$riga =& $sql->fetchRow();
		if ($_POST["tipo_report"]=="V")
			print "<tr class='pari'><td>Scuola secondaria</td><td>".$riga['num']."</td></tr>";
		else
			fwrite($fp,"<tr><td>Scuola secondaria</td><td>".$riga['num']."</td></tr>");
		if ($_POST["tipo_report"]=="V")
			print "</table>";
		else
			fwrite($fp,"</table>");
	
	
	if ($_POST["tipo_report"]=="V") {
?>
    <h3>Statistiche generali sul tipo di degenza</h3>
    <table class="elenco">
	<tr>
	<th>Day hospital</th>
	<th>Degenze ordinarie</th>
	</tr><tr class='pari'>
	<?
	print	"<td>".$num_DH."</td><td>".$num_DO."</td></tr></table>";
	}
	else {
		fwrite ($fp,"<h3>Statistiche generali sul tipo di degenze</h3><table BORDER=1 BORDERCOLOR='#000000' CELLPADDING=4 CELLSPACING=0><tr><th>Day hospital</th><th>Degenze ordinarie</th></tr><tr>");
		fwrite ($fp,"<td>".$num_DH."</td><td>".$num_DO."</td></tr></table>");
   }

//	
	
	
	if ($_POST["tipo_report"]=="V") {
?>
    <h3>Statistiche generali sul tipo di degenza</h3>
    <table class="elenco">
	<tr>
	<th>Day hospital</th>
	<th>Degenze ordinarie</th>
	</tr><tr class='pari'>
	<?
	print	"<td>".$num_DH."</td><td>".$num_DO."</td></tr></table>";
	}
	else {
		fwrite ($fp,"<h3>Statistiche generali sul tipo di degenze</h3><table BORDER=1 BORDERCOLOR='#000000' CELLPADDING=4 CELLSPACING=0><tr><th>Day hospital</th><th>Degenze ordinarie</th></tr><tr>");
		fwrite ($fp,"<td>".$num_DH."</td><td>".$num_DO."</td></tr></table>");
   }

// Statistiche stranieri
    $sql =& $link->query("SELECT n_stato,count(*) as num_stranieri FROM stat WHERE straniero='1' group by n_stato");
	errore_DB($sql);
	$num_righe = $sql->numRows();
	if ($_POST["tipo_report"]=="V") { //ifstr1
		if (!$num_righe)
				print	"<h3>Nessuno studente straniero</h3>";
		else {
				$pari=1; 
?>
				<h3>Statistiche generali stranieri</h3>  
				<table class="elenco">
				<tr>
				<th>Paese origine</th>
				<th>Numero studenti</th>
				</tr><tr class='pari'>
<?
				while ( $riga  =& $sql->fetchRow()) {
					$class = ($pari) ? "pari" : "dispari";
					$pari = 1-$pari;
					print "<tr class='".$class."'><td>".$riga['n_stato']."</td><td>".$riga['num_stranieri']."</td></tr>";
				}
				print "</table></body>";
			
		}
	} //end ifstr1
	else //download report
	{
		if (!$num_righe)
			fwrite ($fp,"<h3>Statistiche generali stranieri</h3>Nessuno studente straniero");
		else {
			fwrite ($fp,"<h3>Statistiche generali stranieri</h3><table BORDER=1 BORDERCOLOR='#000000' CELLPADDING=4 CELLSPACING=0><tr><th>Paese origine</th><th>Numero studenti</th></tr><tr>");
			while ( $riga  =& $sql->fetchRow()) 
				fwrite ($fp,"<td>".$riga['n_stato']."</td><td>".$riga['num_stranieri']."</td></tr>");
			fwrite ($fp,"</table></body>");
		}
		echo '<form method="post" action="./Download.php">';
		echo '<input type="hidden" name="file" value="'.$file.'" />';
		echo '<input type="hidden" name="dir" value="'.$dir.'" /><br><br><center><input type="submit" value="Scarica file statistiche"></center><br><br></form><br><br>'; 
    } //else download report
  
$sql =& $link->query("DROP VIEW stat");
errore_DB($sql);
}


print "<h3>Prospetto degenze multiple</h3>";
echo '<form method="post" action="./ProspettoDegenze.php">';
echo '<center><input type="submit" value="Genera report"></center></form>'; 
		
echo "<br><br><center><a href=\"indice.php\"><img src=\"immagini/menu.png\"></a><br>Pagina Iniziale</center>";
include "Coda.inc";

?>
