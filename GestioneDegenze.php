<?php

// ----------------------------------------------------------------------
// Registro elettronico scuole ospedaliere
// ----------------------------------------------------------------------
// LICENSE and CREDITS
// This program is free software and it's released under the terms of the
// GNU General Public License(GPL) 
// Link: http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
// ----------------------------------------------------------------------
// Nome file:  GestioneDegenze.php
// Autore di questo file: Sophia Danesino, Puria Nafisi
// Descrizione: consente gestione dei periodi di degenza degli studenti:
// il cambio di reparto e la fine del periodo di degenza
// 10/7/2010: aggiunto campo data quando si cambia reparto
// ----------------------------------------------------------------------
// Autorizzazione: ruolo amministratore, operatore
// ----------------------------------------------------------------------
	
$title = "Inserimento Studente";

include "Testa.inc";
include "FunzioniDB.inc";
autorizza_ruoli(ID_ADMIN,ID_OPERATORE);
require_once 'HTML/QuickForm.php';

include("data.inc");
	
$sql = 'SELECT * FROM Studenti WHERE id_studente ='. $link->quoteSmart($_POST['id']);
$res = $link->query($sql);
$riga = $res->fetchRow();

print "<h2>Gestione periodi di frequenza alla scuola ospedaliera di {$riga['cognome']} {$riga['nome']} </h2>";
	
// fine periodo di degenza
	
if (isset($_POST['fine_degenza']))
{
	 $data=$_POST['anno']."-".$_POST['mese']."-".$_POST['giorno'];
	 $inizio=$_POST['anno']."-".$_POST['mese']."-".$_POST['giorno'];
	 $res =& $link->query("UPDATE Degenze SET data_fine='$data' WHERE id_degenza='{$_POST['id_degenza']}'");
	 errore_DB($res);
	 $res =& $link->query("UPDATE Classe SET attivo='N' WHERE id_degenza ='{$_POST['id_degenza']}' AND attivo='S'");
	 errore_DB($res);
	 $res =& $link->query("UPDATE Reparto SET attivo='N' WHERE id_degenza ='{$_POST['id_degenza']}' AND attivo='S'");
	 errore_DB($res);
	 
	 // se lo studente è a scuola non viene aperta alcuna degenza altrimenti è automaticamente inserito a scuola
	 /* $sql = "SELECT * FROM Reparto WHERE id_degenza =". $_POST['id_degenza'];
	 $res =& $link->query($sql);
	 $reparto =& $res->fetchRow();
	 if ($reparto['id_reparto']) {
		$res =& $link->query("INSERT INTO Degenze (id_studente, data_inizio) VALUES ( '{$_POST['id']}' , '$inizio' )");
		errore_DB($res);
		$id_degenza =  mysql_insert_id();
		$sql = "INSERT INTO Reparto (id_degenza, id_reparto, tipo_degenza, attivo ) VALUES (".$id_degenza.",0, 'SC', 'S')";
		$res =& $link->query($sql);
	 }
	 */
	 errore_DB($res);
}

// cambio reparto
	
if (isset($_POST['nuovo_reparto']))
{
 	 // verifico che non sia già inserito nello stesso reparto
	 $sql = "SELECT * FROM Reparto WHERE id_degenza =". $_POST['id_degenza'] ." AND id_reparto=".$_POST['id_reparto'].' AND tipo_degenza="'.$_POST['tipo'].'" AND attivo="S"';
	 $res =& $link->query($sql);
	 $num_righe = $res->numRows();
	 if ( $num_righe != 0)
		  die("<dl><dd>Registro - Errore: lo studente è già inserito in questo reparto con questo tipo di degenza</dd></dl>");	
	 $sql = 'UPDATE Reparto SET attivo="N" WHERE id_degenza ="'. $_POST['id_degenza'] .'" AND attivo="S"';
	 $res =& $link->query($sql);
	 errore_DB($res); 
	 $data=$_POST['anno']."-".$_POST['mese']."-".$_POST['giorno'];
	 $sql = "INSERT INTO Reparto (id_degenza, id_reparto, tipo_degenza, attivo, data_cambio )	VALUES ( '{$_POST['id_degenza']} ', '{$_POST['id_reparto']}','{$_POST['tipo']}' , 'S', '$data')";
	 $res =& $link->query($sql);
	 errore_DB($res);
}
	
// inserimento studente in un reparto ospedaliero 
	
if (isset($_POST['inserisci']))
{
	 $data=$_POST['anno']."-".$_POST['mese']."-".$_POST['giorno'];
	 // verifico che non sia già stato inserito
	 $sql = "SELECT * FROM Degenze WHERE id_studente =". $_POST['id'] .' AND year(data_inizio) < 1000';
	 $res =& $link->query($sql);
	 $num_righe = $res->numRows();
	 if ( $num_righe > 0)
	 		$res =& $link->query("UPDATE Degenze SET data_fine=\"$data\" WHERE year(data_inizio) < 1000 and id_studente = {$_POST['id']}");
	 $sql = "SELECT * FROM Degenze WHERE id_studente =". $_POST['id'] .' AND data_fine="0000-00-00" and year(data_inizio) > 1000';
	 $res =& $link->query($sql);
	 $num_righe = $res->numRows();
	 if ( $num_righe != 0)
		  die("<dl><dd>Registro - Errore: lo studente è già inserito in un reparto</dd></dl>");	
	 $sql = "INSERT INTO Degenze (id_studente, data_inizio ) VALUES ( '{$_POST['id']} ','$data' )";
	 $res =& $link->query($sql);
	 errore_DB($res);
	 $id_degenza = mysql_insert_id();
	 $sql = "INSERT INTO Reparto (id_degenza, id_reparto, tipo_degenza, data_cambio ) VALUES ( '$id_degenza ', '{$_POST['id_reparto']}','{$_POST['tipo']}', '0000-00-00' )";
	 $res =& $link->query($sql);
	 errore_DB($res);
}
	
