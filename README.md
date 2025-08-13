### ⚠️ Project Archived
This Mage-OS project did not reach a stable release and has been inactive for an extended period.  
Archiving is not permanent, if you’d like to resume development, contact the Mage-OS maintainers to have it unarchived.  
🧡 Thank you to all who contributed.

----

# Mage-OS - Web Installer - beta release

This is a BETA release for Mage-OS web installer.

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
