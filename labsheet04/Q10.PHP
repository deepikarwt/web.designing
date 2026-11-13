<VirtualHost *:80>

    ServerName collegeweb.local

    DocumentRoot "C:/xampp/htdocs/PHP.LAB/labsheet04"

    <Directory "C:/xampp/htdocs/PHP.LAB/labsheet04">

        AllowOverride All
        Require all granted

    </Directory>

</VirtualHost>