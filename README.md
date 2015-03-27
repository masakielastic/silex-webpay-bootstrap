# webpay-php-bootstrap

WebPay の決済を利用するための PHP アプリケーションです。

## サーバーの要件

  * HTTP サーバーで SSL 通信が利用可能であること
  * PHP 5.4.0 とそれ以降 - [webpay-php](https://github.com/webpay/webpay-php)) を使うため
  * OpenSSL エクステンション - `openssl_random_pseudo_bytes` による乱数生成のため

## ダウンロード

「[release](https://github.com/masakielastic/webpay-php-bootstrap/releases)」のページから入手したファイル (`webpay-php-bootstrap.zip`) を展開します。`git` および `composer` を使う場合は次のようになります。

```bash
git clone https://github.com/masakielastic/webpay-php-bootstrap.git
cd webpay-php-bootstrap
git checkout tags/v0.1
composer update
```

## インストール

次に `app/views` フォルダーで `config.php.sample` をもとに `config.php` をつくります。[WebPay のユーザ設定](https://webpay.jp/settings)のページで表示されるテスト環境用公開可能鍵とテスト環境用非公開鍵を記入します。

次にサーバーにインストールする場合のディレクトリ構成を考えます。`web` フォルダーに入っているすべてのファイルをインターネットにアクセスできる場所に設置し、それ以外はインターネットからアクセスできない場所に設置します。

 * webpay-php-bootstrap
 * public_html/index.php, css, js, .htaccess

`.htaccess` は隠しファイルを表示する OS のオプションを指定していないと表示されないので、[こちら](https://raw.githubusercontent.com/masakielastic/webpay-php-bootstrap/master/web/.htaccess)のページをもとにテキストエディターでつくるか、コマンドラインないしターミナルの `cp` コマンドを使います。ディレクトリ構成に合わせて `index.php` の `include` で指定される `app/app.php` へのパスをサーバーの環境に合わせて修正します。

```php
include '/path/to/app/app.php';
```

編集作業が終わったら、FTP ソフトでファイルをサーバーにアップロードします。

## テスト

実際にフォームに金額とクレジットカードの情報を入力して課金されることを確かめてみましょう。テスト環境で利用可能なクレジットカードの番号の一覧およびそれ以外の入力情報は[こちら](https://webpay.jp/docs/mock_cards)のページで公開されています。フォームを投稿して、投稿が成功したことを示すメッセージを見た後で、[ダッシュボード](https://webpay.jp/test/dashboard)も確認してみましょう。

## WordPress のテンプレートを使う

[こちら](https://github.com/masakielastic/webpay-php-bootstrap-wp-view)のリポジトリからコードを入手してください。

## ライセンス

MIT とします。
