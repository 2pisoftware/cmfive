@ECHO OFF
SET BIN_TARGET=%~dp0/../vendor/instaclick/php-webdriver/bin/webunit
php "%BIN_TARGET%" %*
