# Deployment Process for xwander.fi

## Environments

### Development (dev.xwander.fi)
- Active development environment
- Latest features and changes
- May contain experimental features
- Located at `/srv/xwander-platform/xwander.fi/dev`

### Quality Assurance (qa.xwander.fi)
- Testing environment
- Release candidate features
- Used for client approval
- Located at `/srv/xwander-platform/xwander.fi/qa`

### Production (xwander.fi)
- Live public-facing site
- Stable, tested features only
- Critical path monitoring
- Located at `/srv/xwander-platform/xwander.fi/prod`

## Deployment Workflow

### Dev to QA Deployment
```bash
# In development environment
cd /srv/xwander-platform/xwander.fi/dev

# Export database
wp db export /data/xwander-platform/backups/xwander_fi/dev_to_qa_$(date +%Y%m%d).sql

# In QA environment
cd /srv/xwander-platform/xwander.fi/qa

# Import database
wp db import /data/xwander-platform/backups/xwander_fi/dev_to_qa_$(date +%Y%m%d).sql

# Update URLs
wp search-replace 'dev.xwander.fi' 'qa.xwander.fi' --all-tables

# Sync uploads
rsync -avz --delete /data/xwander-platform/xwander.fi/dev/uploads/ /data/xwander-platform/xwander.fi/qa/uploads/
```

### QA to Production Deployment
```bash
# In QA environment
cd /srv/xwander-platform/xwander.fi/qa

# Export database
wp db export /data/xwander-platform/backups/xwander_fi/qa_to_prod_$(date +%Y%m%d).sql

# In production environment
cd /srv/xwander-platform/xwander.fi/prod

# Backup production database
wp db export /data/xwander-platform/backups/xwander_fi/prod_backup_$(date +%Y%m%d).sql

# Import QA database
wp db import /data/xwander-platform/backups/xwander_fi/qa_to_prod_$(date +%Y%m%d).sql

# Update URLs
wp search-replace 'qa.xwander.fi' 'xwander.fi' --all-tables

# Sync uploads
rsync -avz --delete /data/xwander-platform/xwander.fi/qa/uploads/ /data/xwander-platform/xwander.fi/prod/uploads/
```

## Code Deployment

### Git-based Deployment
```bash
# In QA/Production environment
cd /srv/xwander-platform/xwander.fi
git pull origin main

# Apply any file permission fixes
find /srv/xwander-platform/xwander.fi/prod -type d -exec chmod 755 {} \;
find /srv/xwander-platform/xwander.fi/prod -type f -exec chmod 644 {} \;
chmod 600 /srv/xwander-platform/xwander.fi/prod/wp-config.php
```

### Plugin & Theme Updates
```bash
# Update WordPress core
wp core update
wp core update-db

# Update plugins
wp plugin update --all

# Update themes
wp theme update --all

# Clear caches
wp cache flush
```

## Automated Deployment

### Deployment Script
File: `/srv/xwander-platform/xwander.fi/scripts/deploy.sh`
```bash
#!/bin/bash
# Automated deployment script for xwander.fi

# Configuration
ENVIRONMENT=$1
TIMESTAMP=$(date +%Y%m%d-%H%M%S)
BACKUP_DIR="/data/xwander-platform/backups/xwander_fi"

# Validate environment argument
if [[ ! $ENVIRONMENT =~ ^(qa|prod)$ ]]; then
    echo "Error: Invalid environment. Use 'qa' or 'prod'"
    exit 1
fi

# Set paths based on environment
if [ "$ENVIRONMENT" = "qa" ]; then
    SOURCE_PATH="/srv/xwander-platform/xwander.fi/dev"
    TARGET_PATH="/srv/xwander-platform/xwander.fi/qa"
    SOURCE_URL="dev.xwander.fi"
    TARGET_URL="qa.xwander.fi"
    SOURCE_UPLOADS="/data/xwander-platform/xwander.fi/dev/uploads"
    TARGET_UPLOADS="/data/xwander-platform/xwander.fi/qa/uploads"
else
    SOURCE_PATH="/srv/xwander-platform/xwander.fi/qa"
    TARGET_PATH="/srv/xwander-platform/xwander.fi/prod"
    SOURCE_URL="qa.xwander.fi"
    TARGET_URL="xwander.fi"
    SOURCE_UPLOADS="/data/xwander-platform/xwander.fi/qa/uploads"
    TARGET_UPLOADS="/data/xwander-platform/xwander.fi/prod/uploads"
fi

echo "===== Deploying to $ENVIRONMENT environment ====="
echo "Source: $SOURCE_PATH"
echo "Target: $TARGET_PATH"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup target database
echo "Backing up target database..."
cd $TARGET_PATH
wp db export $BACKUP_DIR/${ENVIRONMENT}_backup_${TIMESTAMP}.sql

# Export source database
echo "Exporting source database..."
cd $SOURCE_PATH
wp db export $BACKUP_DIR/source_to_${ENVIRONMENT}_${TIMESTAMP}.sql

# Import to target
echo "Importing database to target..."
cd $TARGET_PATH
wp db import $BACKUP_DIR/source_to_${ENVIRONMENT}_${TIMESTAMP}.sql

# Update URLs
echo "Updating URLs..."
cd $TARGET_PATH
wp search-replace $SOURCE_URL $TARGET_URL --all-tables

# Sync uploads
echo "Syncing uploads directory..."
rsync -avz --delete $SOURCE_UPLOADS/ $TARGET_UPLOADS/

# Update code from Git
echo "Updating code from Git..."
cd $TARGET_PATH
git pull origin main

# Fix permissions
echo "Fixing permissions..."
find $TARGET_PATH -type d -exec chmod 755 {} \;
find $TARGET_PATH -type f -exec chmod 644 {} \;
chmod 600 $TARGET_PATH/wp-config.php

# Clear caches
echo "Clearing caches..."
cd $TARGET_PATH
wp cache flush

echo "===== Deployment completed successfully ====="
```

## Rollback Procedure

### Database Rollback
```bash
# In production environment
cd /srv/xwander-platform/xwander.fi/prod

# Import backup
wp db import /data/xwander-platform/backups/xwander_fi/prod_backup_YYYYMMDD.sql
```

### Code Rollback
```bash
# In production environment
cd /srv/xwander-platform/xwander.fi
git checkout v1.2.3  # Checkout specific tag or commit
```

## Post-Deployment Verification

### Automated Checks
```bash
# Check WordPress installation
wp core verify-checksums

# Check site health
wp site health status

# Check plugin status
wp plugin status

# Check theme status
wp theme status
```

### Manual Testing Checklist
- Verify homepage loads correctly
- Test critical user flows (contact form, etc.)
- Check responsive behavior
- Verify integrations with external services
- Check admin functionality

## Security Considerations

### Pre-Deployment Security Checks
- Run vulnerability scans on plugins
- Check file permissions
- Review user accounts and roles
- Verify backup integrity

### Post-Deployment Security
- Enable security monitoring
- Verify SSL certificate is valid
- Check for exposed sensitive files
- Enable brute force protection