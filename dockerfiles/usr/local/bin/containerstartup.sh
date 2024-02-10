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
fi

echo "Set up checkmail schedule"
[ -z "$CRON_CHECK_MAILS" ] && CRON_CHECK_MAILS="@daily"
echo "$CRON_CHECK_MAILS /bin/sh /usr/local/bin/checkmail.sh" >> /etc/crontabs/root

echo "Run migrations"
php /var/www/html/bin/console doctrine:migrations:migrate --no-interaction