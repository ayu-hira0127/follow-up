# PHP 8.2 with Apache
FROM php:8.2-apache

# システムパッケージの更新とLaravel用拡張機能のインストール
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install mbstring exif pcntl bcmath gd zip 

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Apache設定の有効化とDocumentRootの変更
RUN a2enmod rewrite
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's|/var/www/|/var/www/html/|g' /etc/apache2/apache2.conf

# Laravel用AllowOverride設定を追加
RUN echo '<Directory /var/www/html/public>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    AllowOverride All' >> /etc/apache2/sites-available/000-default.conf && \
    echo '</Directory>' >> /etc/apache2/sites-available/000-default.conf

# 作業ディレクトリの設定
WORKDIR /var/www/html

# Laravelプロジェクトの作成（まだプロジェクトが存在しない場合）
RUN if [ ! -f "composer.json" ]; then \
        composer create-project laravel/laravel . --prefer-dist --no-interaction; \
    fi

# Laravel用ファイル権限の設定
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# ポート80を公開
EXPOSE 80

# Apacheをフォアグラウンドで実行
CMD ["apache2-foreground"] 