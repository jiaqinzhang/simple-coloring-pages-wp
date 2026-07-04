#!/bin/bash
# Bootstraps a fresh WordPress container: installs WP core config, activates
# the theme, and sets pretty permalinks (required for /coloring-pages/... and
# /category/... URLs used by the theme).
set -e

CONTAINER=wordpress-site-wordpress-1

docker exec "$CONTAINER" bash -c '
  if ! command -v wp >/dev/null 2>&1; then
    curl -s -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
    chmod +x /usr/local/bin/wp
  fi
'

# Wait for DB to accept connections via wp-cli itself.
for i in $(seq 1 30); do
  if docker exec "$CONTAINER" wp --allow-root core is-installed 2>/dev/null; then
    break
  fi
  if docker exec "$CONTAINER" wp --allow-root core install \
    --url="http://localhost:8080" \
    --title="Simple Coloring Pages" \
    --admin_user=admin \
    --admin_password=admin \
    --admin_email=admin@example.com \
    --skip-email 2>/dev/null; then
    break
  fi
  sleep 2
done

docker exec "$CONTAINER" wp --allow-root theme activate simple-coloring-pages
docker exec "$CONTAINER" wp --allow-root rewrite structure '/%postname%/'
docker exec "$CONTAINER" wp --allow-root rewrite flush --hard

echo "WordPress ready at http://localhost:8080  (admin / admin)"
