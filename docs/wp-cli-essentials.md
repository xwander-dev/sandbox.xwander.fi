# WP-CLI Essentials (2025 Edition)

## Overview

WP-CLI is the official command-line interface for WordPress, providing a powerful and efficient way to manage WordPress installations without using a web browser. This document covers the latest best practices, installation methods, essential commands, and advanced usage patterns for modern WordPress development workflows.

## Installation

### Standard Installation (All Platforms)

```bash
# Download WP-CLI
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar

# Make it executable
chmod +x wp-cli.phar

# Move to PATH
sudo mv wp-cli.phar /usr/local/bin/wp

# Verify installation
wp --info
```

### Platform-Specific Methods

#### macOS (using Homebrew)
```bash
brew install wp-cli
```

#### Linux (using Package Managers)
```bash
# Debian/Ubuntu
sudo apt update
sudo apt install wp-cli

# CentOS/RHEL
sudo yum install wp-cli
```

#### Windows (using Composer)
```bash
composer global require wp-cli/wp-cli-bundle
```

### Security Best Practices for Installation

1. Verify checksums after downloading:
   ```bash
   curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar.sha512
   sha512sum -c wp-cli.phar.sha512
   ```

2. Set proper file permissions:
   ```bash
   chmod 0755 /usr/local/bin/wp
   ```

3. Keep WP-CLI updated:
   ```bash
   wp cli update
   ```

## Configuration

### Project-Specific Configuration

Create a `wp-cli.yml` file in your project root for environment-specific settings:

```yaml
# Basic configuration
path: public
url: https://example.com
user: admin

# Environment flags
debug: true
color: true

# Default behavior for commands
core update:
  minor: true
plugin update:
  all: true
  exclude: 
    - akismet
    - hello-dolly

# Database charset and collate
core config:
  dbcharset: utf8mb4
  dbcollate: utf8mb4_unicode_ci
```

### Global Configuration

Create a `~/.wp-cli/config.yml` file for global settings:

```yaml
# Global defaults
color: true
disabled_commands:
  - db drop
  - plugin deactivate

# Use custom PHP binary
path: /usr/local/bin/php8.1

# Alias for common sites
@prod:
  ssh: user@production.example.com
  path: /var/www/html

@staging:
  ssh: user@staging.example.com
  path: /var/www/html
```

## Essential Commands

### WordPress Installation & Setup

```bash
# Download WordPress core files
wp core download

# Create configuration file
wp config create --dbname=mydb --dbuser=dbuser --dbpass=dbpass --dbhost=localhost

# Install WordPress
wp core install --url=example.com --title="Site Title" --admin_user=admin --admin_password=securepass --admin_email=admin@example.com

# Update WordPress core
wp core update

# Verify core file integrity
wp core verify-checksums

# Update database
wp core update-db
```

### Plugin Management

```bash
# Search for plugins
wp plugin search seo --fields=name,slug,rating

# Install plugin
wp plugin install wordpress-seo --activate

# Update plugins
wp plugin update --all

# List installed plugins
wp plugin list --status=active --format=csv

# Manage multiple plugins
wp plugin activate plugin1 plugin2 plugin3
wp plugin deactivate plugin1 plugin2
wp plugin delete plugin1 plugin2
```

### Theme Management

```bash
# Install theme
wp theme install twentytwentyfive --activate

# Update themes
wp theme update --all

# List themes
wp theme list --status=active --fields=name,version,update

# Create child theme
wp scaffold child-theme child-theme-name --parent_theme=twentytwentyfive

# Delete themes
wp theme delete twentytwentyfour
```

### Content Management

```bash
# Create post
wp post create --post_title="New Post" --post_content="Content here" --post_status=publish

# List posts
wp post list --post_type=page --fields=ID,post_title,post_status

# Generate test content
wp post generate --count=50 --post_type=post

# Update post
wp post update 123 --post_title="Updated Title"

# Delete post
wp post delete 123 --force
```

### Database Operations

```bash
# Export database
wp db export backup.sql

# Import database
wp db import backup.sql

# Execute SQL query
wp db query "SELECT ID, post_title FROM wp_posts LIMIT 5;"

# Find and replace strings
wp search-replace 'http://dev.example.com' 'https://example.com' --all-tables --precise --dry-run

# Optimize database
wp db optimize

# Repair database
wp db repair
```

### User Management

```bash
# Create user
wp user create editor editor@example.com --role=editor --user_pass=securepass

# List users
wp user list --role=subscriber --fields=ID,user_login,user_email

# Update user
wp user update 123 --user_pass=newpassword --display_name="New Name"

# Delete user
wp user delete 123 --reassign=456

# Reset password
wp user reset-password admin
```

### Site Maintenance

