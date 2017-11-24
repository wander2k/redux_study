docker-compose down
docker-compose build
docker-compose up -d
docker-compose ps
docker-compose exec hfjrss /bin/bash /var/www/container/init.sh
