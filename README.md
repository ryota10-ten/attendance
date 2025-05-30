# coachtech勤怠管理アプリ

## 概要
勤怠管理アプリでユーザーごとに勤怠を日時で登録できる。
勤怠に関する修正申請をユーザーが行える。
管理者ユーザーはユーザーごとの勤怠情報の確認と勤怠情報の修正、ユーザーの修正申請の承認が行える。

## 主な機能
新規スタッフユーザーの登録
スタッフユーザーのメール認証機能
スタッフユーザーのログイン機能
スタッフユーザーログアウト機能
スタッフユーザーの勤怠登録機能
スタッフユーザーの勤怠情報の修正申請機能
スタッフユーザーの勤怠情報の一覧表示機能
管理者ユーザーのログイン機能
管理者ユーザーのログアウト機能
管理者ユーザーのスタッフユーザーごとの勤怠情報の一覧表示機能
管理者ユーザーのスタッフユーザーごとの勤怠一覧のCSVエクスポート機能
管理者ユーザーのスタッフユーザーごとの勤怠修正機能
管理者ユーザーの修正申請の承認機能

## 使用技術
laravel=8.*
php:7.4.9-fpm
mysql:8.0.26
nginx:1.21.1

## 開発環境
スタッフユーザーの会員登録ページ:http://localhost/register
スタッフユーザーのログインページ；http://localhost/login
管理者のログインページ；http://localhost/admin/login
phpmyadmin:http://localhost:8080/index.php

## セットアップ
1. リポジトリをクローン
ディレクトリ以下に、attendance.gitをクローンしてリポジトリ名をattendanceTestに変更。

git clone git@github.com:ryota10-ten/attendance.git
mv attendance attendanceTest
cd attendanceTest

2. Docker の設定
docker compose up -d --build
code .

attendancetestコンテナが作成されていれば成功です。

3. Laravel のパッケージのインストール
docker compose exec php bash
composer install

4. .env ファイルの作成
cp .env.example .env
.env.example をコピーして .env を作成。

.env ファイルを以下に修正
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

※メール送信の設定（Mailtrap）
（１）Mailtrap のアカウント作成
Mailtrap の公式サイト（https://mailtrap.io/）にアクセスし、無料アカウントを作成してください。

（２）Mailtrap の SMTP 設定を取得
Mailtrap にログイン後、Inbox を作成
Start Testing を開く

Laravel 7+ and 8.Xの設定を選択
.env に以下の情報を設定

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=no-reply@example.com

MAIL_USERNAME と MAIL_PASSWORD には Mailtrap のダッシュボードで確認できる値を入力してください。


5. アプリキーの生成
以下のコマンドを実行して、アプリケーションの暗号化キーを生成してください。
php artisan key:generate

6. マイグレーションとシーディングの実装
php artisan migrate
php artisan db:seed

5. サーバーを起動

php artisan serve
ブラウザで
http://localhost/register
にアクセスするとアプリを確認できます。

6. 機能テストの確認
MySQLコンテナからMySQLに、rootユーザでログインして、demo_testというデータベースを作成
docker compose exec mysql bash
mysql -u root -p
docker-compose.ymlファイルのMYSQL_ROOT_PASSWORD:に設定されているパスワードを記述

データベースの作成
CREATE DATABASE demo_test;

phpコンテナにログイン
docker compose exec mysql bash

テスト用の.envファイル作成
cp .env .env.testing

.env.testingファイルを以下に修正

APP_ENV=test
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_DATABASE=demo_test
DB_USERNAME=root
DB_PASSWORD=root

テスト用のアプリケーションの暗号キーを作成
php artisan key:generate --env=testing

テスト用のテーブルを作成
php artisan migrate --env=testing

テストの実行
vendor/bin/phpunit tests/Feature/









