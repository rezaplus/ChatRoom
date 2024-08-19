# Default role is 'app'
ROLE=${CONTAINER_ROLE:-app}

case "$ROLE" in
  app)
    echo "Starting PHP-FPM..."
    exec php-fpm
    ;;
  queue)
    echo "Starting Queue Worker..."
    exec php artisan queue:work
    ;;
  scheduler)
    echo "Starting Scheduler..."
    exec php artisan schedule:work
    ;;
  *)
    echo "Invalid ROLE specified. Exiting..."
    exit 1
    ;;
esac