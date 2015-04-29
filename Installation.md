Set up server with apache, php, mysql, php-mysql

Change to web directory

`cd /var/www/html`

Check out site from SVN:

http://code.google.com/p/smoothoperator/source/checkout

Log in to web site

Username: **admin**

Password: **adminpass**

First thing you _**must**_ do is change the password.

You can do this by going to the "users" tab and clicking on the "Change Password" link.

Modules are available under modules tab.

Menus can be rearranged under Menus tab.

MySQL databases are created automatically if php has access to MySQL as root/no password.

If not, you can edit config/db\_config.php