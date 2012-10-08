#!/bin/bash
# Registro Install script version 0.1
# Copyright 2006 Puria nafisi - puria@hipatia.info || puria@softwarelibero.it

# Errori - Eventi
E_NOTROOT=67        # Errore se non sei root
E_BADARGS=65        # Errore se metti argomenti sbagliati
USER_INTERRUPT=13   # Interrupt se si interrompe lo script per es. Ctrl-C

# Costanti
ROOT_UID=0          # $UID 0 cioe' solo i superutenti
ARGS_EXPECTED=0     # Numero di argomenti che ci aspettiamo
CONFIG_FILE=/etc/registro.conf # Il file di configurazione con tutti i dati
DIR=/var/Scuole

# colori
red='\e[1;31m'
green='\e[1;32m'
yellow='\e[1;33m'
cyan='\e[1;36m'
NC='\e[0m'

# variabili
passwd2=unset

if [ "$UID" -ne "$ROOT_UID" ]
then
  echo "Bisogna essere superutenti per eseguire questo script."
  exit $E_NOTROOT
fi

if  [ "$1" = "-h" -o "$1" = "--help" ]
then
cat INSTALL
exit $E_BADARGS
elif [ $# -gt  $ARGS_EXPECTED ]
then
  echo "Error ----------------------------------------------------------------------"
  echo "##### Usage: `basename $0` nessun argomento richiesto, nessuna opzione valida."
  echo "##### Prova a leggere il manuale con `basename $0` -h o --help"
  echo "Error ----------------------------------------------------------------------"
  exit $E_BADARGS
fi

# quando l'utente termina il processo con un interrupt si esce
# dallo script ma prima rimuove il file temporaneo e mette di nuovo
# lo screen echo che magari ha gia tolto per prendere la password

trap 'rm $CONFIG_FILE;stty echo;exit $USER_INTERRUPT' TERM INT

echo -n "Inserire il nome utente mysql: "
read user

while [[ "$passwd" != "$passwd2" ]]
do
  echo -n "Inserire la password di $user: "
  stty -echo # Turns off screen echo per oscurare la password
  read passwd
  echo -ne "\nReinserire la password: "
  read passwd2
  if [[ "$passwd" != "$passwd2" ]]
    then echo -e "\n\n${red}La password non coincide!${NC}"
  fi
  stty echo # Restores screen echo.
done

exec 6>&1
exec > $CONFIG_FILE
cat <<TESTO
<?php
 // le credenizali per connettersi al db che sono nel formato
 // mysql://utente:password@indirizzo_server_sql/Nome_DB
 \$p="$passwd";
 \$u="$user";
?>
TESTO
exec 1>&6 6>&-

if [ -e $CONFIG_FILE ]
then
  echo -e "\n\n$CONFIG_FILE     ${green}CREATO${NC}"
fi

if [ -d $DIR ]
then
  chmod 2777 $DIR
  echo -e "\n$DIR            ${yellow}ESISTENTE${NC} \n\ncontinuo l'installazione"
else
  mkdir -m 2777 $DIR
  echo -e "\n$DIR ${green}CREATO${NC}"
fi

echo -e "\n${green}Installazione andata a buon fine!${NC}"
echo -e "\nAdesso puoi Aprire un Browser e puntare alla \nseguente pagina ${cyan}https://localhost/${PWD##*/}/Installer.php${NC}\n"
exit 0
