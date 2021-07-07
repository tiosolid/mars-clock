
# Mars Clock (MSD / MTC) - Working Example API

## Description
Final working code for a Mars Clock. This serves a single endpoint API that can be used to calculate the Mars Sol Date (MSD) and the Martian Coordinated Time (MTC).

Project was developed with [Laravel Lumen](https://lumen.laravel.com). Tests were created with [PHPUnit](https://phpunit.de).

Code is structured in the following files:
* Controller that handles all the API requests (`app/Http/Controllers/ClockController.php`);
* Model that holds the main logic together (`app/Models/MarsTime.php`);

## Requirements
* PHP 7.4 or newer from [php.net](https://www.php.net/downloads) (_project was fully developed and tested with PHP 7.4.3_);
* SimpleXML (`php-xml`) PHP Extension (usually included by default);
* OpenSSL, PDO and Mbstring PHP Extensions (framework requirements but not really used here);
* [Composer](https://getcomposer.org/download/);
* PHPUnit 9 (included with the project via composer);
* Optionally, [XDebug](https://xdebug.org/) with `xdebug.mode = coverage` set in your `php.ini` file for test coverage reports;

## Setup Instructions
* Unpack the repository into a folder of your choice;
* Open a terminal and navigate to the root project folder;
* Run `composer install` and wait until the application is fully installed;
* Create a `.env` file to set up the application (you can just duplicate the existing `.env.example` file included and rename it to `.env`);
* Optionally, edit the `.env` file and disable the debug mode by setting `APP_DEBUG` to `false`;
* Serve the project using the built-in PHP webserver with the command: `php -S localhost:80 -t public`;

> In case of permission errors, try running the command above with `sudo` or using a different port (like `php -S localhost:8080 -t public`)

## Using the API
After starting the PHP webserver, the API can be reached by going to the main endpoint at http://127.0.0.1/clock. 

Only `POST` requests are currently accepted. The only available parameter is `utcDateTime`, which accepts a UTC Date/Time string compatible with the `strtotime` [PHP method](https://www.php.net/manual/en/function.strtotime.php), for example, `Wed, 23 Jun 2021 20:22:31 GMT`. 

You can perform a test request by executing the following command in a terminal while the PHP webserver is running:

`curl --location --request POST 'http://127.0.0.1/clock?utcDateTime=Wed,%2023%20Jun%202021%2020:22:31%20GMT'`

or if you are running the webserver in a different port:

`curl --location --request POST 'http://127.0.0.1:8080/clock?utcDateTime=Wed,%2023%20Jun%202021%2020:22:31%20GMT'`

For more complex tests, I recommend using [Postman](https://www.postman.com).

## Tests
To run the tests, navigate to the root project folder and issue the command: `./vendor/phpunit/phpunit/phpunit -v --coverage-text`

All included tests should pass without any errors.

> The test files can be found in the folder `tests` and follow the same organizational structure as the main source files in the `app` folder.

## Remarks
This was done as a problem solving test some time ago. Uploading to Github now as some code example of my own.