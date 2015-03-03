# membership-application-webapp

The simple webapp for applying to Hacklab.TO as a member

## Requirements ##

1. PHP 5.4 or greater
2. [Composer](https://getcomposer.org) for installing dependencies
3. An SQL database of somesort (MySQL, SQLite)

## Installation ##

1. `git clone` this repo
2. Run `composer install` in the project root directory.
3. Something something migrations, somehow.

## Running ##

```
php -S localhost:8080 -t web web/index_dev.php
```

Or, if you don't want debugging:

```
php -S localhost:8080 -t web web/index.php
```

Visit [http://localhost:8080/](http://localhost:8080/)

## Project Layout ##

- `src/`: Application source code
- `views/`: Twig templates
- `web/`: Webroot
- `config/`: environment-specific config and secrets

## License ##

See [LICENSE](LICENSE).
