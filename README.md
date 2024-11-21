# MageOS - Web Installer - beta release

This is a BETA release for MageOS web installer.

## How to test this module

1. Download Mage-OS with  
```shell
composer create-project --repository-url=https://repo.mage-os.org/ mage-os/project-community-edition .
```

2. Download this module with
```shell
composer config repositories.web-installer vcs https://github.com/mage-os-lab/web-installer
composer require --dev mage-os/web-installer dev-main
```

3. Open your browser to your virtual host name and you should get redirected to `/setup`
