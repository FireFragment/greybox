# greybox 2.0 - Lumen API
This branch of greybox 2.0 serves as the backend server application providing REST API for the Vue.js frontend application.

## API reference
All available WS are described in the [Apiary reference](http://greybox.docs.apiary.io/).

## Integrations
### Invoicing
This application uses [Fakturoid](https://www.fakturoid.cz/) and it's [API](https://fakturoid.docs.apiary.io/) for generating invoices.

### Logging
Happy to use [Bugsnack](https://www.bugsnag.com/) for logging.
![bugsnack logo](https://images.typeform.com/images/QKuaAssrFCq7/image/default)

## Installation
This project requires [Composer](https://getcomposer.org/) to build the code. Once you have Composer installed on your computer, run `composer install` to install dependencies.

To start the application run:
- `php artisan migrate` to run migrate the DB
- `php -S localhost:8000 -t public` to start the server

## Deployment
Production and Staging deployment on [Heroku](http://heroku.com/) is recommended.

### Tests
To test the code run `.\vendor\bin\phpunit tests`.