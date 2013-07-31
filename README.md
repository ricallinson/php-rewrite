# Php-rewrite

[![Build Status](https://secure.travis-ci.org/ricallinson/php-rewrite.png?branch=master)](http://travis-ci.org/ricallinson/php-rewrite)

Apache rewrite rule generator packaged as a [php-require](https://github.com/ricallinson/php-require) module.

## Example

This module reads comments in PHP files;

    <?php
    // @route GET /index.html
    echo "Hello, world!";

And generates a .htaccess file from them;

    RewriteEngine On
    RewriteBase /

    RewriteCond %{REQUEST_METHOD} GET
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^index.html$ /foo/bar/file.php [L,QSA]
