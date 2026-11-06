FROM php:8.2-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpng-dev \
    && docker-php-ext-install pdo_mysql mysqli bcmath gd \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . .

# Fail the build early if QR dependencies or PNG generation are unavailable.
# Uses dummy data only; does not load bot config or create any accounts.
RUN php -r 'require "/app/vendor/autoload.php"; $qr = new \Endroid\QrCode\Builder\Builder(writer: new \Endroid\QrCode\Writer\PngWriter(), data: "qr-build-check"); $png = $qr->build()->getString(); if (substr($png, 0, 8) !== "\x89PNG\r\n\x1a\n") { throw new \RuntimeException("QR PNG check failed"); } echo "QR PNG build check: OK\n";'

EXPOSE 10000
CMD ["sh", "-c", "php -S 0.0.0.0:$PORT index.php"]
