#!/bin/bash

# 1. Iniciar Laravel Reverb en segundo plano en el puerto 6001
nohup php /home/site/wwwroot/artisan reverb:start --port=6001 --daemon > /home/LogFiles/reverb.log 2>&1 &

# 2. Crear una configuración de Nginx universal y optimizada para Laravel + Inertia + Reverb
cat << 'EOF' > /etc/nginx/sites-available/default
server {
    listen 8080 default_server;
    listen [::]:8080 default_server;
    server_name _;
    root /home/site/wwwroot/public;
    index index.php index.html;

    client_max_body_size 100M;
    fastcgi_buffer_size 128k;
    fastcgi_buffers 4 256k;
    fastcgi_busy_buffers_size 256k;

    # ENRUTAMIENTO WEBSOCKET (Reverb)
    # Cualquier petición que empiece con /app/ se redirige al puerto 6001
    location /app {
        proxy_pass http://127.0.0.1:6001;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
    }

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

# 3. Reiniciar Nginx para aplicar los cambios
service nginx restart

# 4. Iniciar PHP-FPM
php-fpm