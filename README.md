# About

Send Kollaborate.tv file comments & actions through Discord messages.

**NOTICE:**

**Kollab webhooks are in beta; there is no documentation for them.** How it works changes every year and because there's no documentation, you must guess & check on a live project.

**The webhook watches all projects across your entire account.** If you are only interested in a specific project, you must filter for that yourself (it is not done in the code provided here).

**Linked accounts cannot make their own webhooks.**     
Even though all kollaborate accounts have the menus to add a webhook, doing anything in that menu does nothing if a webhook already exists.     
Only the owner of the "host" Kollaborate account for the entire organization can create and manage the webhook.

I have not tested if someone in the organization creates a webhook before the owner does.

# Usage

**Pre-requisites:**    
- A web host where you can host PHP files
- A discord server
- 1 - 2 [Discord webhook](https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks) URLs. 
- A paid kollaborate.tv account


1. Download the code through the green code button or the releases.
2. Unzip the files to a folder such as `kollab webhook`.
3. Copy `defaults.php` and name the copy `environment.php`.
4. Open `environment.php` in a text or code editor.

## Public vs private webhook

The public webhook is the one that posts kollab comments to discord.     
**The private webhook is optional** and shows logs of who viewed the files, who commented, etc. It is spammy and should be sent somewhere private (like a different server) and muted until something untoward happens, at which point you can then search for the log of it.

5. Put the discord webhook URL for your public webhook inside the quotes in the `$public_webhook` line. 
6. If you want a private webhook, put that discord webhook URL in the `$logging_webhook` line. Otherwise, don't change it.
7. Paste the secret key from Kollab (Account -> integrations) inside the quotes on the `$secret_key` line.
8. Scroll down to `$link_list_components`. This makes buttons at the end of your comment message. Here are the things you should change:
   - `label` - the text on the button. 
   - `emoji` - the emoji on the left side of the text on the button. Remove this line entirely if you don't want any emoji there.
   - `url` - link the button goes to.

The recommended link setup is: `[share link to your video folder] [video information spreadsheet] [some other informative link]`    
In my event, the 3 links are `[Event videos] [Vid info sheet] [CW & VPR doc]`

Don't change anything else unless you know what you're doing.

## Upload the files to your web host.

Make a new folder on your domain (e.g. `kollab`) and upload these into it:    
- `environment.php`
- `defaults.php`
- `public` folder
- `src` folder

In Kollab, your "Endpoint URL" is `https://yourdomain.com/yourfolder/public/webhook.php`

9. After uploading the files to your web host, enable the webhook in Kollab by going to your account -> Integrations, using the Endpoint URL, and click "Verify Web Hook".

**Your script responded appropriately...etc.**    
You're done. Navigate away on Kollab.     
Now view, upload, comment, etc., on a kollab file to make sure it is actually posting to discord how you want.

**There was an error.**    
Make sure your secret key is correct between Kollab and your PHP files.

If it still errors, make sure the endpoint URL is actually pointing the correct thing. 

If it still errors, make sure your script is verifying Kollab's signature. If it currently is, stop. For some reason, Kollab does not actually attach its signature with every message.     
I've tried talking to Kollab support about this, but they can't seem to replicate the issue.

You can turn off signature checking in `environment.php` by changing `$kollaborate_has_fixed_their_sig = true;` to `$kollaborate_has_fixed_their_sig = false;`

If it is still erroring, Kollab probably changed something and you will need to update the PHP files to accommodate. Hope you started early! :')

## Troubleshooting webhook issues

### Some comments are not being sent

Kollab sometimes does not send its header signature. Disable signature checking.    
If it's currently disabled, enable it.

### The link to the video sometimes goes to the folder instead

The link used in the discord message is the link the person who left the comment actually used.    
If they used the folder link to get to the video, it is the folder link. If they used a direct share link to the video, that is what is used.

If you need to guarantee a specific link, distribute your links more carefully or don't link back to the video in the discord message.

### Markers/colors/annotations/replies aren't working properly

Kollaborate changed something and you will have to edit the PHP files.

### The logging webhook is missing some information on some messages

The information given is different between logged in users and guests. Some information will be missed or different when the action performed is from a logged in user.    
For this event, we are assuming you only have one user and that user is the event coordinator.     
A user is someone with a kollaborate account that is logged into the project. Everyone else is a guest.

### It is posting comments from other events/projects

The kollaborate webhook captures all comments across all files connected to your kollaborate account. If you need to separate based on project, you will have to edit your PHP file(s) to filter for that specific project only.     
Alternatively, only host one event at a time.


# Build Info (for developers)

## Kollaborate webhook

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

## Nginx configuration

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

## Common components issues

- Link buttons (style => 5) MUST contain a URL.
- Link buttons (style => 5) MUST NOT contain the "custom_id" field.
- Webhooks that send components must set the query parameter `?with_components=true`.
  - Currently this is handled in the event loop in webhook.php, it's not meant to be configurable
