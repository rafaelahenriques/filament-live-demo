#!/usr/bin/env bash
# Starts the demo so phones on the same Wi-Fi can open it, and prints the link to use.
cd "$(dirname "$0")"

URL="http://$(ipconfig getifaddr en0 || ipconfig getifaddr en1):8000/admin"
echo ""
echo "  Open this link (the QR code points here): $URL"
echo ""
(sleep 2 && open "$URL") &

PHP_CLI_SERVER_WORKERS=8 php artisan serve --host=0.0.0.0 --port=8000 --no-reload