```bash
# Flush rewrite rules
wp rewrite flush

# Clear cache
wp cache flush

# Enable/disable maintenance mode
wp maintenance-mode activate
wp maintenance-mode deactivate

# Check site health
wp site health status

# Run cron events
wp cron event run --due-now
```

## Advanced Usage Patterns

### Automation Scripts

Create comprehensive deployment scripts combining multiple operations:

```bash
#!/bin/bash
# deployment.sh - WordPress deployment script

# Backup database
wp db export pre-deploy-backup-$(date +%Y%m%d-%H%M%S).sql

# Pull latest code from Git
git pull origin main

# Update WordPress core
wp core update
wp core update-db

# Update plugins and themes
wp plugin update --all
wp theme update --all

# Clear caches
wp cache flush
wp transient delete --all

# Run any database migrations
wp custom-command run-migrations

# Regenerate image sizes if needed
wp media regenerate --only-missing

# Flush rewrite rules
wp rewrite flush

# Verify site health
wp site health status
```

### Custom Commands

Create a custom WP-CLI command in your plugin or theme:

```php
// In your plugin file
if (defined('WP_CLI') && WP_CLI) {
    // Register the command
    WP_CLI::add_command('custom-export', 'Custom_Export_Command');
}

/**
 * Exports custom post types to JSON.
 */
class Custom_Export_Command {
    /**
     * Export a custom post type to JSON.
     *
     * ## OPTIONS
     *
     * <post_type>
     * : The post type to export.
     *
     * [--status=<status>]
     * : Post status to export (publish, draft, etc.).
     * ---
     * default: publish
     * ---
     *
     * ## EXAMPLES
     *
     *     wp custom-export products
     *     wp custom-export events --status=draft
     */
    public function __invoke($args, $assoc_args) {
        list($post_type) = $args;
        $status = $assoc_args['status'] ?? 'publish';
        
        WP_CLI::log("Exporting $post_type posts with status: $status");
        
        $posts = get_posts([
            'post_type' => $post_type,
            'post_status' => $status,
            'numberposts' => -1
        ]);
        
        if (empty($posts)) {
            WP_CLI::warning("No posts found matching criteria.");
            return;
        }
        
        $data = [];
        foreach ($posts as $post) {
            $data[] = [
                'ID' => $post->ID,
                'title' => $post->post_title,
                'content' => $post->post_content,
                'meta' => get_post_meta($post->ID)
            ];
        }
        
        $file = "$post_type-export-" . date('YmdHis') . ".json";
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
        
        WP_CLI::success("Exported " . count($posts) . " posts to $file");
    }
}
```

### Multisite Operations

Commands specific to WordPress multisite networks:

```bash
# Create a new site in the network
wp site create --slug=site2 --title="Second Site" --email=admin@example.com

# List all sites in the network
wp site list

# Activate plugin across all network sites
wp plugin activate woocommerce --network

# Execute command on specific site
wp --url=site2.example.com user list

# Run command on all sites
wp site list --field=url | xargs -I % wp --url=% plugin update --all
```

### Integration with CI/CD Pipelines

Example GitHub Actions workflow for WordPress:

```yaml
name: WordPress CI/CD

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout code
        uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
          extensions: mbstring, intl
      
      - name: Install WP-CLI
        run: |
          curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
          chmod +x wp-cli.phar
          sudo mv wp-cli.phar /usr/local/bin/wp
      
      - name: Run PHP linting
        run: find . -name "*.php" -not -path "./vendor/*" -not -path "./node_modules/*" -print0 | xargs -0 -n1 php -l
      
      - name: Run PHPUnit tests
        run: wp scaffold plugin-tests my-plugin && cd wp-content/plugins/my-plugin && composer install && bin/phpunit
      
      - name: Deploy to server
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.HOST }}
          username: ${{ secrets.USERNAME }}
          key: ${{ secrets.SSH_KEY }}
          script: |
            cd /var/www/html
            git pull origin main
            wp core update
            wp plugin update --all
            wp theme update --all
            wp cache flush
```

## Security Best Practices

### Secure Input Handling

When creating custom commands, always validate and sanitize input:

```php
public function my_command($args, $assoc_args) {
    // Validate required parameters
    if (empty($args[0])) {
        WP_CLI::error('Missing required parameter.');
    }
    
    // Sanitize input
    $post_id = absint($args[0]);
    $status = in_array($assoc_args['status'], ['publish', 'draft', 'private']) 
        ? $assoc_args['status'] 
        : 'draft';
        
    // Confirm destructive actions
    if (!empty($assoc_args['delete']) && !WP_CLI\Utils\get_flag_value($assoc_args, 'yes')) {
        WP_CLI::confirm('Are you sure you want to delete this?');
    }
}
```

### Environment Variables for Sensitive Data

Use environment variables instead of hardcoding sensitive information:

