# PHP 8.2 with FPM
FROM php:8.2-fpm

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
    nginx \
    supervisor \
    && docker-php-ext-install mbstring exif pcntl bcmath gd zip pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Nginx設定ファイルをコピー
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# PHP-FPM設定の調整
RUN sed -i 's/listen = \/run\/php\/php8.2-fpm.sock/listen = 127.0.0.1:9000/' /usr/local/etc/php-fpm.d/www.conf

# Supervisor設定ファイルをコピー（NginxとPHP-FPMを同時起動）
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# エントリーポイントスクリプトをコピー
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# 作業ディレクトリの設定
WORKDIR /var/www/html

# Laravel用ファイル権限の設定（ボリュームマウント後も有効なように設定）
# 注意: 実際のファイル権限はコンテナ起動時にボリュームマウントで上書きされるため、
# 必要に応じてdocker-compose.ymlのcommandで権限設定を実行することも可能

# Nginxログディレクトリの作成
RUN mkdir -p /var/log/nginx && chown -R www-data:www-data /var/log/nginx

# ポート80を公開
EXPOSE 80

# エントリーポイントスクリプトを実行
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"] 