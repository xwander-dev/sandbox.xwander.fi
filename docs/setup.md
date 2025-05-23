# WordPress Setup for xwander.fi

## Overview
- Native WordPress installation with Git-based version control
- WP-CLI for WordPress management
- Code/data separation following WP-BLUEPRINT standards
- Environments: dev, qa, prod

## Directory Structure
```
/srv/xwander-platform/xwander.fi/
├── dev/                            # Development environment
│   ├── wp-content/                 # WordPress content directory
│   │   ├── mu-plugins/             # Must-use plugins
│   │   ├── plugins/                # WordPress plugins
│   │   ├── themes/                 # WordPress themes
│   │   └── uploads/ -> /data/...   # Symlink to uploads directory
│   ├── wp-config.php               # WordPress configuration
│   └── wp-cli.yml                  # WP-CLI configuration
├── qa/                             # QA environment (similar structure)
├── prod/                           # Production environment (similar structure)
├── scripts/                        # Utility scripts
└── docs/                           # Documentation
```

Data directories (not in Git):
```
/data/xwander-platform/xwander.fi/
├── dev/uploads/                    # Development environment uploads
├── qa/uploads/                     # QA environment uploads
└── prod/uploads/                   # Production environment uploads
```

## Installation Process

### 1. Prerequisites
- PHP 8.1+
- MySQL/MariaDB 10.6+
- WP-CLI 2.8+
- Nginx or Apache
- Git

### 2. Database Setup
```bash
# Create database and user
sudo mysql -e "CREATE DATABASE IF NOT EXISTS xwander_fi_dev; 
CREATE USER IF NOT EXISTS 'xwander_fi_dev'@'localhost' IDENTIFIED BY 'password'; 
GRANT ALL PRIVILEGES ON xwander_fi_dev.* TO 'xwander_fi_dev'@'localhost'; 
FLUSH PRIVILEGES;"
```

### 3. WordPress Installation
```bash
# Directory structure
mkdir -p /srv/xwander-platform/xwander.fi/dev/wp-content/{plugins,themes,mu-plugins}
mkdir -p /data/xwander-platform/xwander.fi/dev/uploads
ln -sf /data/xwander-platform/xwander.fi/dev/uploads /srv/xwander-platform/xwander.fi/dev/wp-content/uploads

# Download WordPress
cd /srv/xwander-platform/xwander.fi/dev
wp core download

# Create configuration
wp config create --dbname=xwander_fi_dev --dbuser=xwander_fi_dev --dbpass=password --dbhost=localhost

# Install WordPress
wp core install --url=dev.xwander.fi --title="XWander Finland" --admin_user=admin --admin_password=secure_password --admin_email=admin@xwander.fi

# Configure settings
wp option update permalink_structure '/%postname%/'
wp option update blogdescription 'Adventures in Finland'
wp option update timezone_string 'Europe/Helsinki'
```

### 4. Essential Plugins
```bash
# Install and activate essential plugins
wp plugin install wordpress-seo advanced-custom-fields wp-fastest-cache --activate
```

### 5. File Permissions
```bash
# Set correct permissions
find /srv/xwander-platform/xwander.fi/dev -type d -exec chmod 755 {} \;
find /srv/xwander-platform/xwander.fi/dev -type f -exec chmod 644 {} \;
chmod 600 /srv/xwander-platform/xwander.fi/dev/wp-config.php
chmod -R 755 /data/xwander-platform/xwander.fi/dev/uploads
```

## Configuration

### WP-CLI Configuration
Create `/srv/xwander-platform/xwander.fi/dev/wp-cli.yml`:
```yaml
path: /srv/xwander-platform/xwander.fi/dev
url: https://dev.xwander.fi
```

### Web Server Configuration
Nginx configuration for `/etc/nginx/sites-available/dev.xwander.fi`:
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

    # WordPress permalinks
    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    # PHP processing
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }

    # Deny access to sensitive files
    location ~ /\.(ht|git) {
        deny all;
    }
}
```

## Access Details
- Admin URL: https://dev.xwander.fi/wp-admin
- Username: admin
- Password: [Stored in password manager]

## Maintenance Commands

### Updates
```bash
wp core update
wp plugin update --all
wp theme update --all
```

### Backups
```bash
wp db export backup-$(date +%Y%m%d).sql
```

### Database Operations
```bash
wp db optimize
wp search-replace 'old-url.com' 'new-url.com'
```