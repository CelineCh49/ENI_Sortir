# ENI_Sortir
Projet réalisé en équipe avec PHP/Symfony lors de la formation Développeur Web et Web mobile au sein de l'ENI

La société ENI souhaite développer pour ses stagiaires actifs, ainsi que ses anciens stagiares, une plateforme web leur permettant d'organiser des sorties.
La plateforme est une plateforme privée dont l'inscription sera gérée par le ou les administrateurs.
Les sorties, ainsi que les participants sont rattachés à un campus pour permettre une organisation géographique des sorties.


## Technologies utilisées

- PHP: 8.2.9
- Symfony: 6.3
- Twig
- Tailwindcss
- Mysql


## Initialisation du projet

1. **Cloner le dépôt**

   ```bash
   git clone https://github.com/CelineCh49/ENI_Sortir.git
   ```

2. **Installer les dépendances**

   Avec Composer :

   ```bash
   composer install
   npm install
   ```

3. **Configurer la base de données**

   Assurez-vous de mettre à jour le fichier `.env` avec vos informations de connexion à la base de données.

   ```bash
   symfony console doctrine:database:create
   symfony console make:migration
   symfony console doctrine:migrations:migrate
   ```

4. **Lancer le serveur de développement**

   ```bash
   npm run build
   npm run dev
   npm run watch
   symfony serve -d
   ```

   ou si vous utilisez le serveur web PHP intégré :

   ```bash
   php -S localhost:8000 -t public/
   ```
5. **Lancer les fixtures**

   ```bash
   symfony console doctrine:fixtures:load
   ```

## Contribution

Les contributions sont les bienvenues ! Veuillez créer une issue ou soumettre une pull request pour toute contribution.

## Licence

Ce projet est sous licence 


## Auteur
CHAIGNE Céline
DEBAILLEUL Cédric
PRZYBYL Yohan

