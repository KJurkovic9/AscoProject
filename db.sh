docker run -d --name asco-db \
  -e MYSQL_ROOT_PASSWORD=rootroot \
  -e MYSQL_DATABASE=asco \
  -e MYSQL_USER=admin \
  -e MYSQL_PASSWORD=admin \
  -v $(pwd)/data:/var/lib/mysql \
  -p 3306:3306 \
  mysql:latest