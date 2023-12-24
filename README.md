# Benford Law Compliance

Laravel command that evaluates compliance with Benford's Law in a given dataset.

If you want to learn about the [Benford's Law, click here!](https://en.wikipedia.org/wiki/Benford%27s_law)

## System Requirement:
* Local PHP 8 and Composer

## Installation Steps:

* Clone this repository
* Create .env file: `cp .env.example .env`
* Install dependencies: `composer install`
* Generate key: `php artisan key:generate`
* Run tests: `vendor/bin/phpunit`

## Run Checker Command
* Run command: `php artisan data-science:benford-law-checker <space-separated-integers>`
