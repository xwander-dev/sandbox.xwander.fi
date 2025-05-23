# Uploads Migration to Bedrock Structure

This document details the process of migrating uploads from a traditional WordPress installation to a Bedrock structure, following WP-BLUEPRINT standards.

## Overview

WordPress media uploads are migrated to follow WP-BLUEPRINT data separation principles:
- Code remains in Git repository (`/srv/xwander-platform/xwander.fi/`)
- Data lives outside Git in the data directory (`/data/xwander-platform/volumes/xwander.fi/`)
- Symlinks connect the two locations

## Directory Structure

```
# Bedrock app directory
/srv/xwander-platform/xwander.fi/dev/web/app/uploads
    ↓ (symlink)
# Actual data location
/data/xwander-platform/volumes/xwander.fi/dev/uploads/
```

## Migration Script

We used the `migrate-uploads.sh` script located in the `scripts/` directory to:
1. Create the target directory in `/data/xwander-platform/volumes/xwander.fi/dev/uploads/`
2. Create a symlink from the Bedrock directory to the data directory
3. Copy all files from the source directory using rsync
4. Set proper permissions (755) on the uploads directory

## Migration Results

- Total files migrated: 6,990 files
- Total data size: 1.63 GB
- Source: `migration-files/extracted/wp-content/uploads/`
- Target: `/data/xwander-platform/volumes/xwander.fi/dev/uploads/`
- Bedrock link: `/srv/xwander-platform/xwander.fi/dev/web/app/uploads -> /data/xwander-platform/volumes/xwander.fi/dev/uploads`

## Advantages of This Approach

1. **Data Separation**: Keeps large binary files out of Git repository
2. **Easier Backups**: Data directories can be backed up separately
3. **Better Performance**: Symlinks avoid duplicate files
4. **Environment Consistency**: Same structure across dev/qa/prod

## Next Steps

1. Configure web server to point to the Bedrock web directory
2. Verify uploads are accessible in WordPress admin
3. Test media embedding in posts
4. Configure image optimization plugins

## Related Documentation

- [WP-BLUEPRINT Database Standards](/srv/xwander-platform/docs/wp-blueprint-database.md)
- [Bedrock Documentation](https://roots.io/bedrock/docs/)