#!/bin/bash
if [ -f .env.local ]; then
    if ! grep -qe "^DATABASE_TYPE=" .env.local; then
        echo "entry DATABASE_TYPE" does not exist in .env.local
        echo "adding DATABASE_TYPE=mysql"
        printf "\nDATABASE_TYPE=\"mysql\"" >> .env.local
    fi

    if ! grep -qe "^ENABLE_REGISTRATION=" .env.local; then
        echo "entry ENABLE_REGISTRATION" does not exist in .env.local
        echo "adding ENABLE_REGISTRATION=true"
        printf "\nENABLE_REGISTRATION=\"true\"" >> .env.local
    fi

    if ! grep -qe "^MAILBOX_PATH=" .env.local; then
        echo "entry MAILBOX_PATH" does not exist in .env.local
        echo "adding MAILBOX_PATH=INBOX"
        printf "\MAILBOX_PATH=\"INBOX\"" >> .env.local
    fi

    if ! grep -qe "^MAILBOX2_ENABLED=" .env.local; then
        echo "entry MAILBOX2_ENABLED" does not exist in .env.local
        echo "adding MAILBOX2 variables"
        printf "\MAILBOX2_ENABLED=\"false\"" >> .env.local
        printf "\MAILBOX2_CONNECTION=" >> .env.local
        printf "\MAILBOX2_USERNAME=" >> .env.local
        printf "\MAILBOX2_PASSWORD=" >> .env.local
        printf "\MAILBOX2_PATH=\"INBOX\"" >> .env.local
    fi
fi

echo "done"
echo ""