```bash
# Use environment variables for database credentials
wp config create --dbname=$DB_NAME --dbuser=$DB_USER --dbpass=$DB_PASSWORD

# In scripts
if [ -f .env ]; then
  export $(grep -v '^#' .env | xargs)
fi
wp db import $BACKUP_FILE
```

### Access Restrictions

Limit who can use WP-CLI in production environments:

```bash
# In wp-config.php
if (!defined('WP_CLI') || !WP_CLI) {
    // Define additional constants only when not using WP-CLI
    define('DISALLOW_FILE_EDIT', true);
    define('DISALLOW_FILE_MODS', true);
}

// Restrict WP-CLI to specific users
if (defined('WP_CLI') && WP_CLI) {
    $allowed_users = ['www-data', 'admin-user'];
    $current_user = trim(shell_exec('whoami'));
    
    if (!in_array($current_user, $allowed_users)) {
        echo "WP-CLI access denied for user: $current_user\n";
        exit(1);
    }
}
```

## Performance Optimization

### Efficient Batch Processing

Process large amounts of data in batches:

```php
// In a custom command
public function process_large_data($args, $assoc_args) {
    $batch_size = $assoc_args['batch-size'] ?? 100;
    $total = 10000;  // Total items to process
    $batches = ceil($total / $batch_size);
    
    $progress = \WP_CLI\Utils\make_progress_bar('Processing items', $batches);
    
    for ($i = 0; $i < $batches; $i++) {
        $offset = $i * $batch_size;
        
        // Process batch
        $items = get_items($offset, $batch_size);
        foreach ($items as $item) {
            process_item($item);
        }
        
        $progress->tick();
    }
    
    $progress->finish();
    WP_CLI::success("Processed $total items.");
}
```

### Optimized Database Queries

Minimize database impact:

```bash
# Use specific fields rather than retrieving all data
wp post list --fields=ID,post_title,post_date

# Run resource-intensive operations during off-peak hours
wp cron event schedule database_optimization hourly --timestamp=1730

# Use dry-run before performing intensive operations
wp search-replace 'old' 'new' --dry-run
```

## Nginx Configuration for WordPress

Optimized Nginx configuration for WordPress sites using WP-CLI:

```nginx
server {
    listen 80;
    server_name example.com www.example.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name example.com www.example.com;

    # SSL configuration
    ssl_certificate /etc/letsencrypt/live/example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/example.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers on;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 1d;
    ssl_session_tickets off;
    ssl_stapling on;
    ssl_stapling_verify on;
    
    # Security headers
    add_header X-Content-Type-Options nosniff;
    add_header X-Frame-Options SAMEORIGIN;
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy no-referrer-when-downgrade;
    add_header Content-Security-Policy "default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval';" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    
    # Root directory
    root /var/www/html;
    index index.php;
    
    # WordPress permalinks
    location / {
        try_files $uri $uri/ /index.php?$args;
    }
    
    # Block access to sensitive files
    location ~ /\.(ht|git|svn) {
        deny all;
    }
    
    # Block WP-CLI access from web
    location ~ /wp-cli\.phar {
        deny all;
    }
    
    # PHP handling
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_intercept_errors on;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_read_timeout 300;
    }
    
    # Static file caching
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg|woff2|woff|ttf|eot)$ {
        expires 365d;
        add_header Cache-Control "public, max-age=31536000";
        access_log off;
    }
    
    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1000;
    gzip_proxied any;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript image/svg+xml;
    gzip_disable "msie6";
}
```

## Real-World Use Cases

### Site Migration

Migrating a WordPress site to a new domain:

```bash
# Export database from old site
wp db export old-site-backup.sql --path=/path/to/old-site

# Import database to new site
wp db import old-site-backup.sql --path=/path/to/new-site

# Update site URL
wp option update home 'https://newdomain.com' --path=/path/to/new-site
wp option update siteurl 'https://newdomain.com' --path=/path/to/new-site

# Update database URLs
wp search-replace 'olddomain.com' 'newdomain.com' --all-tables --path=/path/to/new-site

# Update user passwords if needed
wp user reset-password admin --path=/path/to/new-site

# Flush rewrite rules
wp rewrite flush --path=/path/to/new-site
```

### Maintenance Script

Scheduled maintenance script with log output:

