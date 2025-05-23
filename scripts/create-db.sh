#!/bin/bash
# xwander.fi MariaDB setup script following WP-BLUEPRINT standards

# Exit on error
set -e

# Default values
DB_PREFIX="xwander_fi"
DB_ENV=${1:-"dev"}  # Default to dev if not provided
DB_NAME="${DB_PREFIX}_${DB_ENV}"
DB_USER="${DB_PREFIX}_${DB_USER}"
DB_PASSWORD=$(openssl rand -base64 32 | tr -dc 'a-zA-Z0-9' | fold -w 16 | head -n 1)
DATA_DIR="/data/xwander-platform/databases/xwander_fi"
BACKUP_DIR="/data/xwander-platform/backups/xwander_fi"

# Print banner
echo "============================================="
echo "MariaDB Setup for xwander.fi - ${DB_ENV} Environment"
echo "Following WP-BLUEPRINT Database Standards"
echo "============================================="

# Create data directories
echo "Creating data directories..."
mkdir -p "$DATA_DIR"
mkdir -p "$BACKUP_DIR"
mkdir -p "/data/xwander-platform/volumes/xwander.fi/${DB_ENV}/uploads"

# Check if MariaDB is installed
if ! command -v mariadb &> /dev/null; then
    echo "⚠️  MariaDB is not installed. Please install MariaDB 10.11+"
    echo "    sudo apt update && sudo apt install mariadb-server mariadb-client"
    exit 1
fi

# Create MariaDB configuration
echo "Creating MariaDB configuration..."
CONFIG_DIR="/etc/mysql/mariadb.conf.d"
CONFIG_FILE="99-xwander-${DB_PREFIX}.cnf"

# Check if we have permission to write to config dir
if [ -w "$CONFIG_DIR" ]; then
    cat > "$CONFIG_DIR/$CONFIG_FILE" <<EOF
[mysqld]
# WP-BLUEPRINT standards for xwander.fi
innodb_buffer_pool_size = 512M
max_connections = 100
character_set_server = utf8mb4
collation_server = utf8mb4_unicode_ci
default_storage_engine = InnoDB
EOF
    echo "✅ MariaDB configuration created at $CONFIG_DIR/$CONFIG_FILE"
else
    echo "⚠️  Cannot write to $CONFIG_DIR. Creating local configuration file."
    cat > "/srv/xwander-platform/xwander.fi/scripts/$CONFIG_FILE" <<EOF
[mysqld]
# WP-BLUEPRINT standards for xwander.fi
innodb_buffer_pool_size = 512M
max_connections = 100
character_set_server = utf8mb4
collation_server = utf8mb4_unicode_ci
default_storage_engine = InnoDB
EOF
    echo "✅ MariaDB configuration created at /srv/xwander-platform/xwander.fi/scripts/$CONFIG_FILE"
    echo "   Please copy this file to $CONFIG_DIR/ as root:"
    echo "   sudo cp /srv/xwander-platform/xwander.fi/scripts/$CONFIG_FILE $CONFIG_DIR/"
fi

# Create SQL commands file
SQL_COMMANDS_FILE="$(mktemp)"
cat > "$SQL_COMMANDS_FILE" <<EOF
-- Create database with proper encoding
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user with strong password
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';

-- Grant privileges
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;
EOF

# Run SQL commands
echo "Creating database and user..."
if ! mysql -u root < "$SQL_COMMANDS_FILE"; then
    echo "⚠️  Failed to create database. Trying with password prompt..."
    mysql -u root -p < "$SQL_COMMANDS_FILE"
fi

# Remove temporary file
rm "$SQL_COMMANDS_FILE"

# Create .env file with database settings
ENV_FILE="/srv/xwander-platform/xwander.fi/dev/.env"
if [ -f "$ENV_FILE" ]; then
    # Update existing .env file
    sed -i "s/^DB_NAME=.*$/DB_NAME='${DB_NAME}'/" "$ENV_FILE"
    sed -i "s/^DB_USER=.*$/DB_USER='${DB_USER}'/" "$ENV_FILE"
    sed -i "s/^DB_PASSWORD=.*$/DB_PASSWORD='${DB_PASSWORD}'/" "$ENV_FILE"
    sed -i "s/^DB_HOST=.*$/DB_HOST='localhost'/" "$ENV_FILE"
    echo "✅ Updated .env file with database credentials"
else
    echo "⚠️  .env file not found at $ENV_FILE"
fi

# Configure database backup
BACKUP_SCRIPT="/srv/xwander-platform/xwander.fi/scripts/backup-db.sh"
cat > "$BACKUP_SCRIPT" <<EOF
#!/bin/bash
# Database backup script for xwander.fi

# Exit on error
set -e

# Default values
DB_PREFIX="xwander_fi"
DB_ENV=\${1:-"dev"}  # Default to dev if not provided
DB_NAME="\${DB_PREFIX}_\${DB_ENV}"
BACKUP_DIR="/data/xwander-platform/backups/xwander_fi"
DATE=\$(date +%Y%m%d)

# Create backup directory if it doesn't exist
mkdir -p "\$BACKUP_DIR"

# Backup database
mysqldump \${DB_NAME} > "\${BACKUP_DIR}/\${DATE}_\${DB_NAME}.sql"
gzip "\${BACKUP_DIR}/\${DATE}_\${DB_NAME}.sql"

# Keep 10 most recent backups
cd "\$BACKUP_DIR" && ls -t *.sql.gz | tail -n +11 | xargs rm -f

echo "✅ Database backup completed: \${BACKUP_DIR}/\${DATE}_\${DB_NAME}.sql.gz"
EOF

chmod +x "$BACKUP_SCRIPT"
echo "✅ Created database backup script: $BACKUP_SCRIPT"

# Set up cron job
CRON_JOB="0 0 * * 0 $BACKUP_SCRIPT"
if command -v crontab &> /dev/null; then
    (crontab -l 2>/dev/null || echo "") | grep -v "$BACKUP_SCRIPT" | { cat; echo "$CRON_JOB"; } | crontab -
    echo "✅ Added weekly backup cron job"
else
    echo "⚠️  crontab not available. To add backup schedule, run:"
    echo "   crontab -e"
    echo "   And add the following line:"
    echo "   $CRON_JOB"
fi

# Success message
echo ""
echo "============================================="
echo "✅ MariaDB setup completed successfully!"
echo "============================================="
echo "Database name: $DB_NAME"
echo "Database user: $DB_USER"
echo "Database password: $DB_PASSWORD"
echo ""
echo "Data directory: $DATA_DIR"
echo "Backup directory: $BACKUP_DIR"
echo ""
echo "Next steps:"
echo "1. If prompted, copy the configuration file to $CONFIG_DIR/"
echo "2. Restart MariaDB: sudo systemctl restart mariadb"
echo "3. Import the database: ./scripts/import-db.sh"
echo "============================================="