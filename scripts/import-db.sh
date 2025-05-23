#!/bin/bash
# xwander.fi Database Import Script following WP-BLUEPRINT standards

# Exit on error
set -e

# Default values
DB_PREFIX="xwander_fi"
DB_ENV=${1:-"dev"}  # Default to dev if not provided
DB_NAME="${DB_PREFIX}_${DB_ENV}"
SQL_FILE=${2:-"/srv/xwander-platform/xwander.fi/migration-files/extracted/wp-content/mysql.sql"}
OLD_URL="http://xwander.fi"
NEW_URL="https://dev.xwander.fi"

# Print banner
echo "============================================="
echo "Database Import for xwander.fi - ${DB_ENV} Environment"
echo "Following WP-BLUEPRINT Standards"
echo "============================================="

# Check if SQL file exists
if [ ! -f "$SQL_FILE" ]; then
    echo "⚠️  SQL file not found: $SQL_FILE"
    exit 1
fi

# Check if MariaDB is installed
if ! command -v mariadb &> /dev/null; then
    echo "⚠️  MariaDB is not installed. Please install MariaDB 10.11+"
    echo "    sudo apt update && sudo apt install mariadb-server mariadb-client"
    exit 1
fi

# Read database credentials from .env file
ENV_FILE="/srv/xwander-platform/xwander.fi/dev/.env"
if [ ! -f "$ENV_FILE" ]; then
    echo "⚠️  .env file not found: $ENV_FILE"
    echo "   Run create-db.sh first to set up the database"
    exit 1
fi

# Parse .env file
eval "$(grep -v '^#' "$ENV_FILE" | sed 's/^/export /' | sed 's/=\(.*\)/="\1"/')"

echo "Database info:"
echo "  Name: $DB_NAME"
echo "  User: $DB_USER"
echo "  Host: $DB_HOST"

# Check if WP-CLI is available for URL replacement
WP_CLI_AVAILABLE=false
if command -v wp &> /dev/null; then
    WP_CLI_AVAILABLE=true
    echo "✅ WP-CLI available for URL replacement"
else
    echo "⚠️  WP-CLI not available, will use SQL for URL replacement"
fi

# Create a backup before import
echo "Creating backup before import..."
BACKUP_DIR="/data/xwander-platform/backups/xwander_fi"
mkdir -p "$BACKUP_DIR"
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump ${DB_NAME} > "${BACKUP_DIR}/${DATE}_${DB_NAME}_pre_import.sql"
gzip "${BACKUP_DIR}/${DATE}_${DB_NAME}_pre_import.sql"
echo "✅ Backup created: ${BACKUP_DIR}/${DATE}_${DB_NAME}_pre_import.sql.gz"

# Import database
echo "Importing database from: $SQL_FILE"
echo "This may take some time for large databases..."
if ! mysql -u ${DB_USER} -p${DB_PASSWORD} -h ${DB_HOST} ${DB_NAME} < ${SQL_FILE}; then
    echo "⚠️  Import failed. Please check the SQL file and database credentials."
    exit 1
fi
echo "✅ Database imported successfully"

# URL replacement
echo "Updating URLs: $OLD_URL -> $NEW_URL"

if [ "$WP_CLI_AVAILABLE" = true ]; then
    # Use WP-CLI for URL replacement
    cd /srv/xwander-platform/xwander.fi/dev/web
    wp search-replace "$OLD_URL" "$NEW_URL" --all-tables --skip-columns=guid
    echo "✅ URLs updated with WP-CLI"
else
    # Use SQL for URL replacement
    SQL_REPLACE_FILE="$(mktemp)"
    cat > "$SQL_REPLACE_FILE" <<EOF
-- Update site URL and home URL
UPDATE wp_options SET option_value = REPLACE(option_value, '$OLD_URL', '$NEW_URL') WHERE option_name IN ('siteurl', 'home');

-- Update URLs in post content
UPDATE wp_posts SET post_content = REPLACE(post_content, '$OLD_URL', '$NEW_URL');

-- Update URLs in post excerpts
UPDATE wp_posts SET post_excerpt = REPLACE(post_excerpt, '$OLD_URL', '$NEW_URL');

-- Update URLs in post GUID (excluding media attachments)
UPDATE wp_posts SET guid = REPLACE(guid, '$OLD_URL', '$NEW_URL') WHERE post_type != 'attachment';

-- Update URLs in postmeta
UPDATE wp_postmeta SET meta_value = REPLACE(meta_value, '$OLD_URL', '$NEW_URL') WHERE meta_value LIKE '%$OLD_URL%' AND meta_value NOT LIKE '%s:%';

-- Update serialized data in options
UPDATE wp_options SET option_value = REPLACE(option_value, 's:' + CHAR_LENGTH('$OLD_URL') + ':"$OLD_URL"', 's:' + CHAR_LENGTH('$NEW_URL') + ':"$NEW_URL"') WHERE option_value LIKE '%$OLD_URL%' AND option_value LIKE '%s:%';
EOF

    # Execute URL replacement
    mysql -u ${DB_USER} -p${DB_PASSWORD} -h ${DB_HOST} ${DB_NAME} < ${SQL_REPLACE_FILE}
    rm "$SQL_REPLACE_FILE"
    echo "✅ URLs updated with SQL"
fi

# Success message
echo ""
echo "============================================="
echo "✅ Database import completed successfully!"
echo "============================================="
echo "SQL file: $SQL_FILE"
echo "Database: $DB_NAME"
echo "URL replaced: $OLD_URL -> $NEW_URL"
echo ""
echo "Next steps:"
echo "1. Migrate uploads: ./scripts/migrate-uploads.sh $DB_ENV"
echo "2. Run composer install: cd dev && composer install"
echo "3. Configure web server to point to /srv/xwander-platform/xwander.fi/dev/web"
echo "============================================="