# Exercice technique

## Install

Ouvrir un terminal à la racine du projet et taper :
`./dockerdo fresh_install`  
Cette commande va créer les conteneurs, créer la base de données `webapp` (structure et données), et démarrer les
conteneurs.

Cette commande permet aussi de reset entièrement l'environnement si besoin (conteneurs et DB).

Si la base de données ne contient aucune donnée, une erreur a peut-être eu lieu de la copie ou exécution des scripts
SQL.
Dans ce cas, exécuter manuellement les deux fichiers .sql présents dans `install/`.

## Tester le code

Ouvrir [http://localhost:8080](http://localhost:8080)

Note : par souci de simplicité de test, l'endpoint de patch de notification est ici sollicité en GET au lieu de PATCH.  
Je souhaitais terminer cet exercice le plus rapidement possible, alors j'ai pris ce raccourci.

## Documents

Il y a dans le répertoire /docs 2 fichiers :

- MPD.png : schéma de la base de données, fait avec MySQLWorkbench
- notif.mwb : fichier pour MySQL Workbench, si vous voulez voir comment j'ai déclaré les FK

## Temps passé (approximatif)

- setup d'environnement docker : 3-4h
- conception base de données + scripts SQL = 3h
- Base du code (App, Model, Service) = 3h
- page getNotificationCounts = 1h + ?
- page getNotifications = 