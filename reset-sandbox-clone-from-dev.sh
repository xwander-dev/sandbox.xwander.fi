#!/bin/bash
# reset-sandbox-clone-from-dev.sh
# Complete sandbox environment reset from dev.xwander.fi
#
# Documentation: /srv/xwander-platform/sandbox.xwander.fi/project-docs/reset-script-documentation.md
# This script resets the entire sandbox to match current dev environment.
# See documentation for detailed explanation of each step.

set -euo pipefail

echo "🔄 Starting complete sandbox reset from dev environment..."
echo "⚠️  This will destroy all sandbox data! Press Ctrl+C to cancel..."
sleep 5

# Save this script
SCRIPT_PATH="$0"
cp "$SCRIPT_PATH" /tmp/reset-sandbox-temp.sh

echo "1️⃣ Resetting Git repository..."
git reset --hard
git clean -fdx

echo "2️⃣ Syncing code from dev environment..."
rsync -av --delete \
    --exclude='.git' \
    --exclude='reset-sandbox-clone-from-dev.sh' \
    --exclude='web/app/uploads' \
    /srv/xwander-platform/xwander.fi/dev/ ./

# Restore reset script
cp /tmp/reset-sandbox-temp.sh "$SCRIPT_PATH"
chmod +x "$SCRIPT_PATH"
rm /tmp/reset-sandbox-temp.sh

echo "3️⃣ Resetting database..."
mysql -u root << EOF
DROP DATABASE IF EXISTS xwander_fi_sandbox;
CREATE DATABASE xwander_fi_sandbox CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON xwander_fi_sandbox.* TO 'xwander_fi_sandbox'@'localhost';
FLUSH PRIVILEGES;
EOF

echo "4️⃣ Cloning database from dev..."
mysqldump -u root xwander_fi_dev | mysql -u root xwander_fi_sandbox

echo "5️⃣ Updating URLs in database..."
cd web
wp search-replace 'dev.xwander.fi' 'sandbox.xwander.fi' \
    --path=wp \
    --skip-columns=guid \
    --report-changed-only

echo "6️⃣ Syncing uploads from dev..."
sudo rsync -av --delete \
    /data/xwander-platform/xwander.fi/uploads/ \
    /data/xwander-platform/xwander.fi/uploads/sandbox/ \
    --exclude=sandbox/

echo "7️⃣ Fixing permissions..."
sudo chown -R www-data:www-data /data/xwander-platform/xwander.fi/uploads/sandbox/

echo "8️⃣ Installing composer dependencies..."
composer install --no-dev --optimize-autoloader --ignore-platform-req=php

echo "9️⃣ Clearing caches..."
wp cache flush --path=wp
wp rewrite flush --path=wp

echo "🔟 Committing reset state to Git..."
cd ..
git add -A
git commit -m "Reset sandbox from dev - $(date +%Y%m%d_%H%M%S)" || true

echo "✅ Sandbox reset complete!"
echo "🌐 Visit: https://sandbox.xwander.fi"
echo "👤 Use your dev environment credentials to log in"
echo ""
echo "⚠️  Note: DNS record for sandbox.xwander.fi must be configured"
echo "   pointing to server IP for HTTPS to work"