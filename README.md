# Kollaborate webhook

Ubuntu server

```sh
sudo apt install -y nginx
sudo apt install -y php8.3-fpm php8.3-mbstring php8.3-curl
sudo snap install --classic certbot
```

```sh
# Recommended to do this under /var/www
git clone https://github.com/tsukomu/kollaborate-webhook.git webhook
```

You must put environment.php at the root of this repo. Copy defaults.php and name the copy environment.php. 

environment.php is your personal settings. You can remove everything but the webhook URLs and $link_list_components if you do not want to change the defaults.

# Nginx configuration

/etc/nginx/conf.d/webhook.conf, stolen from Grav lol

```conf
## Begin - Server Info
listen 80;
root /var/www/webhook/public;
server_name your.domain.com;
## End - Server Info

## Begin - Index
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
## End - Index

## Begin - Security
location ~* /(\.git|cache|bin|logs|backup|tests)/.*$ { return 403; }
location ~* /(system|vendor)/.*\.(txt|xml|md|html|htm|shtml|shtm|json|yaml|yml|php|php2|php3|php4|php5|phar|phtml|pl|py|cgi|twig|sh|bat)$ { return 403; }
location ~* /user/.*\.(txt|md|json|yaml|yml|php|php2|php3|php4|php5|phar|phtml|pl|py|cgi|twig|sh|bat)$ { return 403; }
location ~ /(LICENSE\.txt|composer\.lock|composer\.json|nginx\.conf|web\.config|htaccess\.txt|\.htaccess) { return 403; }
## End - Security

## Begin - PHP
location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php-fpm.sock;
    fastcgi_split_path_info ^(.+\.php)(/.+)$;
    fastcgi_index index.php;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/$fastcgi_script_name;
}
## End - PHP
```

Use certbot to enable https:

```sh
certbot --nginx -d your.domain.com
```

Certbot will change the Nginx configuration, this is fine.

# Common components issues

- Link buttons (style => 5) MUST contain a URL.
- Link buttons (style => 5) MUST NOT contain the "custom_id" field.
- Webhooks that send components must set the query parameter `?with_components=true`.
  - Currently this is handled in the event loop in webhook.php, it's not meant to be configurable
