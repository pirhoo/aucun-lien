# Aucun Lien : WordPress Theme

## Installation

Move the project's folder to your *wp-content/theme*.

Use the following **.htaccess** to allow custom URL:
```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^index\.php$ - [L]
  RewriteRule ^tweet/(\d*)(\.?(\w{2,4}))?/?$ /?post_type=tweet&p=$1&format=$3 [L,NC,QSA]
  RewriteRule ^tweet/(\d*)-(\d*)x(\d*)(\.?(\w{2,4}))?/?$ /?post_type=tweet&p=$1&format=$5&w=$2&h=$3 [L,NC,QSA]
</IfModule>

# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
```

## Customization

This theme use the powerfull Compass framework to edit stylesheets. To customize the CSS, you need to change the files under */inc/sass*.

To compile SASS files, install Compass with the following command from the project folder:
```
make install
```

Then, when you are editing the theme, run this command to generate CSS files autocratically :
```
make watch
```

## GNU General Public License
This software is the property of [Pierre Romera](http://pirhoo.com) and licensed under the [GNU Genral Public License](https://www.gnu.org/licenses/gpl-3.0.txt).