Installation instructions
=========================

1. create a mysql database, eg. cmfive

2. load the db.sql file into the database

3. create a vhost entry for apache, eg.

```
<VirtualHost *:80>
    DocumentRoot "C:\Users\admin\git\cmfive"
    ServerName cmfive.local
    ErrorLog "logs/cmfive.localhost-error.log"
    CustomLog "logs/cmfive.localhost-access.log" combined
    <Directory "C:\Users\admin\git\cmfive">
      Options FollowSymLinks
      AllowOverride All
      Order allow,deny
      Allow from all
    </Directory>
</VirtualHost>
```

4. if on windows creat a host entry in C:/Windows/System32/drivers/etc/hosts

```
127.0.0.1    cmfive.local
```

5. edit the index.php file to change database parameters

6. edit the config.php file to change module parameters

7. default admin password is admin