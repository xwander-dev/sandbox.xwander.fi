# xwander.fi WordPress Migration Plan

This document outlines the comprehensive migration plan for xwander.fi from its current state to a native WordPress installation using WP-CLI. We've decided to abandon the Bedrock approach in favor of a more traditional WordPress setup with Git-based version control and modern best practices.

## Table of Contents

1. [Migration Overview](#migration-overview)
2. [Directory Structure](#directory-structure)
3. [Environment Setup](#environment-setup)
4. [Migration Steps](#migration-steps)
5. [WP-CLI Configuration](#wp-cli-configuration)
6. [Nginx Configuration](#nginx-configuration)
7. [Automated Tasks](#automated-tasks)
8. [Performance Optimization](#performance-optimization)
9. [Security Considerations](#security-considerations)
10. [Post-Migration Verification](#post-migration-verification)
11. [Rollback Plan](#rollback-plan)

## Migration Overview

The migration will:
- Move from a Bedrock-based structure to a native WordPress installation
- Utilize WP-CLI for all WordPress management tasks
- Implement version control with Git, treating WordPress core as external
- Separate code and data according to WP-BLUEPRINT standards
- Configure environments (dev, qa, prod) consistently
- Set up automation for routine maintenance tasks

## Directory Structure

```
/srv/xwander-platform/xwander.fi/
├── dev/                            # Development environment
│   ├── wp-content/                 # Git-tracked content directory
│   │   ├── mu-plugins/             # Must-use plugins
│   │   ├── plugins/                # WordPress plugins
│   │   ├── themes/                 # WordPress themes
│   │   └── uploads/ -> /data/...   # Symlink to uploads directory
│   ├── wp-config.php               # Environment-specific config
│   └── .wp-cli/                    # WP-CLI configuration
├── qa/                             # QA environment
│   └── [Similar structure to dev]
├── prod/                           # Production environment
│   └── [Similar structure to dev]
├── scripts/                        # Migration and maintenance scripts
│   ├── wp-migrate.sh               # Main migration script
│   ├── import-db.sh                # Database import script
│   ├── migrate-uploads.sh          # Uploads migration script
│   └── deploy.sh                   # Deployment script
├── docs/                           # Documentation
│   └── xwander_fi_migration.md     # This document
└── .gitignore                      # Git ignore rules
```

External data directories (not in Git):
```
/data/xwander-platform/xwander.fi/
├── dev/uploads/                    # Development environment uploads
├── qa/uploads/                     # QA environment uploads
└── prod/uploads/                   # Production environment uploads
```

## Environment Setup

### Prerequisites

- PHP 8.1 or later
- MySQL/MariaDB 10.11 or later
- WP-CLI 2.8 or later
- Git
- Nginx or Apache

### WP-CLI Installation

```bash
# Download WP-CLI
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar

# Make executable and move to PATH
chmod +x wp-cli.phar
sudo mv wp-cli.phar /usr/local/bin/wp

# Verify installation
wp --info
```

## Migration Steps

### 1. Prepare Source Files

1. Extract backup files from existing WordPress installation:
   ```bash
   mkdir -p /srv/xwander-platform/xwander.fi/migration-files/extracted
   # Extract backup files into this directory
   ```

2. Analyze content structure:
   ```bash
   wp core verify-checksums --path=/srv/xwander-platform/xwander.fi/migration-files/extracted
   wp plugin list --path=/srv/xwander-platform/xwander.fi/migration-files/extracted
   wp theme list --path=/srv/xwander-platform/xwander.fi/migration-files/extracted
   ```

### 2. Set Up Directory Structure

1. Create necessary directories:
   ```bash
   mkdir -p /srv/xwander-platform/xwander.fi/dev/wp-content/{plugins,themes,mu-plugins}
   mkdir -p /data/xwander-platform/xwander.fi/dev/uploads
   ```

2. Create symlinks for uploads:
   ```bash
   ln -sf /data/xwander-platform/xwander.fi/dev/uploads /srv/xwander-platform/xwander.fi/dev/wp-content/uploads
   ```

### 3. Download WordPress Core

```bash
cd /srv/xwander-platform/xwander.fi/dev
wp core download --skip-content
```

### 4. Configure WordPress

1. Create configuration files:
   ```bash
   cd /srv/xwander-platform/xwander.fi/dev
   wp config create --dbname=xwander_fi_dev --dbuser=xwander_fi_dev --dbpass=YOUR_PASSWORD \
     --dbhost=localhost --dbprefix=wp_ --extra-php <<PHP
// Environment-specific settings
define('WP_ENV', 'development');
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Security settings
define('DISALLOW_FILE_EDIT', true);
define('AUTOMATIC_UPDATER_DISABLED', true);
define('WP_AUTO_UPDATE_CORE', false);

// Performance settings
define('WP_CACHE', false);
define('WP_MEMORY_LIMIT', '256M');
PHP
   ```

### 5. Database Migration

1. Create database:
   ```bash
   wp db create --path=/srv/xwander-platform/xwander.fi/dev
   ```

2. Import database:
   ```bash
   wp db import /srv/xwander-platform/xwander.fi/migration-files/extracted/database-export.sql --path=/srv/xwander-platform/xwander.fi/dev
   ```

3. Update URLs:
   ```bash
   wp search-replace 'old-domain.com' 'dev.xwander.fi' --all-tables --path=/srv/xwander-platform/xwander.fi/dev
   ```

### 6. Migrate Content

1. Migrate themes:
   ```bash
   rsync -av /srv/xwander-platform/xwander.fi/migration-files/extracted/wp-content/themes/xwander/ \
     /srv/xwander-platform/xwander.fi/dev/wp-content/themes/xwander/
   ```

2. Migrate plugins:
   ```bash
   # Copy plugins using script with selective filtering
   bash /srv/xwander-platform/xwander.fi/scripts/migrate-plugins.sh
   ```

3. Migrate uploads:
   ```bash
   rsync -av /srv/xwander-platform/xwander.fi/migration-files/extracted/wp-content/uploads/ \
     /data/xwander-platform/xwander.fi/dev/uploads/
   ```

### 7. Finalize Installation

1. Update WordPress options:
   ```bash
   wp option update blogname "XWander.fi (Dev)" --path=/srv/xwander-platform/xwander.fi/dev
   wp option update blogdescription "Development Environment" --path=/srv/xwander-platform/xwander.fi/dev
   wp option update permalink_structure '/%postname%/' --path=/srv/xwander-platform/xwander.fi/dev
   ```

2. Set up admin user:
   ```bash
   wp user create admin admin@xwander.fi --role=administrator --user_pass=SECURE_PASSWORD --path=/srv/xwander-platform/xwander.fi/dev
   ```

## WP-CLI Configuration

Create a project-specific WP-CLI configuration file:

```bash
# Create .wp-cli/config.yml in each environment
cat > /srv/xwander-platform/xwander.fi/dev/.wp-cli/config.yml << EOL
path: /srv/xwander-platform/xwander.fi/dev
url: https://dev.xwander.fi
color: true
debug: true

# Default settings for commands
core update:
  minor: true

plugin update:
  all: true

theme update:
  all: true
EOL
```

## Nginx Configuration

Create Nginx server configuration for dev environment:

```nginx
server {
    listen 80;
    server_name dev.xwander.fi;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name dev.xwander.fi;

    root /srv/xwander-platform/xwander.fi/dev;
    index index.php;

    # SSL configuration
    ssl_certificate /etc/letsencrypt/live/dev.xwander.fi/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/dev.xwander.fi/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers on;

    # Security headers
    add_header X-Content-Type-Options nosniff;
    add_header X-Frame-Options SAMEORIGIN;
    add_header X-XSS-Protection "1; mode=block";
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Access logs
    access_log /var/log/nginx/dev.xwander.fi.access.log;
    error_log /var/log/nginx/dev.xwander.fi.error.log;

    # WordPress permalinks
    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    # PHP-FPM configuration
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to sensitive files
    location ~ /\.(ht|git) {
        deny all;
    }

    # Deny access to wp-config.php
    location = /wp-config.php {
        deny all;
    }

    # Cache-Control for static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires max;
        log_not_found off;
    }
}
```

## Automated Tasks

Create a `crontab` entry for routine maintenance:

```bash
# Daily database backup
0 1 * * * cd /srv/xwander-platform/xwander.fi/dev && wp db export /data/xwander-platform/backups/xwander_fi/dev_$(date +\%Y\%m\%d).sql --path=/srv/xwander-platform/xwander.fi/dev

# Weekly plugin and theme updates (QA environment only)
0 2 * * 1 cd /srv/xwander-platform/xwander.fi/qa && wp plugin update --all --path=/srv/xwander-platform/xwander.fi/qa && wp theme update --all --path=/srv/xwander-platform/xwander.fi/qa

# Daily cache cleaning
0 3 * * * cd /srv/xwander-platform/xwander.fi/dev && wp cache flush --path=/srv/xwander-platform/xwander.fi/dev
```

## Performance Optimization

1. Install and configure optimal caching:
   ```bash
   wp plugin install wp-fastest-cache --activate --path=/srv/xwander-platform/xwander.fi/dev
   wp plugin install autoptimize --activate --path=/srv/xwander-platform/xwander.fi/dev
   ```

2. Optimize database:
   ```bash
   wp db optimize --path=/srv/xwander-platform/xwander.fi/dev
   ```

3. Configure object caching with Redis (recommended for production):
   ```bash
   wp plugin install redis-cache --activate --path=/srv/xwander-platform/xwander.fi/prod
   ```

## Security Considerations

1. Set proper file permissions:
   ```bash
   # Set directories to 755
   find /srv/xwander-platform/xwander.fi/dev -type d -exec chmod 755 {} \;
   
   # Set files to 644
   find /srv/xwander-platform/xwander.fi/dev -type f -exec chmod 644 {} \;
   
   # Make wp-config.php more secure
   chmod 600 /srv/xwander-platform/xwander.fi/dev/wp-config.php
   ```

2. Install security plugins:
   ```bash
   wp plugin install wordfence --activate --path=/srv/xwander-platform/xwander.fi/dev
   ```

3. Configure WordPress Salts:
   ```bash
   wp config shuffle-salts --path=/srv/xwander-platform/xwander.fi/dev
   ```

## Post-Migration Verification

1. Run site health check:
   ```bash
   wp site health --path=/srv/xwander-platform/xwander.fi/dev
   ```

2. Verify plugins are working:
   ```bash
   wp plugin status --path=/srv/xwander-platform/xwander.fi/dev
   ```

3. Test theme functionality:
   ```bash
   wp theme status --path=/srv/xwander-platform/xwander.fi/dev
   ```

4. Check for broken links:
   ```bash
   wp plugin install broken-link-checker --activate --path=/srv/xwander-platform/xwander.fi/dev
   ```

5. Manual testing checklist:
   - Verify frontend appearance
   - Test responsive design on mobile devices
   - Check contact forms and other interactive elements
   - Test user registration and login
   - Verify multilingual functionality (if applicable)
   - Check integration with third-party services

## Rollback Plan

In case of migration failure, prepare a rollback strategy:

1. Backup all files before migration:
   ```bash
   tar -czf /srv/xwander-platform/backup/xwander.fi-pre-migration-$(date +%Y%m%d-%H%M%S).tar.gz /srv/xwander-platform/xwander.fi
   ```

2. Backup database before changes:
   ```bash
   wp db export /srv/xwander-platform/backup/xwander.fi-db-pre-migration-$(date +%Y%m%d-%H%M%S).sql --path=/path/to/current/installation
   ```

3. Rollback procedure:
   ```bash
   # Restore files
   rm -rf /srv/xwander-platform/xwander.fi
   tar -xzf /srv/xwander-platform/backup/xwander.fi-pre-migration-*.tar.gz -C /srv/xwander-platform/
   
   # Restore database
   wp db import /srv/xwander-platform/backup/xwander.fi-db-pre-migration-*.sql --path=/path/to/restored/installation
   ```

## Conclusion

This migration plan provides a comprehensive approach to transitioning xwander.fi to a native WordPress installation with modern best practices. By following these steps, we will establish a robust, maintainable, and secure WordPress environment that follows the WP-BLUEPRINT standards while leveraging the power of WP-CLI for automation and management.

The migration will be executed in phases, starting with the development environment, then QA, and finally production. Each phase will include thorough testing to ensure a smooth transition and minimal disruption to the live site.