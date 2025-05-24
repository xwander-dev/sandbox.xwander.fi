# Reset Script Documentation

## Overview

The `reset-sandbox-clone-from-dev.sh` script provides a one-command solution to completely reset the sandbox environment to match the current state of dev.xwander.fi. This document explains how the script works, what it does, and how to use it safely.

## Script Location

```
/srv/xwander-platform/sandbox.xwander.fi/reset-sandbox-clone-from-dev.sh
```

## What It Does

The reset script performs a complete environment refresh in 10 steps:

### Step-by-Step Process

#### 1. Safety Warning (5-second delay)
```bash
echo "⚠️  This will destroy all sandbox data! Press Ctrl+C to cancel..."
sleep 5
```
Gives users time to cancel if run accidentally.

#### 2. Self-Preservation
```bash
SCRIPT_PATH="$0"
cp "$SCRIPT_PATH" /tmp/reset-sandbox-temp.sh
```
Copies itself to /tmp to survive the Git reset.

#### 3. Git Repository Reset
```bash
git reset --hard
git clean -fdx
```
- Reverts all file changes
- Removes all untracked files
- Deletes all ignored files (including vendor/, logs, etc.)

#### 4. Code Sync from Dev
```bash
rsync -av --delete \
    --exclude='.git' \
    --exclude='reset-sandbox-clone-from-dev.sh' \
    --exclude='web/app/uploads' \
    /srv/xwander-platform/xwander.fi/dev/ ./
```
- Copies all files from dev environment
- Preserves sandbox Git history
- Keeps the reset script
- Excludes uploads (handled separately)

#### 5. Script Restoration
```bash
cp /tmp/reset-sandbox-temp.sh "$SCRIPT_PATH"
chmod +x "$SCRIPT_PATH"
rm /tmp/reset-sandbox-temp.sh
```
Restores the reset script from temporary location.

#### 6. Database Reset
```bash
mysql -u root << EOF
DROP DATABASE IF EXISTS xwander_fi_sandbox;
CREATE DATABASE xwander_fi_sandbox CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON xwander_fi_sandbox.* TO 'xwander_fi_sandbox'@'localhost';
FLUSH PRIVILEGES;
EOF
```
- Completely drops existing database
- Creates fresh database
- Re-grants permissions

#### 7. Database Clone
```bash
mysqldump -u root xwander_fi_dev | mysql -u root xwander_fi_sandbox
```
Direct pipe from dev database dump to sandbox database.

#### 8. URL Updates
```bash
cd web
wp search-replace 'dev.xwander.fi' 'sandbox.xwander.fi' \
    --path=wp \
    --skip-columns=guid \
    --report-changed-only
```
Updates all dev URLs to sandbox URLs in database.

#### 9. Uploads Sync
```bash
sudo rsync -av --delete \
    /data/xwander-platform/xwander.fi/uploads/ \
    /data/xwander-platform/xwander.fi/uploads/sandbox/ \
    --exclude=sandbox/
```
- Syncs all uploads from shared directory
- Excludes sandbox directory itself to prevent recursion

#### 10. Permissions Fix
```bash
sudo chown -R www-data:www-data /data/xwander-platform/xwander.fi/uploads/sandbox/
```
Ensures web server can access all uploads.

#### 11. Composer Dependencies
```bash
composer install --no-dev --optimize-autoloader --ignore-platform-req=php
```
Installs all PHP dependencies in production mode.

#### 12. Cache Clearing
```bash
wp cache flush --path=wp
wp rewrite flush --path=wp
```
Clears all WordPress caches and permalinks.

#### 13. Git Commit
```bash
git add -A
git commit -m "Reset sandbox from dev - $(date +%Y%m%d_%H%M%S)" || true
```
Creates a Git commit marking the reset point.

## Usage

### Basic Usage
```bash
cd /srv/xwander-platform/sandbox.xwander.fi
./reset-sandbox-clone-from-dev.sh
```

### What Gets Reset

✅ **Completely Reset:**
- All WordPress files
- Database content
- Plugin files
- Theme files
- Configuration files
- Uploads/media
- Caches

❌ **Preserved:**
- Git history
- The reset script itself
- Nginx configuration
- System configurations
- SSL certificates

### What Gets Updated

🔄 **Automatically Updated:**
- Site URLs (dev.xwander.fi → sandbox.xwander.fi)
- File permissions
- Composer dependencies
- WordPress caches

### Time Required

Typical reset takes 2-5 minutes:
- Code sync: 30 seconds
- Database reset: 10 seconds
- Database import: 30 seconds
- Uploads sync: 1-3 minutes (1.6GB)
- URL updates: 30 seconds

## Safety Features

1. **Warning Prompt**: 5-second delay to cancel
2. **Self-Preservation**: Script copies itself before reset
3. **Error Handling**: `set -euo pipefail` stops on any error
4. **Non-Destructive Git**: Preserves repository history
5. **Idempotent**: Can be run multiple times safely

## Requirements

- Root or sudo access (for database operations)
- MySQL root access (passwordless)
- At least 5GB free disk space
- dev.xwander.fi must be accessible
- All services running (MySQL, PHP-FPM)

## Troubleshooting

### Script Fails at Database Step
```bash
# Check MySQL is running
sudo systemctl status mysql

# Test root access
mysql -u root -e "SHOW DATABASES;"
```

### Permission Denied Errors
```bash
# Run with sudo if needed
sudo ./reset-sandbox-clone-from-dev.sh
```

### Uploads Sync Takes Too Long
```bash
# Check disk space
df -h /data

# Run uploads sync separately
sudo rsync -av --progress /data/xwander-platform/xwander.fi/uploads/ /data/xwander-platform/xwander.fi/uploads/sandbox/
```

### URL Replacement Fails
```bash
# Check WP-CLI works
cd web && wp --info

# Run replacement manually
wp search-replace 'dev.xwander.fi' 'sandbox.xwander.fi' --dry-run
```

## Best Practices

1. **Before Major Experiments**: Run reset to ensure clean state
2. **After Breaking Changes**: Use reset instead of debugging
3. **Weekly Resets**: Keep sandbox fresh and performant
4. **Document Findings**: Note what broke before resetting

## Extending the Script

### Add Custom Reset Steps
```bash
# Add after step 12
echo "🎯 Custom cleanup..."
rm -rf web/app/custom-experimental-plugin/
```

### Skip Uploads Sync (Faster Reset)
Comment out lines in step 9 for quick code-only resets.

### Add Notifications
```bash
# Add at end
curl -X POST https://hooks.slack.com/... -d '{"text":"Sandbox reset complete"}'
```

## Related Documentation

- [Sandbox Setup Guide](./sandbox-setup-guide.md)
- [Main Script Source](../reset-sandbox-clone-from-dev.sh)
- [Sandbox README](../SANDBOX-README.md)