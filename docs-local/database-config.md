# xwander.fi Database Configuration

This document outlines the database configuration for the xwander.fi WordPress site. We follow the WP-BLUEPRINT database standards.

## MariaDB Configuration

We use MariaDB 10.11+ as recommended by WP-BLUEPRINT standards:

```ini
# /etc/mysql/mariadb.conf.d/99-xwander-xwander_fi.cnf
[mysqld]
# WP-BLUEPRINT standards for xwander.fi
innodb_buffer_pool_size = 512M
max_connections = 100
character_set_server = utf8mb4
collation_server = utf8mb4_unicode_ci
default_storage_engine = InnoDB
```

## Database Naming

We follow the standard naming convention:

- **Development**: `xwander_fi_dev`
- **QA**: `xwander_fi_qa`
- **Production**: `xwander_fi_prod`

Each environment has its own dedicated database user with the same name.

## Database Location

Following WP-BLUEPRINT standards, all database files are stored outside of Git in:

```
/data/xwander-platform/databases/xwander_fi/
```

## Backup Strategy

- **Development**: Weekly backups, keep 4 weeks
- **Production**: Daily backups, keep 10 days
- **Before major changes**: Manual backup

Backups are stored in:

```
/data/xwander-platform/backups/xwander_fi/
```

The backup script (`/srv/xwander-platform/xwander.fi/scripts/backup-db.sh`) is scheduled to run weekly via cron.

## Database Creation

The database is created using the script at `/srv/xwander-platform/xwander.fi/scripts/create-db.sh`. This script:

1. Creates the necessary data directories
2. Generates a MariaDB configuration file
3. Creates the database with UTF8MB4 encoding
4. Creates a database user with a strong password
5. Updates the `.env` file with the database credentials
6. Creates and schedules a backup script

## Database Import

The import script at `/srv/xwander-platform/xwander.fi/scripts/import-db.sh` handles:

1. Creating a backup before import
2. Importing the SQL dump file
3. Replacing URLs in the database
4. Setting proper permissions

## Database Migration

When migrating from a traditional WordPress installation to Bedrock:

1. Export the database from the source WordPress site
2. Run the `create-db.sh` script to create a new database
3. Run the `import-db.sh` script to import the database
4. Update URLs and site configuration as needed

## WordPress Configuration

The database credentials are stored in the `.env` file in each environment:

```
# Database Configuration
DB_NAME='xwander_fi_dev'
DB_USER='xwander_fi_dev'
DB_PASSWORD='strong_generated_password'
DB_HOST='localhost'
```

WordPress reads these values through the Bedrock configuration system.