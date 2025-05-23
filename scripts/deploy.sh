#!/bin/bash
# Deployment script for xwander.fi WordPress site

set -euo pipefail

# Check arguments
if [ $# -eq 0 ]; then
    echo "Usage: $0 [dev|qa|prod]"
    exit 1
fi

ENV=$1
SITE_NAME="xwander.fi"
REPO_DIR="/srv/xwander-platform/$SITE_NAME"
DATA_DIR="/data/xwander-platform/$SITE_NAME"

# Validate environment
if [[ ! "$ENV" =~ ^(dev|qa|prod)$ ]]; then
    echo "Error: Invalid environment. Must be dev, qa, or prod"
    exit 1
fi

echo "Deploying $SITE_NAME to $ENV environment..."

# Create data directory if it doesn't exist
mkdir -p "$DATA_DIR/$ENV/wp-content/uploads"

# Sync files from Git to runtime location
if [ "$ENV" == "prod" ]; then
    # Production deployment - rsync from Git to /data/
    rsync -av --delete \
        --exclude 'uploads/' \
        --exclude '.env' \
        --exclude '*.log' \
        "$REPO_DIR/$ENV/wp-content/" \
        "$DATA_DIR/$ENV/wp-content/"
    
    echo "Production deployment completed"
else
    # Dev/QA deployment - can be direct or via rsync
    rsync -av --delete \
        --exclude 'uploads/' \
        --exclude '.env' \
        --exclude '*.log' \
        "$REPO_DIR/$ENV/wp-content/" \
        "$DATA_DIR/$ENV/wp-content/"
    
    echo "$ENV deployment completed"
fi

# Set permissions
chown -R www-data:www-data "$DATA_DIR/$ENV/wp-content/"
chmod -R 755 "$DATA_DIR/$ENV/wp-content/"
chmod -R 775 "$DATA_DIR/$ENV/wp-content/uploads/"

echo "Deployment to $ENV completed successfully"