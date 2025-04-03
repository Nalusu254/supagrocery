#!/bin/bash

# Exit on error
set -e

# Configuration
DEPLOY_PATH="/var/www/supagrocery"
BACKUP_PATH="/var/www/backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${GREEN}Starting deployment process...${NC}"

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_PATH"

# Backup current version if it exists
if [ -d "$DEPLOY_PATH" ]; then
    echo "Creating backup of current version..."
    tar -czf "$BACKUP_PATH/backup_$TIMESTAMP.tar.gz" -C "$DEPLOY_PATH" .
fi

# Create or clean deployment directory
mkdir -p "$DEPLOY_PATH"

# Copy files to deployment directory
echo "Copying files to deployment directory..."
rsync -av --exclude={'.git*','*.log','node_modules','vendor','deploy.sh','tests'} ./ "$DEPLOY_PATH/"

# Set permissions
echo "Setting file permissions..."
find "$DEPLOY_PATH" -type f -exec chmod 644 {} \;
find "$DEPLOY_PATH" -type d -exec chmod 755 {} \;

# Set specific permissions for writable directories
chmod -R 775 "$DEPLOY_PATH/assets/uploads"
chmod -R 775 "$DEPLOY_PATH/logs"

# Set ownership
chown -R www-data:www-data "$DEPLOY_PATH"

# Create environment file if it doesn't exist
if [ ! -f "$DEPLOY_PATH/.env" ]; then
    echo "Creating .env file..."
    cat > "$DEPLOY_PATH/.env" << EOF
APP_ENV=production
DB_HOST=localhost
DB_NAME=grocery_db
DB_USER=your_db_user
DB_PASS=your_db_password
EOF
fi

# Install dependencies if composer.json exists
if [ -f "$DEPLOY_PATH/composer.json" ]; then
    echo "Installing PHP dependencies..."
    cd "$DEPLOY_PATH"
    composer install --no-dev --optimize-autoloader
fi

# Clear cache
echo "Clearing cache..."
rm -rf "$DEPLOY_PATH/cache/*"

# Run database migrations if they exist
if [ -f "$DEPLOY_PATH/database/migrate.php" ]; then
    echo "Running database migrations..."
    php "$DEPLOY_PATH/database/migrate.php"
fi

echo -e "${GREEN}Deployment completed successfully!${NC}"

# Post-deployment checklist
echo -e "\n${GREEN}Post-deployment checklist:${NC}"
echo "1. Verify database connection"
echo "2. Check file permissions"
echo "3. Test user authentication"
echo "4. Verify image uploads"
echo "5. Check error logging"
echo "6. Test payment integration"
echo "7. Verify email functionality"
echo "8. Check SSL certificate"
echo "9. Test backup system"
echo "10. Monitor error logs" 