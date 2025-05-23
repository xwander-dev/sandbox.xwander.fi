#!/bin/bash
# xwander.fi Uploads Migration Script following WP-BLUEPRINT standards

# Exit on error
set -e

# Default values
DB_ENV=${1:-"dev"}  # Default to dev if not provided
SOURCE_DIR=${2:-"/srv/xwander-platform/xwander.fi/migration-files/extracted/wp-content/uploads"}
TARGET_DIR="/data/xwander-platform/volumes/xwander.fi/${DB_ENV}/uploads"
BEDROCK_DIR="/srv/xwander-platform/xwander.fi/dev/web/app/uploads"

# Print banner
echo "============================================="
echo "Uploads Migration for xwander.fi - ${DB_ENV} Environment"
echo "Following WP-BLUEPRINT Standards"
echo "============================================="

# Check if source directory exists
if [ ! -d "$SOURCE_DIR" ]; then
    echo "⚠️  Source directory not found: $SOURCE_DIR"
    exit 1
fi

# Create target directory
mkdir -p "$TARGET_DIR"
echo "✅ Target directory created: $TARGET_DIR"

# Create symlink to data directory from Bedrock web directory
if [ -L "$BEDROCK_DIR" ]; then
    echo "ℹ️  Removing existing symlink: $BEDROCK_DIR"
    rm "$BEDROCK_DIR"
elif [ -d "$BEDROCK_DIR" ]; then
    echo "⚠️  $BEDROCK_DIR exists as a directory. Moving contents to $TARGET_DIR"
    # Move existing content
    rsync -av "$BEDROCK_DIR/" "$TARGET_DIR/"
    rm -rf "$BEDROCK_DIR"
fi

# Create symlink from Bedrock uploads directory to data directory
mkdir -p "$(dirname "$BEDROCK_DIR")"
ln -sf "$TARGET_DIR" "$BEDROCK_DIR"
echo "✅ Created symlink: $BEDROCK_DIR -> $TARGET_DIR"

# Copy uploads with progress
echo "Migrating uploads from $SOURCE_DIR to $TARGET_DIR"
echo "This may take a while for large sites..."

# Count files for progress tracking
total_files=$(find "$SOURCE_DIR" -type f | wc -l)
echo "Total files to copy: $total_files"

# Use rsync for fast copying with progress
rsync -av --info=progress2 "$SOURCE_DIR/" "$TARGET_DIR/"

# Set permissions
chmod -R 755 "$TARGET_DIR"
echo "✅ Set permissions on $TARGET_DIR"

# Success message
echo ""
echo "============================================="
echo "✅ Uploads migration completed successfully!"
echo "============================================="
echo "Source: $SOURCE_DIR"
echo "Target: $TARGET_DIR"
echo "Symlink: $BEDROCK_DIR -> $TARGET_DIR"
echo ""
echo "Next steps:"
echo "1. Configure web server to point to /srv/xwander-platform/xwander.fi/dev/web"
echo "2. Verify uploads are accessible in WordPress"
echo "============================================="