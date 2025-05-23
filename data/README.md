# Data Directory for xwander.fi

This directory contains placeholder directories that should be symlinked to the actual data directories in `/data/xwander-platform/`.

## Important Note

According to WP-BLUEPRINT standards, all persistent data should be stored in `/data/` and not in Git-tracked directories.

## Required Setup

The actual data directories should be located at:

```
/data/xwander-platform/databases/xwander_fi/     # MariaDB data
/data/xwander-platform/volumes/xwander.fi/       # Uploads and other runtime data
/data/xwander-platform/backups/xwander_fi/       # Database backups
```

## Creating the Real Directories

```bash
sudo mkdir -p /data/xwander-platform/databases/xwander_fi
sudo mkdir -p /data/xwander-platform/volumes/xwander.fi/{dev,qa,prod}/uploads
sudo mkdir -p /data/xwander-platform/backups/xwander_fi
```

## Setting Permissions

```bash
sudo chown -R www-data:www-data /data/xwander-platform/volumes/xwander.fi
sudo chmod -R 755 /data/xwander-platform/volumes/xwander.fi
```

## Creating Symlinks

If needed, you can create symlinks from the Bedrock structure to the data directories:

```bash
# For uploads directory
ln -sf /data/xwander-platform/volumes/xwander.fi/dev/uploads /srv/xwander-platform/xwander.fi/dev/web/app/uploads
```

The database configuration script (`create-db.sh`) will handle creating these directories automatically.