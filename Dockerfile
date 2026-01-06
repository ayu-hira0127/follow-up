# PHP 8.2 with FPM
FROM php:8.2-fpm

# システムパッケージの更新とLaravel用拡張機能のインストール
# apt-get update: パッケージリストを最新化
# apt-get install -y: パッケージをインストール（-yは確認なしでインストール）
RUN apt-get update && apt-get install -y \
    # バージョン管理ツール、Composerで使用
    git \
    # cURL（HTTPリクエストを送信するツール）
    curl \
    # 正規表現処理用のライブラリ（mbstring拡張機能に必要）
    libonig-dev \
    # XML処理用のライブラリ（DOM、SimpleXML拡張機能に必要）
    libxml2-dev \
    # PHP拡張機能のインストール
    # mbstring: マルチバイト文字列処理（日本語などの多バイト文字を扱う）
    # pdo_mysql: MySQLデータベースへの接続
    && docker-php-ext-install mbstring pdo_mysql \
    # パッケージキャッシュをクリーンアップ（イメージサイズを小さくする）
    && apt-get clean \
    # パッケージリストを削除（イメージサイズを小さくする）
    && rm -rf /var/lib/apt/lists/*

# Composerのインストール
# ComposerはPHPの依存関係管理ツール（Laravelのインストールに必要）
# --from=composer:latest: 別のDockerイメージ（composer:latest）からファイルをコピー
# Composerのイメージをビルドする必要がなく、効率的にインストールできる
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# PHP-FPM設定の調整（外部から接続可能にする）
# デフォルトでは、PHP-FPMはUnixソケット（ファイル経由）で通信する設定になっている
# しかし、別コンテナ（Nginx）から接続するため、TCPポート（9000番）で待ち受けるように変更
# sed -i: ファイルを直接編集するコマンド
# listen = /run/php/php8.2-fpm.sock → listen = 0.0.0.0:9000 に変更
RUN sed -i 's/listen = \/run\/php\/php8.2-fpm.sock/listen = 0.0.0.0:9000/' /usr/local/etc/php-fpm.d/www.conf

# エントリーポイントスクリプトをコピー
# コンテナ起動時に自動的に実行されるスクリプトをコピー
# このスクリプトは、Laravelのファイル権限を設定してからPHP-FPMを起動する
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
# 実行権限を付与（chmod +x: ファイルを実行可能にする）
RUN chmod +x /usr/local/bin/entrypoint.sh

# 作業ディレクトリの設定
# 以降のコマンドは、このディレクトリを基準に実行される
# Laravelアプリケーションのソースコードは、このディレクトリにマウントされる
WORKDIR /var/www/html

# ポート9000を公開（PHP-FPM）
# このポートでPHP-FPMが待ち受ける（Nginxから接続される）
# ただし、docker-compose.ymlで明示的にポートマッピングしない限り、外部からはアクセスできない
EXPOSE 9000

# エントリーポイントスクリプトを実行
# コンテナ起動時に、このスクリプトが自動的に実行される
# スクリプト内でPHP-FPMが起動されるため、コンテナはPHP-FPMプロセスとして動作し続ける
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"] 