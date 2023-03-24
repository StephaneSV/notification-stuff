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
-

## Cas de 0 notification

Je n'ai pas codé de comportement spécial pour le cas où 0 notification sont trouvées.  
Pour éviter un couplage API avec un front spécifique,
je considère que ce n'est pas la responsabilité de l'API de choisir le comportement en front s'il y a 0 notification,
c'est plutôt au front (ou à l'app, ou n'importe quel récepteur des données API) de choisir quoi faire de ce résultat.

Ce n'est que mon point de vue, qui correspondait au projet où c'était applicable.   
A ce stade, j'ignore si cela est valable pour vos projets.

## Temps passé (approximatif)

- setup d'environnement docker : 1/2d
- conception base de données + scripts SQL = 1/2d
- Base du code (App, Model, Service) = 1/2d
- le reste = env 1.5d

