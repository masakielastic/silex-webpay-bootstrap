# webpay-example-php

レンタルホスティングサービスで WebPay の決済を試すためのコードです。

## インストール

「Download ZIP」で入手したファイルを展開します。config.php.sample をコピーして config.php をつくり、
[WebPay のユーザ設定](https://webpay.jp/settings)のページで表示されるテスト環境用公開可能鍵とテスト環境用非公開鍵を入力します。入力作業が終わったら、FTP ソフトでアップロードします。

##  アクセス

ブラウザーで http://your-domain.com/webpay-example-php にアクセスします。

## テスト

課金されることを確かめてみましょう。テスト環境で利用可能なクレジットカードの番号の一覧およびそれ以外の入力情報は[こちら](https://webpay.jp/docs/mock_cards)のページで公開されています。フォームを投稿して、成功を示すメッセージが示されたら、[ダッシュボード](https://webpay.jp/test/dashboard)を確認しましょう。