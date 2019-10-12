# ToDoList

Handle your daily tasks with ease thanks to this website.

## Installation

1. Clone the project with `git clone urlOfTheGitHubRepositoryHere`
1. Run `composer install`
1. Create a database and create a `.env.local` file with your database credentials like that:
    ```
    ###> symfony/framework-bundle ###
    APP_ENV=prod
    APP_SECRET=yourOwnSecretCode
    ###< symfony/framework-bundle ###

    ###> doctrine/doctrine-bundle ###
    DATABASE_URL=mysql://yourDatabaseUserNameHere:yourDatabaseUserPasswordHere@127.0.0.1:3306/yourDatabaseNameHere
    ###< doctrine/doctrine-bundle ###
    ```
1. Run `php bin/console doctrine:migrations:migrate -n` to create the right tables (note the `-n` or `--no-interaction` which avoid command prompts, thus allowing you to use this command inside an automatic script)

## Test suite

The application comes with a serie of unit and functional tests written with PhpUnit.

To execute these tests, first create a file called `.env.test.local` at the root of the project and put your test database credentials in it as you did in the `.env.local` file, then execute `php bin/console phpunit`.

If you want to generate a coverage report, add `--coverage-html nameOfTheDestinationFolderHere` to the previous command.

## Documentation

Some documentation is located inside the `/docs` folder.

Also a static website dedicated to the documentation [is available here](https://nicordev.github.io/formation-oc-php-projet8-todoco/).

## How to contribute

Everything is explained in the `contribute.md` file located in the `/docs` folder.

## UML diagrams

UML diagrams are located in the `uml_diagrams` folder in the project's root.

*Enjoy!*
