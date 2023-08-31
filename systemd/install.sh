#!/bin/bash
SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
echo "Installing systemd service..."
cat $SCRIPT_DIR/dmarcmailcheck.service | sed -e 's@{PATH}@'$SCRIPT_DIR'@g' > /usr/lib/systemd/system/dmarcmailcheck.service
cp $SCRIPT_DIR/dmarcmailcheck.timer /usr/lib/systemd/system/dmarcmailcheck.timer
echo "Reloading systemd daemon..."
systemctl daemon-reload
echo "enable dmarcmailcheck service..."
systemctl enable dmarcmailcheck.timer