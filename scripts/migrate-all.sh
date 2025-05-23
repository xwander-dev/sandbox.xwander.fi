#!/bin/bash
# Master script for WordPress to Bedrock migration
echo "=========================================="
echo "WordPress to Bedrock Migration"
echo "=========================================="
echo "Starting migration process at $(date)"
echo ""

# Check if directories exist
if [ ! -d "/srv/xwander-platform/xwander.fi/migration-files/extracted" ]; then
  echo "Error: Source files not found in /srv/xwander-platform/xwander.fi/migration-files/extracted"
  exit 1
fi

if [ ! -d "/srv/xwander-platform/xwander.fi/dev" ]; then
  echo "Error: Bedrock structure not found in /srv/xwander-platform/xwander.fi/dev"
  exit 1
fi

# Step 1: Migrate Theme
echo "Step 1: Migrating Theme"
echo "----------------------------------------"
/srv/xwander-platform/xwander.fi/migrate-theme.sh
echo ""

# Step 2: Analyze and Copy Plugins
echo "Step 2: Analyzing and Migrating Plugins"
echo "----------------------------------------"
/srv/xwander-platform/xwander.fi/analyze-plugins.sh
echo ""

# Step 3: Import Database (if MySQL is available)
echo "Step 3: Importing Database"
echo "----------------------------------------"
/srv/xwander-platform/xwander.fi/import-database.sh
echo ""

# Step 4: Migrate Uploads (optional - can take a long time)
read -p "Do you want to migrate uploads now? This could take a long time for large sites. (y/n): " migrate_uploads
if [[ "$migrate_uploads" == "y" || "$migrate_uploads" == "Y" ]]; then
  echo "Step 4: Migrating Uploads"
  echo "----------------------------------------"
  /srv/xwander-platform/xwander.fi/migrate-uploads.sh
  echo ""
else
  echo "Uploads migration skipped. You can run it later with:"
  echo "/srv/xwander-platform/xwander.fi/migrate-uploads.sh"
  echo ""
fi

echo "Migration completed at $(date)"
echo "----------------------------------------"
echo "Next steps:"
echo "1. Install composer dependencies with: cd /srv/xwander-platform/xwander.fi/dev && composer install"
echo "2. Set up a web server to point to /srv/xwander-platform/xwander.fi/dev/web"
echo "3. Access the site and verify functionality"
echo "4. Fix any issues with database or configuration"
echo "=========================================="