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
        printf "\ENABLE_REGISTRATION=\"true\"" >> .env.local
    fi
fi

echo "done"
echo ""