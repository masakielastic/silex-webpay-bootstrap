# webpay-php-bootstrap

WebPay の決済を利用するための PHP アプリケーションです。

## サーバーの要件

  * HTTP サーバーで SSL 通信が利用可能であること
  * PHP 5.4.0 とそれ以降 - [webpay-php](https://github.com/webpay/webpay-php)) を使うため
  * PHP 7.0 以前であれば OpenSSL エクステンション - `openssl_random_pseudo_bytes` による乱数生成のため

## ダウンロード

「[release](https://github.com/masakielastic/webpay-php-bootstrap/releases)」のページから入手したファイル (`webpay-php-bootstrap.zip`) を展開します。`git` および `composer` を使う場合は次のようになります。

```bash
git clone https://github.com/masakielastic/webpay-php-bootstrap.git
cd webpay-php-bootstrap
git checkout tags/v0.1
composer update
```

## インストール

まずは動作の確認を目的とした設置方法を説明します。`https://example.org/webpay-php-bootstrap/web` にアクセスできるように `webpay-php-bootstrap` フォルダーを FTP/FTPS ソフトでアップロードします。次に `app` フォルダーで `config.sample.php` をもとに `config.php` をつくります。[WebPay のユーザ設定](https://webpay.jp/settings)のページで表示されるテスト環境用公開可能鍵とテスト環境用非公開鍵を記入します。

## 動作の確認

実際にフォームに金額とクレジットカードの情報を入力して課金されることを確かめてみましょう。テスト環境で利用可能なクレジットカードの番号の一覧およびそれ以外の入力情報は[こちら](https://webpay.jp/docs/mock_cards)のページで公開されています。フォームを投稿して、投稿が成功したことを示すメッセージを見た後で、[ダッシュボード](https://webpay.jp/test/dashboard)も確認してみましょう。

## WordPress のテンプレートを使う

[こちら](https://github.com/masakielastic/webpay-php-bootstrap-wp-view)のリポジトリからコードを入手してください。

## ディレクトリのカスタマイズ

`app` をウェブからアクセスできない位置に設置した上で `web` ディレクトリーに入っている `index.php` を修正します。

```php
/path/to/app.php
```

## ライセンス

MIT とします。