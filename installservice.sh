#!/bin/bash
SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
echo "Installing systemd service..."
cat $SCRIPT_DIR/systemd/dmarcmailcheck.service | sed -e 's@{PATH}@'$SCRIPT_DIR'@g' > /usr/lib/systemd/system/dmarcmailcheck.service
cp $SCRIPT_DIR/systemd/dmarcmailcheck.timer /usr/lib/systemd/system/dmarcmailcheck.timer
echo "Reloading systemd daemon..."
systemctl daemon-reload
echo "start dmarcmailcheck timer..."
systemctl start dmarcmailcheck.timer