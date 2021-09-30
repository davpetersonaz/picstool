This project is a picture manager, it allows the user to upload pictures,
write captions for them, indicate which ones are to be displayed on the front page,
and to indicate which ones to feature.

for brand new implementations, these files must be created (not included in git repository):
./public_html/php.ini -- must have: extension=imagick.so
./public_html/.htaccess -- must SetEnv to php.ini file
./site_config.ini -- must have these vars: site_number, db_host, db_name, db_user, db_pass

NOTE: this is a work-in-progress, most of the administrative backend is complete,
including a config page and ability to upload pictures.

author: davpeterson@zoho.com
