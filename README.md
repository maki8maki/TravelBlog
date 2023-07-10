# TravelBlog

## To Do

* 投稿後の編集・削除
* デザイン
* 投稿順の表示
* 複数の写真のアップロード

## 使い方

* ./bin/set-env-ugid.shを実行し，.devcontainer/.envに情報を記入する（最初のみ）
* コンテナの起動（vscodeのdevcontainerやターミナル）を行う
* index.phpへのアクセス
  * [http://localhost:80](http://localhost:80)にアクセスする
* phpMyAdminへのアクセス
  * [http://localhost:4040](http://localhost:4040)にアクセスする

## 起動の注意点

* （再掲）実行環境で初めてdocker-composeを行う場合は，./bin/set-env-ugid.shを実行し，.devcontainer/.envに情報を記入する
* データベースを削除したいとき（docker-compose.ymlを変更したときなどを含む）は，./bin/reset-container.shを実行する
  * ターミナルから行うとき
