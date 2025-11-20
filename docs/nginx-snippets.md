# Nginx hardening snippets

Use these snippets to front the application with HTTPS, caching, and PHP-FPM. Replace `/var/www/ukbn2/public` with your deploy path and `example.com` with your hostname.

```nginx
server {
    listen 443 ssl http2;
    server_name example.com;

    root /var/www/ukbn2/public;
    index index.php;

    ssl_certificate /etc/letsencrypt/live/example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/example.com/privkey.pem;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;

    # Security headers
    add_header X-Content-Type-Options nosniff;
    add_header X-Frame-Options DENY;
    add_header Referrer-Policy strict-origin-when-cross-origin;
    add_header Content-Security-Policy "default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self'; img-src 'self' data:; font-src 'self' data:; frame-ancestors 'none'; base-uri 'self'; form-action 'self';";

    # Compress assets
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;
    gzip_min_length 1024;

    # Cache static assets aggressively
    location ~* \.(?:js|css|png|jpg|jpeg|gif|svg|ico|woff2?)$ {
        expires 30d;
        access_log off;
        add_header Cache-Control "public, max-age=2592000, immutable";
        try_files $uri =404;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock; # adjust socket version as needed
        fastcgi_read_timeout 60s;
    }

    # Limit request rates for auth endpoints
    location ~ ^/(login|register|password/reset) {
        limit_req zone=auth_zone burst=10 nodelay;
    }
}

# Define shared rate limit zone (1MB shared memory bucket)
limit_req_zone $binary_remote_addr zone=auth_zone:10m rate=10r/m;

server {
    listen 80;
    server_name example.com;
    return 301 https://$host$request_uri;
}
```
