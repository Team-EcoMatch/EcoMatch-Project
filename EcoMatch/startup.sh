#!/bin/bash
# Cambiar el document root a /public
sed -i "s|root /home/site/wwwroot|root /home/site/wwwroot/public|g" /etc/nginx/sites-available/default

# Asegurar que Laravel maneje las rutas (try_files)
sed -i '/location \/ {/c\location / {\n    try_files $uri $uri/ /index.php?$query_string;\n}' /etc/nginx/sites-available/default

# Recargar Nginx y iniciar PHP
service nginx reload
php-fpm