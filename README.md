# projet chouette sellerie

## instalation

### cloner le projet

```
https://github.com/Hypnololz/chouettesellerie.git
```

### changer les connection base de données dans .env

### deplacer le terminal dans le dossier cloné

```
cd chouettesellerie
```
### taper les commandes suivantes :

```
composer install
symfony console doctrine:database:create
symfony console make:migration
symfony console doctrine:migrations:migrate
```
