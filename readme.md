# The Core

## Requirements
* PHP >= 5.4
* MCrypt PHP Extension
* Composer (globally or a local copy in the project) - [Install Instructions](http://getcomposer.org/doc/00-intro.md#installation-nix)

## Setup Guide

1. Make sure you have setup your vhost to point at the `public/` directory.
2. Assuming you installed composer globally, run `composer install -o` to install dependancies
3. Set recursive write permissions on the `app/storage` and `resources`
4. Create a MySQL database and fill in the details in `app/config/database.php` under the mysql key
5. Run the remaining migrations `php artisan migrate --env=local`