#!/bin/bash
# Migrate xwander theme to Bedrock structure

# Define paths
SOURCE_THEME="/srv/xwander-platform/xwander.fi/migration-files/extracted/wp-content/themes/xwander"
TARGET_THEME="/srv/xwander-platform/xwander.fi/dev/web/app/themes/xwander"

# Create target directory if it doesn't exist
mkdir -p "$TARGET_THEME"

# Copy theme files
echo "Copying theme files from $SOURCE_THEME to $TARGET_THEME"
cp -r "$SOURCE_THEME"/* "$TARGET_THEME"/

# Create .gitkeep file to ensure themes directory is tracked
touch "/srv/xwander-platform/xwander.fi/dev/web/app/themes/.gitkeep"

echo "✅ Theme migration completed"