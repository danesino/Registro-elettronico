/* Aggiornato a v.5.0 */

DROP TABLE IF EXISTS `CdC`;
CREATE TABLE `CdC` (
	 `id_classe` int(10) unsigned NOT NULL default '0', 
	 `id_utente` int(10) unsigned NOT NULL default '0', 
	 `id_materia` int(10) unsigned NOT NULL default '0'
);
DROP TABLE IF EXISTS `Classe`;
CREATE TABLE `Classe` (
	 `id_clasdeg` int(10) unsigned NOT NULL auto_increment, 
	 `id_classe` int(10) unsigned NOT NULL default '0', 
	 `id_degenza` int(10) unsigned NOT NULL default '0', 
	 `attivo` enum('S','N') NOT NULL default 'S', 
	 PRIMARY KEY  (`id_clasdeg`)
); 
DROP TABLE IF EXISTS `Classi`;
CREATE TABLE `Classi` (
  `id_classe` int(10) unsigned NOT NULL auto_increment,
  `classe` tinytext NOT NULL,
  `ordine` enum('i','1','2','s') NOT NULL default 'i',
  UNIQUE KEY `id` (`id_classe`)
);
DROP TABLE IF EXISTS `Degenze`;
CREATE TABLE `Degenze` (
  `id_degenza` int(10) unsigned NOT NULL auto_increment,
  `id_studente` int(10) unsigned NOT NULL default '0',
  `data_inizio` date NOT NULL default '0000-00-00',
  `data_fine` date default '0000-00-00',
  PRIMARY KEY  (`id_degenza`)
);
DROP TABLE IF EXISTS `Esterni`;
CREATE TABLE `Esterni` (
  `id_utente` int(10) unsigned NOT NULL default '0',
  `id_studente` int(10) unsigned NOT NULL default '0'
);
DROP TABLE IF EXISTS `Materie`;
CREATE TABLE `Materie` (
  `id_materia` int(10) unsigned NOT NULL auto_increment,
  `nome` varchar(30) NOT NULL default '',
  `ordine` smallint(6) NOT NULL,
  UNIQUE KEY `id_materia` (`id_materia`)
);
DROP TABLE IF EXISTS `Ospedale`;
CREATE TABLE `Ospedale` (
  `cod_reg` varchar(10) NOT NULL default '',
  `codice` varchar(10) NOT NULL default '',
  `nome` tinytext NOT NULL,
  `telefono` varchar(30) default NULL,
  `indirizzo` tinytext,
  `fax` varchar(20) default NULL,
  `email` tinytext,
  `denominazione` tinytext,
  `sitoweb` tinytext,
  `cap` varchar(5) default NULL,
  `citta` varchar(30) default NULL,
  `provincia` char(2) default NULL
);
DROP TABLE IF EXISTS `Registro`;
CREATE TABLE IF NOT EXISTS `Registro` (
  `id_registro` int(11) NOT NULL auto_increment,
  `id_materia` int(10) unsigned NOT NULL default '0',
  `data` date NOT NULL default '0000-00-00',
  `argomenti` text NOT NULL,
  `osservazioni` text NOT NULL,
  `valutazione` decimal(4,2),
  `ruolo` smallint(6) NOT NULL,
  `id_degenza` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_registro`),
  UNIQUE KEY `id_registro` (`id_registro`)
);
DROP TABLE IF EXISTS `Reparti`;
CREATE TABLE `Reparti` (
  `id_reparto` int(10) unsigned NOT NULL auto_increment,
  `nome` tinytext NOT NULL,
  `descrizione` tinytext NOT NULL,
  UNIQUE KEY `id_reparto` (`id_reparto`)
);
DROP TABLE IF EXISTS `Reparto`;
CREATE TABLE `Reparto` (
  `id_repdeg` int(11) NOT NULL auto_increment,
  `id_degenza` int(11) NOT NULL default '0',
  `id_reparto` int(11) NOT NULL default '0',
  `tipo_degenza` enum('DH','DO') NOT NULL default 'DH',
  `data_cambio` date NOT NULL default '0000-00-00',
  `attivo` enum('S','N') NOT NULL default 'S',
  PRIMARY KEY  (`id_repdeg`)
);
DROP TABLE IF EXISTS `Ruoli`;
CREATE TABLE `Ruoli` (
  `id_ruolo` int(10) unsigned NOT NULL default '0',
  `descrizione` varchar(30) NOT NULL default '',
  UNIQUE KEY `id_ruolo` (`id_ruolo`)
);
DROP TABLE IF EXISTS `Scuola`;
CREATE TABLE `Scuola` (
  `id_scuola` int(11) NOT NULL default '0',
  `id_studente` int(11) NOT NULL default '0',
  `tipo` enum('f','p') NOT NULL default 'f'
);
DROP TABLE IF EXISTS `Scuole`;
CREATE TABLE `Scuole` (
  `id_scuola` int(11) NOT NULL auto_increment,
  `codice` varchar(20) NOT NULL default '',
  `nome` varchar(30) NOT NULL default '',
  `telefono` varchar(30) default NULL,
  `indirizzo` varchar(30) default NULL,
  `fax` varchar(20) default NULL,
  `email` varchar(50) default NULL,
  `denominazione` varchar(50) default NULL,
  `sitoweb` varchar(50) default NULL,
  `cap` varchar(5) default NULL,
  `citta` varchar(30) default NULL,
  `provincia` char(2) default NULL,
  UNIQUE KEY `id` (`id_scuola`)
);
DROP TABLE IF EXISTS `Studenti`;
CREATE TABLE `Studenti` (
  `id_studente` int(11) NOT NULL auto_increment,
  `nome` varchar(30) NOT NULL default '',
  `cognome` varchar(30) NOT NULL default '',
  `sesso` set('M','F') NOT NULL default 'M',
  `straniero` set('0','1') NOT NULL default '0',
  `HC` set('0','1') NOT NULL default '0',
  `n_citta` varchar(30) NOT NULL default '',
  `n_provincia` char(2) NOT NULL default '',
  `n_stato` varchar(20) NOT NULL default '',
  `cittadinanza` varchar(30) NOT NULL default '',
  `r_via` varchar(20) NOT NULL default '',
  `r_numero` varchar(5) NOT NULL default '',
  `r_citta` varchar(30) NOT NULL default '',
  `r_provincia` char(2) NOT NULL default '',
  `r_cap` varchar(5) NOT NULL default '',
  `r_telefono` varchar(30) default NULL,
  `r_cellulare` varchar(30) default NULL,
  `CF` varchar(16) default NULL,
  `note` text,
  `n_data` date NOT NULL default '0000-00-00',
  `r_stato` varchar(20) default NULL,
  `email` varchar(50) default NULL,
  `classe` set('1','2','3','4','5') NOT NULL default '1',
  `ordine` set('M','P','I','S') NOT NULL default 'M',
  `esame` set('0','1') NOT NULL default '0',
  `RC` set('0','1') NOT NULL default '1',
  `ripetente` set('0','1') NOT NULL default '0',
  `lingua1` varchar(20) NOT NULL default 'Inglese',
  `lingua2` varchar(20) default NULL,
  `lingua3` varchar(20) default NULL,
  UNIQUE KEY `id_studente` (`id_studente`)
);
DROP TABLE IF EXISTS `Utenti`;
CREATE TABLE `Utenti` (
  `id_utente` int(10) unsigned NOT NULL auto_increment,
  `cognome` varchar(30) NOT NULL default '',
  `nome` varchar(30) NOT NULL default '',
  `telefono` varchar(30) default NULL,
  `cellulare` varchar(30) NOT NULL default '',
  `email` tinytext,
  `username` varchar(10) NOT NULL default '',
  `pwd` varchar(40) NOT NULL default '',
  `id_ruolo` smallint(6) NOT NULL default '0',
  `note` text,
  KEY `id` (`id_utente`)
);

DROP TABLE IF EXISTS `Messaggi`;
CREATE TABLE `Messaggi` (
  `id` int(11) NOT NULL auto_increment,
  `id_utente_dest` int(11) default NULL,
  `id_utente_mitt` int(11) default NULL,
  `corpo` text,
  `oggetto` text,
  `nuovo` int(11) default NULL,
  PRIMARY KEY  (`id`)
);
INSERT INTO `Ruoli` VALUES (1,'Amministratore'),(2,'Operatore'),(3,'Docente Ospedaliero'),(4,'Osservatore'),(5,'Docente non ospedaliero'),(6,'Docente domiciliare');
INSERT INTO `Reparti` VALUES (1,'Non ospedalizzato','Studente dimesso');
UPDATE `Reparti` SET `id_reparto` = '0' WHERE `id_reparto` =1; 

