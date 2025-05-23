#!/bin/bash
# Analyze plugins for migration to Bedrock structure

# Define paths
SOURCE_PLUGINS="/srv/xwander-platform/xwander.fi/migration-files/extracted/wp-content/plugins"
TARGET_PLUGINS="/srv/xwander-platform/xwander.fi/dev/web/app/plugins"
ANALYSIS_FILE="/srv/xwander-platform/xwander.fi/plugin-analysis.md"

# Create target directory if it doesn't exist
mkdir -p "$TARGET_PLUGINS"

# Create analysis file
echo "# WordPress Plugin Analysis" > "$ANALYSIS_FILE"
echo "Generated on $(date)" >> "$ANALYSIS_FILE"
echo "" >> "$ANALYSIS_FILE"
echo "## Plugins Found" >> "$ANALYSIS_FILE"
echo "" >> "$ANALYSIS_FILE"

# Get list of plugins
plugins=$(ls "$SOURCE_PLUGINS")

# Known WordPress.org plugins that can be managed via Composer
known_plugins=(
  "advanced-custom-fields"
  "advanced-custom-fields-pro"
  "contact-form-7"
  "regenerate-thumbnails"
  "wordpress-seo"
  "akismet"
  "woocommerce"
  "elementor"
  "wp-fastest-cache"
  "wpforms-lite"
  "classic-editor"
  "tinymce-advanced"
  "wordfence"
  "easy-wp-smtp"
  "updraftplus"
  "wp-optimize"
  "redirection"
  "cookie-notice"
  "duplicate-post"
  "wp-super-cache"
  "all-in-one-seo-pack"
)

# Create composer dependency array
echo "## Composer Dependencies" >> "$ANALYSIS_FILE"
echo "" >> "$ANALYSIS_FILE"
echo "\`\`\`json" >> "$ANALYSIS_FILE"
echo "{" >> "$ANALYSIS_FILE"
echo "  \"require\": {" >> "$ANALYSIS_FILE"
echo "    \"wpackagist-plugin/advanced-custom-fields\": \"^6.0\"," >> "$ANALYSIS_FILE"
echo "    \"wpackagist-plugin/contact-form-7\": \"^5.0\"" >> "$ANALYSIS_FILE"
echo "  }" >> "$ANALYSIS_FILE"
echo "}" >> "$ANALYSIS_FILE"
echo "\`\`\`" >> "$ANALYSIS_FILE"
echo "" >> "$ANALYSIS_FILE"

# List plugins and categorize them
echo "## Plugin Categories" >> "$ANALYSIS_FILE"
echo "" >> "$ANALYSIS_FILE"
echo "### Available via Composer (wpackagist)" >> "$ANALYSIS_FILE"
echo "" >> "$ANALYSIS_FILE"

for plugin in $plugins; do
  # Skip index.php and directories that aren't plugins
  if [[ "$plugin" == "index.php" ]]; then
    continue
  fi
  
  # Check if it's a known plugin
  if [[ " ${known_plugins[@]} " =~ " ${plugin} " ]]; then
    echo "- $plugin: \`composer require wpackagist-plugin/$plugin\`" >> "$ANALYSIS_FILE"
  fi
done

echo "" >> "$ANALYSIS_FILE"
echo "### Custom/Premium Plugins (Manual Installation)" >> "$ANALYSIS_FILE"
echo "" >> "$ANALYSIS_FILE"

for plugin in $plugins; do
  # Skip index.php and directories that aren't plugins
  if [[ "$plugin" == "index.php" ]]; then
    continue
  fi
  
  # Check if it's NOT a known plugin
  if [[ ! " ${known_plugins[@]} " =~ " ${plugin} " ]]; then
    echo "- $plugin" >> "$ANALYSIS_FILE"
    
    # Copy custom plugins to Bedrock structure
    if [ -d "$SOURCE_PLUGINS/$plugin" ]; then
      echo "  - Copying to Bedrock structure" >> "$ANALYSIS_FILE"
      cp -r "$SOURCE_PLUGINS/$plugin" "$TARGET_PLUGINS/"
    fi
  fi
done

echo "" >> "$ANALYSIS_FILE"
echo "## Migration Notes" >> "$ANALYSIS_FILE"
echo "" >> "$ANALYSIS_FILE"
echo "1. For plugins available via wpackagist, add them to composer.json" >> "$ANALYSIS_FILE"
echo "2. Custom/premium plugins have been copied to the Bedrock structure" >> "$ANALYSIS_FILE"
echo "3. Remember to update plugin configuration in the database" >> "$ANALYSIS_FILE"

echo "✅ Plugin analysis completed. Results written to $ANALYSIS_FILE"