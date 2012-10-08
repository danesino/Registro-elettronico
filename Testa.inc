<?php
@include "autenticazione.php";
@include "testa_html.inc";
?>
 <div class="container4">
<div class="menu4">
<ul>
	<li class="home"><a href="indice.php">Home</a></li>
	<li class="indietro"><a href="#" onclick="javascript:history.go(-1); return false">Indietro</a></li>
	<li class="stampa"><a href="#" onclick="javascript:window.print(); return false">Stampa</a></li>
	<li class="alunni"><a class="drop" href="#nogo">Studenti<!--[if IE 7]><!--></a><!--<![endif]--><table class="menu"><tbody><tr><td>
	<ul>
		<li class="scuole"><a class="drop" href="#nogo">Scuole<!--[if IE 7]><!--></a><!--<![endif]--><table class="menu"><tbody><tr><td>
    <ul class="right">
      <li><a href="InserimentoScuola.php">Inserimento</a></li>
      <li><a href="VisualizzaScuole.php">Modifica</a></li>
      <li><a href="VisualizzaScuole.php">Elenco</a></li>
    </ul>
    </td></tr></tbody></table><!--[if lte IE 6]></a><![endif]-->  </li>
		<li class="studenti"><a class="drop" href="#nogo">Studenti<!--[if IE 7]><!--></a><!--<![endif]--><table class="menu"><tbody><tr><td>
		<ul>
			<li><a href="InserimentoStudente.php">Inserimento</a></li>
			<li><a href="VisualizzaStudenti.php">Elenco</a></li>
			<li><a href="RicercaStudente.php">Ricerca</a></li>
		</ul>
		</td></tr></tbody></table><!--[if lte IE 6]></a><![endif]-->
		</li>
		<li class="reparti"><a href="GestioneReparti.php">Reparti</a></li>
	</ul>
	</td></tr></tbody></table><!--[if lte IE 6]></a><![endif]--></li>
	<li class="docenti"><a class="drop" href="#nogo">Docenti<!--[if IE 7]><!--></a><!--<![endif]--><table class="menu"><tbody><tr><td>
	<ul>
		<li class="utenti"><a class="drop" href="#nogo">Utenti<!--[if IE 7]><!--></a><!--<![endif]--><table class="menu"><tbody><tr><td>
    <ul class="right">
      <li><a href="InserimentoUtente.php">Inserimento</a></li>
      <li><a href="VisualizzaUtenti.php">Elenco</a></li>
    </ul>
    </td></tr></tbody></table><!--[if lte IE 6]></a><![endif]-->  </li>
		<li class="materie"><a href="GestioneMaterie.php">Materie</a></li>
	</ul>
	</td></tr></tbody></table><!--[if lte IE 6]></a><![endif]--></li>
	<li class="gr_lavoro"><a class="drop" href="#nogo">gr lavoro<!--[if IE 7]><!--></a><!--<![endif]--><table class="menu"><tbody><tr><td>
	<ul>
		<li class="insert"><a href="InserimentoClasse.php">Inserimento</a></li>
		<li class="elenco"><a href="VisualizzaClassi.php">Elenco</a></li>
		<li class="cdc"><a href="GestioneCdC.php">Docenti</a></li>
		<li class="composizione"><a href="GestioneClasse.php">Composizione</a></li>
		<li class="email"><a href="InviaMessaggio.php">Invia Messaggi</a></li>
		<li class="readmail"><a href="VisualizzaMessaggi.php?a=1">Leggi Messaggi</a></li>
		<li class="presenze"><a href="DownloadPresenzeMensili.php">Presenze mensili</a></li>
		<li class="statistiche"><a href="Statistiche.php">Statistiche</a></li>
	</ul>
	</td></tr></tbody></table><!--[if lte IE 6]></a><![endif]-->
	</li>
	<li class="logout"><a href="Logout.php">&nbsp;Esci</a></li>
</ul>
</div>
</div>

<div class="benv">Ciao <strong><?=$NOME." ".$COGNOME?>!</strong> Sei collegato alla Scuola ospedaliera <strong><?=$REG?></strong></div>
<?php if($RUOLO==ID_OSPEDALIERO || $RUOLO==ID_OSSERVATORE || $RUOLO==ID_AFFIDATARIO || $RUOLO==ID_DOMICILIARE) echo "<style type='text/css'>.container4{display:none;}</style>"; ?>
<div id="main">
