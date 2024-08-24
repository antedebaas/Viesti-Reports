#!/bin/sh

echo "Generate .env.local file"
if [ ! -f "/var/www/html/.env.local" ]; then
    APP_SECRET=$(echo "$RANDOM" | md5sum | head -c 32)
    echo "APP_ENV=prod" >> /var/www/html/.env.local
    echo "APP_SECRET=$APP_SECRET" >> /var/www/html/.env.local
    echo "DATABASE_URL=\"$DATABASE_TYPE://$DATABASE_USER:$DATABASE_PASSWORD@$DATABASE_HOST:$DATABASE_PORT/$DATABASE_NAME\"" >> /var/www/html/.env.local
    echo "MAILER_DSN=smtp://$MAILER_USER:$MAILER_PASSWORD@$MAILER_HOST:$MAILER_SMTPPORT" >> /var/www/html/.env.local
    echo "MAILBOX_CONNECTION=\"$MAILER_HOST:$MAILER_IMAPPORT/imap/ssl\"" >> /var/www/html/.env.local
    echo "MAILBOX_USERNAME=\"$MAILER_USER\"" >> /var/www/html/.env.local
    echo "MAILBOX_PASSWORD=\"$MAILER_PASSWORD\"" >> /var/www/html/.env.local
    echo "DELETE_PROCESSED_MAILS=\"$DELETE_PROCESSED_MAILS\"" >> /var/www/html/.env.local
    echo "ENABLE_REGISTRATION=\"$ENABLE_REGISTRATION\"" >> /var/www/html/.env.local
fi

echo "Check if attachments directory exists"
if test -d /path/to/directory; then
  echo "Attachments directory exists."
else 
    echo "Attachments directory does not exist, creating it."
    mkdir -p /var/www/html/var/imap/attachments
    chown -R nobody:nobody /var/www/html/var/imap/attachments
    chmod 775 /var/www/html/var/imap/attachments
fi

echo "Set cron schedule"
if echo "$MAILCHECK_SCHEDULE" | egrep -s -q "^(@(monthly|weekly|daily|hourly|15min))|((((\d+,)+\d+|((\d+|\*)(\/|-)\d+)|\d+|\*) ?){5})$"; then
    echo 'Cron schedule match found, following input schedule';
else
    echo 'Cron schedule match not found, setting to run once every hour';
    MAILCHECK_SCHEDULE='0 * * * *';
fi

if [ "$MAILCHECK_SCHEDULE" == "@15min" ]; then
    ln -s /usr/local/bin/checkmail.sh /etc/periodic/15min/checkmail.sh
else if [ "$MAILCHECK_SCHEDULE" == "@hourly" ]; then
    ln -s /usr/local/bin/checkmail.sh /etc/periodic/hourly/checkmail.sh
else if [ "$MAILCHECK_SCHEDULE" == "@daily" ]; then
    ln -s /usr/local/bin/checkmail.sh /etc/periodic/daily/checkmail.sh
else if [ "$MAILCHECK_SCHEDULE" == "@weekly" ]; then
    ln -s /usr/local/bin/checkmail.sh /etc/periodic/weekly/checkmail.sh
else if [ "$MAILCHECK_SCHEDULE" == "@monthly" ]; then
    ln -s /usr/local/bin/checkmail.sh /etc/periodic/monthly/checkmail.sh
else
    grep "/usr/local/bin/checkmail.sh" /etc/crontabs/root || echo "$MAILCHECK_SCHEDULE /usr/local/bin/checkmail.sh" >> /etc/crontabs/root
fi  fi  fi  fi  fi

echo "Mainenance"
php /var/www/html/bin/console app:removemaillock --quiet
php /var/www/html/bin/console cache:clear --quiet

echo "Run migrations"
php /var/www/html/bin/console doctrine:migrations:migrate --no-interaction