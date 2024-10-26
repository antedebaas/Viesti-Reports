#!/bin/bash
if [ -f .env.local ]; then

    if ! grep -qe "^ENABLE_REGISTRATION=" .env.local; then
        echo "entry ENABLE_REGISTRATION" does not exist in .env.local
        echo "adding ENABLE_REGISTRATION=true"
        printf "\nENABLE_REGISTRATION=\"true\"" >> .env.local
    fi

    if ! grep -qe "^MAILBOX_PATH=" .env.local; then
        echo "entry MAILBOX_PATH" does not exist in .env.local
        echo "adding MAILBOX_PATH=INBOX"
        printf "\nMAILBOX_PATH=\"INBOX\"" >> .env.local
    fi

    if ! grep -qe "^MAILBOX2_ENABLED=" .env.local; then
        echo "entry MAILBOX2_ENABLED" does not exist in .env.local
        echo "adding MAILBOX2 variables"
        printf "\nMAILBOX2_ENABLED=\"false\"" >> .env.local
        printf "\nMAILBOX2_CONNECTION=" >> .env.local
        printf "\nMAILBOX2_USERNAME=" >> .env.local
        printf "\nMAILBOX2_PASSWORD=" >> .env.local
        printf "\nMAILBOX2_PATH=\"INBOX\"" >> .env.local
    fi
fi

SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
php $SCRIPT_DIR/../bin/console app:migrateenvvars
echo "done"
echo ""