#!/bin/bash
# 1. Sobreescribir la configuración de Nginx con una perfecta para Laravel
cat << 'EOF' > /etc/nginx/sites-available/default
server {
    listen 8080;
    server_name _;
    root /home/site/wwwroot/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires max;
        log_not_found off;
    }
}
EOF

# 2. Reiniciar Nginx para aplicar la nueva configuración
service nginx restart

# 3. Iniciar PHP-FPM
php-fpm