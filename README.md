# webpay-php-bootstrap

WebPay の決済を利用するための PHP アプリケーションです。

## サーバーの要件

  * HTTP サーバーで SSL 通信が利用可能であること
  * PHP 5.4.0 とそれ以降 - [webpay-php](https://github.com/webpay/webpay-php)) を使うため
  * OpenSSL エクステンション - `openssl_random_pseudo_bytes` による乱数生成のため

## インストール

「Download ZIP」で入手したファイルを展開します。次に config.php.sample を参考に config.php をつくります。config.php の編集にはテキストエディターを使います。[WebPay のユーザ設定](https://webpay.jp/settings)のページで表示されるテスト環境用公開可能鍵とテスト環境用非公開鍵を記入します。入力作業が終わったら、ブラウザーからアクセスできる場所に FTP ソフトでアップロードします。

## テスト

実際にフォームに金額とクレジットカードの情報を入力して課金されることを確かめてみましょう。テスト環境で利用可能なクレジットカードの番号の一覧およびそれ以外の入力情報は[こちら](https://webpay.jp/docs/mock_cards)のページで公開されています。フォームを投稿して、投稿が成功したことを示すメッセージを見た後で、[ダッシュボード](https://webpay.jp/test/dashboard)も確認してみましょう。

## WordPress のテンプレートを使う

[こちら](https://github.com/masakielastic/webpay-php-bootstrap)のリポジトリからコードを入手してください。

## ライセンス

MIT とします。
