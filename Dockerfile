# PHP 8.2 with Apache
FROM php:8.2-apache

# システムパッケージの更新とよく使われる拡張機能のインストール
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Apache設定の有効化
RUN a2enmod rewrite

# 作業ディレクトリの設定
WORKDIR /var/www/html

# ポート80を公開
EXPOSE 80

# Apacheをフォアグラウンドで実行
CMD ["apache2-foreground"] 