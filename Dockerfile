FROM php:8.2-cli
RUN docker-php-ext-install pdo_mysql mysqli bcmath
WORKDIR /app
COPY . .
EXPOSE 10000
CMD ["sh", "-c", "php -S 0.0.0.0:$PORT"]