$sql = "SELECT * FROM Degenze WHERE id_studente =". $_POST['id'] .' AND data_fine="0000-00-00" AND year(data_inizio) > 1000';
$res =& $link->query($sql);
$num_righe = $res->numRows();
if ( $num_righe == 0):?>
	<br /><br />
	<table>
	<tr>
		<td bgcolor="#C1DADF" align="center" >Inizio frequenza</td>
		<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<input type="hidden" name="id" value="<?=$_POST['id']?>" />
			<input type="hidden" name="inserisci" value="1" />
		<td  nowrap="nowrap"><?data_odierna();?></td></tr>
	<tr>
		<td bgcolor="#C1DADF" align="center" >Reparto</td>
		<td  nowrap="nowrap">
<?
	@$reparto = selectall("Reparti");
	print '<select  name="id_reparto" >';
	print_r($reparto);
	if($reparto === 0){
		die("<dd>Non sono stati inseriti Reparti, per inserirne uno Clicca <a href='GestioneReparti.php'>qui</a></dd>");
	}
	else
	{
	 foreach ($reparto as $reparto)
		  print "\t<option name=\"id_reparto\" value=\"{$reparto['id_reparto']}\">{$reparto['nome']}</option>\n";
	}
	print "</select>";
?>
	</td></tr>
	<tr>
		<td bgcolor="#C1DADF" align="center" >Tipo degenza</td>
		<td  nowrap="nowrap"><select  name="tipo" >
			<option value="DH">Day hospital</option>
			<option value="DO">Degenza ordinaria</option>
			<option value="SC">Scuola non ospedaliera</option>
		</select></td></tr>
	<tr> 
		<td><br /><input type="submit" value="Nuova degenza"></td></tr>
</table>
		
<?	else :?>
<h3>Situazione corrente</h3>
<br /><center>
	<table class="elenco">
	<tr>
		<th>Inizio frequenza</th>
		<td bgcolor="#DDDDDD">
<?		
$riga =& $res->fetchRow();
$data = preg_replace("|\b(\d+)-(\d+)-(\d+)\b|","\\3-\\2-\\1",$riga['data_inizio']);
print "$data</td></tr>";
print '<tr><th>Reparto</th>';
print '<td bgcolor="#DDDDDD">';
$sql_reparto = 'SELECT * FROM Reparto, Reparti WHERE Reparto.id_degenza='. $riga['id_degenza'].' AND Reparto.id_reparto = Reparti.id_reparto AND Reparto.attivo="S"';
$res =& $link->query($sql_reparto);
$num_righe = $res->numRows();
if ($num_righe == 0)
{
	 die("<dl><dd>Non &egrave; stato inserito nessun reparto. Per continuare &egrave; necessario che ci sia almeno un reparto.<br />Per inserirne uno cliccare <a href='GestioneReparti.php'>qui</a></dd></dl>");
} else {
$reparto =& $res->fetchRow();

print "{$reparto['nome']}</td></tr>";
if ($reparto['id_reparto']!=0) {
	print '<tr><th>Tipo frequenza</th>';
	print '<td bgcolor="#DDDDDD">';
	print ($reparto['tipo_degenza']=='DH') ? "Day Hospital</td>" : "Degenza ordinaria</td>"; 		
}
}
?>
</table></center>
<!--pulsante di fine degenza-->
<br /><br /><br /><br /><br />

<h3>Dimissioni: per segnalare la fine frequenza</h3>
<br />
	<table>
	<tr><form action="<?=$_SERVER['PHP_SELF']?>" method="post">
		<td><?data_odierna()?></td>
		<input type="hidden" name="id" value="<?=$_POST['id']?>">
		<input type="hidden" name="id_degenza" value="<?=$riga['id_degenza']?>">
		<input type="hidden" name="fine_degenza" value="1">
		<td><input type="submit" value="Fine frequenza"></td></form>
	</tr>
	</table><br />
<!--pulsante di cambio reparto-->

<?		
if ($reparto['id_reparto']!=0) {
	?>
<h3>Cambio reparto: per cambiare il reparto ospedaliero o tipo di degenza</h3>
	<table>
	<tr><form action="<?=$_SERVER['PHP_SELF']?>" method="post">
	<td><?data_odierna()?></td>
<?		
    $sql_reparti = "SELECT * FROM Reparti";
	$query_reparti = mysql_query($sql_reparti);
	print '<td><select  name="id_reparto" >';
	while ($reparto  =  mysql_fetch_object($query_reparti))
		print "\t<option name=\"id_reparto\" value=\"$reparto->id_reparto\">$reparto->nome</option>\n";?>
	</select></td>
	
	<td><select  name="tipo" >
	<option value="DH">Day hospital</option>
	<option value="DO">Degenza ordinaria</option>
	</select></td>
	<input type="hidden" name="id_degenza" value="<?=$riga['id_degenza']?>">
	<input type="hidden" name="id" value="<?=$_POST['id']?>">
	<input type="hidden" name="nuovo_reparto" value="1">
	<td><input type="submit" value="Modifica"></td></form>
	</tr></table>

<?	
}
endif; 
$up="VisualizzaStudenti";
include "Coda.inc";
?>
