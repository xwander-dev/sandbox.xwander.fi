# WP-CLI for xwander.fi Management

## Configuration

### Local Configuration
File: `/srv/xwander-platform/xwander.fi/dev/wp-cli.yml`
```yaml
path: /srv/xwander-platform/xwander.fi/dev
url: https://dev.xwander.fi
```

### Environment-Specific Commands
```bash
# Development environment
cd /srv/xwander-platform/xwander.fi/dev
wp plugin list

# QA environment
cd /srv/xwander-platform/xwander.fi/qa
wp plugin list

# Production environment
cd /srv/xwander-platform/xwander.fi/prod
wp plugin list
```

## Common Commands

### Installation Management
```bash
# Update WordPress core
wp core update

# Update database after core update
wp core update-db

# Verify core file integrity
wp core verify-checksums
```

### Plugin Management
```bash
# List installed plugins
wp plugin list

# Install and activate plugin
wp plugin install wordpress-seo --activate

# Update all plugins
wp plugin update --all

# Activate/deactivate plugin
wp plugin activate advanced-custom-fields
wp plugin deactivate hello
```

### Theme Management
```bash
# List installed themes
wp theme list

# Activate theme
wp theme activate twentytwentyfive

# Update all themes
wp theme update --all
```

### Content Management
```bash
# Create a page
wp post create --post_type=page --post_title='About Us' --post_status=publish

# List pages
wp post list --post_type=page

# Update a post
wp post update 123 --post_title='New Title'

# Delete a post
wp post delete 123 --force
```

### User Management
```bash
# Create user
wp user create editor editor@xwander.fi --role=editor --user_pass=secure_password

# List users
wp user list --fields=ID,user_login,user_email,roles

# Update user
wp user update 2 --display_name='New Name'

# Reset password
wp user reset-password admin
```

### Database Operations
```bash
# Export database
wp db export /data/xwander-platform/backups/xwander_fi/dev_$(date +%Y%m%d).sql

# Import database
wp db import /path/to/database.sql

# Search and replace
wp search-replace 'old-domain.com' 'dev.xwander.fi' --all-tables

# Optimize database
wp db optimize
```

### Option Management
```bash
# Get option value
wp option get siteurl

# Update option
wp option update blogname 'XWander Finland'

# List options
wp option list --search="*mail*"
```

### File Management
```bash
# Fix permissions
wp media regenerate --yes
```

## Automated Tasks

### Daily Backup Script
```bash
#!/bin/bash
# /srv/xwander-platform/xwander.fi/scripts/backup.sh

# Database backup
cd /srv/xwander-platform/xwander.fi/dev
wp db export /data/xwander-platform/backups/xwander_fi/dev_$(date +%Y%m%d).sql

# Optimize database
wp db optimize

# Clear transients
wp transient delete --all

echo "Backup completed: $(date)"
```

### Site Deployment Script
```bash
#!/bin/bash
# /srv/xwander-platform/xwander.fi/scripts/deploy.sh

# Deploy to production
cd /srv/xwander-platform/xwander.fi/prod

# Update core and plugins
wp core update
wp core update-db
wp plugin update --all
wp theme update --all

# Clear caches
wp cache flush

echo "Deployment completed: $(date)"
```

## Security Best Practices

- Never hardcode passwords in scripts
- Use environment variables when possible
- Limit WP-CLI access to specific users
- Run potentially destructive commands with `--dry-run` first
- Regularly update WP-CLI with `wp cli update`

## Troubleshooting

### Common Issues
- Database connection error: Check wp-config.php credentials
- Permission denied: Fix file permissions
- Memory limit exceeded: Increase PHP memory limit

### Command Reference
```bash
# Get WP-CLI info
wp cli info

# Check WordPress status
wp core is-installed

# Debug a plugin
wp plugin deactivate problematic-plugin

# Run in debug mode
WP_DEBUG=true wp plugin activate plugin-name
```