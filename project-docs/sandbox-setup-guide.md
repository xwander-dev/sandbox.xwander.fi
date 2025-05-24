# Sandbox Environment Setup Guide

## Overview

The sandbox.xwander.fi environment is a complete WordPress installation cloned from dev.xwander.fi, designed for destructive testing and experimentation. This document details the complete setup process and architecture.

## Architecture

### Directory Structure

```
/srv/xwander-platform/sandbox.xwander.fi/
├── web/                          # Web root
│   ├── wp/                       # WordPress core (Composer managed)
│   ├── app/                      # WordPress content directory
│   │   ├── plugins/              # Plugins (Composer + manual)
│   │   ├── themes/               # Themes
│   │   └── uploads/              # → /data/.../uploads/sandbox (symlink)
│   ├── vendor/                   # Composer dependencies
│   ├── composer.json             # Composer configuration
│   ├── wp-config.php            # → /data/.../config/sandbox/wp-config.php (symlink)
│   └── wp-cli.yml               # → /data/.../config/sandbox/wp-cli.yml (symlink)
├── scripts/                      # Utility scripts
├── project-docs/                 # This documentation
└── reset-sandbox-clone-from-dev.sh  # Reset script
```

### Data Directory Structure

```
/data/xwander-platform/xwander.fi/
├── config/sandbox/               # Environment-specific configs
│   ├── wp-config.php            # WordPress configuration
│   └── wp-cli.yml               # WP-CLI configuration
└── uploads/sandbox/             # Media uploads (1.6GB copied from shared)
```

## Configuration Details

### Database
- **Name**: xwander_fi_sandbox
- **User**: xwander_fi_sandbox
- **Password**: sandbox_secure_password_2024
- **Host**: localhost
- **Charset**: utf8mb4

### URLs
- **Site URL**: https://sandbox.xwander.fi
- **WordPress URL**: https://sandbox.xwander.fi/wp
- **Content URL**: https://sandbox.xwander.fi/app

### PHP Settings (via Nginx)
- **upload_max_filesize**: 100M
- **post_max_size**: 100M
- **max_execution_time**: 300s
- **max_input_time**: 300s
- **memory_limit**: 512M

### WordPress Settings
- **WP_DEBUG**: false (can be enabled for testing)
- **WP_MEMORY_LIMIT**: 256M
- **WP_MAX_MEMORY_LIMIT**: 512M
- **File modifications**: Allowed (DISALLOW_FILE_EDIT = false)

## Setup Process

### 1. Repository Creation
```bash
# Created GitHub repository
gh repo create xwander-dev/sandbox.xwander.fi --public

# Initialized local directory
mkdir -p /srv/xwander-platform/sandbox.xwander.fi
cd /srv/xwander-platform/sandbox.xwander.fi
git init
git branch -m main
```

### 2. Initial Clone from Dev
```bash
# Copied entire dev structure (excluding .git)
rsync -av --exclude='.git' /srv/xwander-platform/xwander.fi/dev/ ./

# Removed deployment workflows (not needed for sandbox)
rm -rf .github/workflows/deploy.yml

# Created sandbox-specific .gitignore
```

### 3. Database Setup
```bash
# Created database and user
CREATE DATABASE xwander_fi_sandbox CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'xwander_fi_sandbox'@'localhost' IDENTIFIED BY 'sandbox_secure_password_2024';
GRANT ALL PRIVILEGES ON xwander_fi_sandbox.* TO 'xwander_fi_sandbox'@'localhost';

# Imported data from dev
mysqldump xwander_fi_dev | mysql xwander_fi_sandbox

# Updated URLs
wp search-replace 'dev.xwander.fi' 'sandbox.xwander.fi' --skip-columns=guid
```

### 4. Configuration Files
Created in `/data/xwander-platform/xwander.fi/config/sandbox/`:
- **wp-config.php**: WordPress configuration with absolute paths
- **wp-cli.yml**: WP-CLI settings

Symlinked to web directory:
```bash
ln -sf /data/.../config/sandbox/wp-config.php web/wp-config.php
ln -sf /data/.../config/sandbox/wp-cli.yml web/wp-cli.yml
```

### 5. Uploads Directory
```bash
# Created and populated uploads
mkdir -p /data/xwander-platform/xwander.fi/uploads/sandbox
rsync -av /data/xwander-platform/xwander.fi/uploads/ .../uploads/sandbox/

# Created symlink
ln -sf /data/.../uploads/sandbox web/app/uploads
```

### 6. Web Server Configuration
```nginx
server {
    listen 443 ssl http2;
    server_name sandbox.xwander.fi;
    root /srv/xwander-platform/sandbox.xwander.fi/web;
    
    # SSL managed by Certbot
    ssl_certificate /etc/letsencrypt/live/sandbox.xwander.fi/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/sandbox.xwander.fi/privkey.pem;
    
    # PHP processing
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }
}
```

### 7. SSL Certificate
```bash
sudo certbot --nginx -d sandbox.xwander.fi
# Certificate expires: 2025-08-21 (auto-renewal enabled)
```

## Reset Script

See [reset-script-documentation.md](./reset-script-documentation.md) for detailed information about the reset functionality.

## Maintenance

### Daily Tasks
- Monitor disk usage (uploads can grow large)
- Check error logs if issues reported

### Weekly Tasks
- Run reset script to clean up experiments
- Check for security updates (not auto-applied)

### Monthly Tasks
- Review and clean old Git commits
- Update documentation with new findings

## Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   sudo chown -R www-data:www-data /data/xwander-platform/xwander.fi/uploads/sandbox/
   ```

2. **Database Connection Failed**
   - Check credentials in wp-config.php
   - Verify MySQL service is running
   - Test with: `mysql -u xwander_fi_sandbox -p`

3. **404 Errors**
   - Check Nginx is running: `sudo systemctl status nginx`
   - Verify root directory in Nginx config
   - Check .htaccess isn't interfering

4. **White Screen of Death**
   - Enable debug in wp-config.php temporarily
   - Check PHP error logs: `/var/log/php8.1-fpm.log`
   - Verify all symlinks are valid

## Security Notes

⚠️ **This is an experimental environment with relaxed security:**
- Debug can be enabled
- File modifications allowed
- No automated backups
- Shared API keys from dev

**Never use for:**
- Production data
- Client testing
- Security testing
- Performance benchmarks

## Related Documentation

- [Reset Script Documentation](./reset-script-documentation.md)
- [Main Sandbox README](../SANDBOX-README.md)
- [Setup Plan with Checklist](../../docs/sandbox-setup-plan.md)