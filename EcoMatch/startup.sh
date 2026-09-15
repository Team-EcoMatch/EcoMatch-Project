#!/bin/bash

# 1. Crear una configuración de Nginx universal y optimizada para Laravel + Inertia
cat << 'EOF' > /etc/nginx/sites-available/default
server {
    listen 8080 default_server;
    listen [::]:8080 default_server;
    server_name _;
    root /home/site/wwwroot/public;
    index index.php index.html;

    # Buffers grandes (Evita el error 502 con Inertia.js y Sesiones)
    client_max_body_size 100M;
    fastcgi_buffer_size 128k;
    fastcgi_buffers 4 256k;
    fastcgi_busy_buffers_size 256k;

    # Manejo de rutas universal (Cualquier URL va a index.php de Laravel)
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Pasar archivos PHP a FastCGI
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Expiración de archivos estáticos (CSS, JS, imágenes)
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires max;
        log_not_found off;
    }
}
EOF

# 2. Reiniciar Nginx para aplicar los cambios
service nginx restart

# 3. Iniciar PHP-FPM
php-fpm