<?
session_start();
@include "autenticazione.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title>Registro Elettronico - <?php if(isset($title)) echo $title; ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <style type="text/css">
  p {
    margin-top:0;
    margin-bottom:0;
  }
//   @import url(registro.css);
  </style>
  <link rel="shortcut icon" href="favicon.ico" />
 </head>
 <body>

<?php if($RUOLO==3 || $RUOLO==4 || $RUOLO==5) echo "<style type='text/css'>.container4{display:none;}</style>"; ?>
<div id="main">
