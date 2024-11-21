# Mage-OS - Web Installer - beta release

This is a BETA release for Mage-OS web installer.

## How to test this module

1. Download Mage-OS with  
```shell
composer create-project --repository-url=https://repo.mage-os.org/ mage-os/project-community-edition .
```
3. Download this module with
```shell
composer config repositories.web-installer vcs https://github.com/mage-os-lab/web-installer
composer require --dev mageos/web-installer dev-main
```
4. Open your browser to your virtual host name and you should get redirected to `/setup`
