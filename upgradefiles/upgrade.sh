#!/bin/bash
if [ -f .env.local ]; then
    if ! grep -qe "^DATABASE_TYPE=" .env.local; then
        echo "entry DATABASE_TYPE" does not exist in .env.local
        echo "adding DATABASE_TYPE=mysql"
        printf "\nDATABASE_TYPE=\"mysql\"" >> .env.local
    fi
fi

echo "done"