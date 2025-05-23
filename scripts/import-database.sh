#!/bin/bash
# Import WordPress database to Bedrock structure

# Define paths and settings
SQL_FILE="/srv/xwander-platform/xwander.fi/migration-files/extracted/wp-content/mysql.sql"
ENV_FILE="/srv/xwander-platform/xwander.fi/dev/.env"

# Source the .env file
if [ -f "$ENV_FILE" ]; then
  export $(grep -v '^#' "$ENV_FILE" | xargs -d '\n')
else
  echo "Error: .env file not found at $ENV_FILE"
  exit 1
fi

# Check if SQL file exists
if [ ! -f "$SQL_FILE" ]; then
  echo "Error: SQL file not found at $SQL_FILE"
  exit 1
fi

echo "Starting database import..."
echo "SQL File: $SQL_FILE"
echo "Database: $DB_NAME"
echo "User: $DB_USER"

# Import database (commented out since MySQL is not installed)
# mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < "$SQL_FILE"

# Search and replace old URLs (commented out since MySQL is not installed)
# OLD_URL=$(grep -o "https://[^'\"]*xwander.fi" "$SQL_FILE" | head -1)
# if [ -n "$OLD_URL" ]; then
#   echo "Found old URL: $OLD_URL"
#   echo "New URL: $WP_HOME"
#   
#   # Use WP-CLI to perform search-replace (if available)
#   if command -v wp &> /dev/null; then
#     cd "/srv/xwander-platform/xwander.fi/dev/web"
#     wp search-replace "$OLD_URL" "$WP_HOME" --all-tables --skip-columns=guid
#   else
#     echo "WP-CLI not found. Manual search-replace will be needed."
#   fi
# fi

echo "⚠️  Database import simulation completed"
echo "MySQL appears to be not installed. Please install MySQL and uncomment the actual commands in this script."