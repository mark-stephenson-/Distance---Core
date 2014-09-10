# The Core

## Requirements
* PHP >= 5.4
* Composer (globally or a local copy in the project) - [Instructions] (http://getcomposer.org/doc/00-intro.md#installation-nix)
* Homebrew & Grunt (globally or a local copy in the project) - [Instructions] (http://thechangelog.com/install-node-js-with-homebrew-on-os-x/)
* MCrypt PHP Extension - [Instructions] (run 'brew install mcrypt' in terminal and update /etc/php.ini libs to reference mcrypt.so')

## Setup Guide

1. Make sure you have setup your 'Document root' to point at the 'public/' directory.
2. Assuming you installed composer globally, run 'composer install -o' to install dependancies
3. Set recursive write permissions on the 'app/storage' and 'resources'
4. Create a MySQL database and fill in the details in 'app/config/database.php' under the mysql key
5. Compile .less files to .css by running 'grunt dev' via the terminal in the directory of 'GruntFile.js'
6. Run the remaining migrations 'php artisan migrate --env=local'