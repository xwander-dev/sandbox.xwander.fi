# xwander.fi WordPress to Bedrock Migration

This document outlines the process of migrating the xwander.fi WordPress site from a traditional WordPress installation to a Bedrock-based structure following the WP-BLUEPRINT standard.

## Migration Overview

The migration process involves:

1. Setting up a Bedrock structure
2. Migrating the custom theme
3. Analyzing and migrating plugins
4. Importing the database
5. Migrating uploads (media files)
6. Configuring the web server

## Migration Scripts

The following scripts have been created to automate the migration process:

- `migrate-all.sh`: Master script that runs all migration steps
- `migrate-theme.sh`: Migrates the custom xwander theme
- `analyze-plugins.sh`: Analyzes plugins and creates a report
- `import-database.sh`: Imports the WordPress database
- `migrate-uploads.sh`: Migrates media files

## Bedrock Structure

Bedrock provides a more secure and maintainable WordPress project structure:

```
xwander.fi/
├── dev/                    # Development environment
│   ├── composer.json       # Composer configuration
│   ├── config/             # WordPress configuration
│   ├── web/                # Web root
│   │   ├── app/            # WordPress content directory
│   │   │   ├── mu-plugins/ # Must-use plugins
│   │   │   ├── plugins/    # WordPress plugins
│   │   │   ├── themes/     # WordPress themes
│   │   │   └── uploads/    # Uploaded media files
│   │   ├── wp/             # WordPress core (installed via Composer)
│   │   └── index.php       # WordPress front controller
│   └── .env                # Environment variables
├── qa/                     # QA environment
└── prod/                   # Production environment
```

## Requirements

- PHP 7.4 or later
- Composer
- MySQL 5.7 or later
- Web server (Nginx or Apache)

## Migration Steps

### 1. Setup Bedrock Structure

The Bedrock structure has been initialized with:

```bash
python3 /srv/xwander-platform/tools/wp.py init xwander.fi
```

### 2. Theme Migration

The custom xwander theme has been migrated to the Bedrock structure:

```bash
./migrate-theme.sh
```

### 3. Plugin Analysis and Migration

Plugins have been analyzed and categorized as:
- Composer-installable
- Premium/custom (requiring manual installation)

```bash
./analyze-plugins.sh
```

### 4. Database Import

The database import script has been prepared:

```bash
./import-database.sh
```

**Note:** MySQL is currently not installed on the system. You'll need to install it before running the database import.

### 5. Uploads Migration

The uploads migration script is ready:

```bash
./migrate-uploads.sh
```

**Note:** This can take a long time for large sites.

## Next Steps

1. Install MySQL and import the database
2. Run `composer install` in the dev directory
3. Configure the web server to point to the `/srv/xwander-platform/xwander.fi/dev/web` directory
4. Verify the site functionality
5. Deploy to production

## Issues and Considerations

- Database connection parameters need to be configured in the `.env` file
- Some plugins may need manual configuration after migration
- URLs in the database need to be updated
- WPML settings may need additional configuration
- ACF fields need to be verified after migration