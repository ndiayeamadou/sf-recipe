## RecipeApp

### Command lines
```bash
php bin/console doctrine:database:create OR
php bin/console d:d:c
```
```bash
php bin/console make:entity
```
```bash
php bin/console make:migration
```
* migrate all migrations into DB
```bash
php bin/console doctrine:migrations:migrate
```

* in the development env :
* Using fixtures to load fake data & also fakephp
composer require --dev orm-fixtures
composer require fakerphp/faker --dev

* load these fake data in DB
```bash
php bin/console doctrine:fixtures:load
```
* make form
```bash
php bin/console make:form
```