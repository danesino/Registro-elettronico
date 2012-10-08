<?php
include_once "FunzioniGrafiche.inc";

function inserisci_scuola($dati)
// funzione che inserisce una nuova scuola, prende come argomento
// tutti i dati in forma di array dei vari campi della query
{   
    $tabella = 'Scuole';
    global $link; 
    if(in_DB($tabella,'nome',$dati['nome']))
        box_errore("Errore: La scuola <b>{$dati['nome']}</b> &egrave; gi&agrave; presente");
    else
    { 
        $res = $link->autoExecute($tabella, $dati, DB_AUTOQUERY_INSERT);
        if (PEAR::isError($res))
            box_errore("Errore Inserisci_scuola ".$res->getMessage());
        else 
            box_successo("Inserimento della scuola <b>{$dati['nome']}</b> effettuato con successo<br /><br /> per inserire un altra scuola clicca <a href=\"./InserimentoScuola.php\">qui</a>");
    }
}

function in_DB($tabella,$where_campo,$where_contenuto)
{
    global $link;
    $elenco =& $link->query("SELECT * FROM $tabella WHERE $where_campo = ? LIMIT 1", $where_contenuto);
    return $elenco->numRows();
}

function inserisci_reparto($dati)
{
	 global $link;
	if (in_DB('Reparti','nome',$dati['nome']))
		die("<dd>Il reparto <b>{$dati['nome']}</b> &egrave; gi&agrave; presente</dd>");
	else
	{
		 $a = array_keys($dati);
	 	 $b = array_values($dati);
		 $sth = $link->autoPrepare('Reparti', $a, DB_AUTOQUERY_INSERT);
		 if (PEAR::isError($sth)) {
			      die($sth->getMessage());
		 }

		 $res =& $link->execute($sth, $b);
		 if (PEAR::isError($res))
			  box_errore("Errore Inserisci Reparto ".$res->getMessage());
		 else
  			  box_successo("Inserimento del Reparto <b>{$dati['nome']}</b> effettuato con successo<br /><br />Per inserire un altro reparto cliccare <a href=\"./GestioneReparti.php\">qui</a>");
	}
}

function modifica_reparto($dati)
{
	global $link;
  if (in_DB('Reparti','nome',$dati['nome']))
    die("<dd>Il reparto <b>{$dati['nome']}</b> &egrave; gi&agrave; presente</dd>");
  else
	{
		print_r($dati);
	  $id=array_pop($dati);
		$res = $link->autoExecute('Reparti', $dati, DB_AUTOQUERY_UPDATE, "id_reparto=".$id);
  	if (PEAR::isError($res))
    	box_errore("Errore Modifica Reparto ".$res->getMessage());
  	else
    	box_successo("Reparto <b>{$dati['nome']}</b> modificato con successo<br /><br />Per modificare un altro reparto cliccare <a href=\"./GestioneReparti.php\">qui</a>");
	}
}

function autoinsert($table_name, $table_fields,$table_values)
{
	global $link;
	
  $sth = $link->autoPrepare($table_name, $table_fields, DB_AUTOQUERY_INSERT);

	if (PEAR::isError($sth)) {
    	echo "<dl><dd>".$sth->getMessage()."<dd/><dl/>";
	}

	$res =& $link->execute($sth, $table_values);

	if (PEAR::isError($res)) {
    echo "<dl><dd>".$res->getMessage()."<dd/><dl/>";
	}
}

function autoupdate($table_name, $table_fields,$table_values)
{
	global $link;
  $sth = $link->autoPrepare($table_name, $table_fields, DB_AUTOQUERY_UPDATE);

	if (PEAR::isError($sth)) {
    	echo "<dl><dd>".$sth->getMessage()."</dd><dl/>";
	}
	$res =& $link->execute($sth, $table_values);


	if (PEAR::isError($res)) {
    echo "<dl><dd>".$res->getMessage()."</dd><dl/>";
	}
}

function selectall($table_name){
	global $link;
	$r =& $link->query("SELECT * FROM $table_name");
  if (PEAR::isError($r)) {
      echo "<dl><dd>".$r->getMessage()."</dd><dl/>";
  }
  else{
		while($r->fetchInto($obj))
  {
		$array[] = $obj;
  }
    return $array;
  }
}

function errore_DB($r){
  if (PEAR::isError($r))
  {
    die("<dl><dd>".$r->getMessage()."</dd></dl>");
  }
}

?>
