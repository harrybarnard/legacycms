# Legacy PHP Content Management System

This CMS was used to power several websites that were developed whilst I was technical 
lead at a small digital agency. I'm publishing it mainly for posterity and to serve as
an example of my past work. I don't recommend it be used in production for future projects.

## Installation

It's designed around a standard Linux/Apache/MySQL/PHP stack.

### Apache

Apache will need the rewrite module enabled.

Apache virtual host configuration:

    <VirtualHost *:80>
        ServerName legacycms.local
        DocumentRoot /path/to/legacycmsroot/public
     
        SetEnv APPLICATION_ENV "development"
     
        <Directory /path/to/legacycmsroot/public>
            DirectoryIndex index.php
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
    </VirtualHost>
    
### PHP

PHP will need the MySQL PDO and GD extensions enabled.

Run composer install to install dependencies.

Rename application/config/config.example.ini to application/config/config.ini and change values
accordingly.

### Permissions    

Create cache, assets, search/site-index directories and give write permissions to server.