```bash
#!/bin/bash
# maintenance.sh - Weekly maintenance script

# Set variables
SITE_PATH="/var/www/html"
LOG_FILE="/var/log/wp-maintenance-$(date +%Y%m%d).log"
ERROR_LOG="/var/log/wp-maintenance-error-$(date +%Y%m%d).log"
BACKUP_DIR="/backups/wordpress"

# Start logging
echo "=========================================" >> $LOG_FILE
echo "WordPress Maintenance Started: $(date)" >> $LOG_FILE
echo "=========================================" >> $LOG_FILE

# Create database backup
echo "Creating database backup..." >> $LOG_FILE
cd $SITE_PATH
mkdir -p $BACKUP_DIR

if wp db export $BACKUP_DIR/db-backup-$(date +%Y%m%d).sql >> $LOG_FILE 2>> $ERROR_LOG; then
    echo "✅ Database backup successful" >> $LOG_FILE
else
    echo "❌ Database backup failed" >> $LOG_FILE
fi

# Update WordPress core
echo "Updating WordPress core..." >> $LOG_FILE
if wp core update >> $LOG_FILE 2>> $ERROR_LOG; then
    echo "✅ Core update successful" >> $LOG_FILE
    
    # Update database if core was updated
    if wp core update-db >> $LOG_FILE 2>> $ERROR_LOG; then
        echo "✅ Database update successful" >> $LOG_FILE
    else
        echo "❌ Database update failed" >> $LOG_LOG
    fi
else
    echo "❌ Core update failed or already up to date" >> $LOG_FILE
fi

# Update plugins
echo "Updating plugins..." >> $LOG_FILE
if wp plugin update --all >> $LOG_FILE 2>> $ERROR_LOG; then
    echo "✅ Plugin updates successful" >> $LOG_FILE
else
    echo "❌ Plugin updates failed or all plugins up to date" >> $LOG_FILE
fi

# Update themes
echo "Updating themes..." >> $LOG_FILE
if wp theme update --all >> $LOG_FILE 2>> $ERROR_LOG; then
    echo "✅ Theme updates successful" >> $LOG_FILE
else
    echo "❌ Theme updates failed or all themes up to date" >> $LOG_FILE
fi

# Clean up database
echo "Cleaning up database..." >> $LOG_FILE
if wp db optimize >> $LOG_FILE 2>> $ERROR_LOG; then
    echo "✅ Database optimization successful" >> $LOG_FILE
else
    echo "❌ Database optimization failed" >> $LOG_FILE
fi

# Delete spam comments
echo "Deleting spam comments..." >> $LOG_FILE
SPAM_COUNT=$(wp comment list --status=spam --format=count)
if [ "$SPAM_COUNT" -gt 0 ]; then
    if wp comment delete $(wp comment list --status=spam --format=ids) >> $LOG_FILE 2>> $ERROR_LOG; then
        echo "✅ Deleted $SPAM_COUNT spam comments" >> $LOG_FILE
    else
        echo "❌ Failed to delete spam comments" >> $LOG_FILE
    fi
else
    echo "✅ No spam comments to delete" >> $LOG_FILE
fi

# Clean up transients
echo "Cleaning up transients..." >> $LOG_FILE
if wp transient delete --all >> $LOG_FILE 2>> $ERROR_LOG; then
    echo "✅ Transients cleanup successful" >> $LOG_FILE
else
    echo "❌ Transients cleanup failed" >> $LOG_FILE
fi

# Check for plugin/theme vulnerabilities
echo "Running security check..." >> $LOG_FILE
if wp plugin list >> $LOG_FILE 2>> $ERROR_LOG; then
    echo "✅ Plugin list generated for security review" >> $LOG_FILE
fi
if wp theme list >> $LOG_FILE 2>> $ERROR_LOG; then
    echo "✅ Theme list generated for security review" >> $LOG_FILE
fi

# Verify core checksums
echo "Verifying core file integrity..." >> $LOG_FILE
if wp core verify-checksums >> $LOG_FILE 2>> $ERROR_LOG; then
    echo "✅ Core files integrity verified" >> $LOG_FILE
else
    echo "❌ Core files integrity check failed" >> $LOG_FILE
fi

# Flush cache
echo "Flushing cache..." >> $LOG_FILE
if wp cache flush >> $LOG_FILE 2>> $ERROR_LOG; then
    echo "✅ Cache flushed successfully" >> $LOG_FILE
else 
    echo "❌ Cache flush failed" >> $LOG_FILE
fi

# End logging
echo "=========================================" >> $LOG_FILE
echo "WordPress Maintenance Completed: $(date)" >> $LOG_FILE
echo "=========================================" >> $LOG_FILE

# Send email notification with logs
mail -s "WordPress Maintenance Report: $(date +%Y-%m-%d)" admin@example.com < $LOG_FILE
```

## Conclusion

WP-CLI remains an essential tool for WordPress developers and site administrators in 2025. By leveraging these advanced capabilities and following security best practices, you can significantly improve your WordPress development workflow, automate routine tasks, and maintain a healthier WordPress installation.

## Resources

- [Official WP-CLI Documentation](https://wp-cli.org/)
- [WP-CLI Command Reference](https://developer.wordpress.org/cli/commands/)
- [GitHub Repository](https://github.com/wp-cli/wp-cli)
- [Package Index](https://wp-cli.org/package-index/)
- [Community Resources](https://make.wordpress.org/cli/handbook/)