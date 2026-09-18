#!/bin/bash

# 1. Iniciar Laravel Reverb en segundo plano (SIN --daemon)
nohup php /home/site/wwwroot/artisan reverb:start --port=6001 > /home/LogFiles/reverb.log 2>&1 &

# 2. Crear configuración de Nginx universal y optimizada para Laravel + Inertia + Reverb
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

# 3. Reiniciar Nginx
service nginx restart

# 4. Iniciar PHP-FPM
php-fpm