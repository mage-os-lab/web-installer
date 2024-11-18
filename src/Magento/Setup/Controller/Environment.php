<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Controller;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Setup\Model\Cron\ReadinessCheck;
use Laminas\Mvc\Controller\AbstractActionController;

class Environment extends AbstractActionController
{
    /** @var string  */
    public const UPDATER_DIR = 'update';

    /**
     * @param \Magento\Framework\Setup\FilePermissions $permissions
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Setup\Model\CronScriptReadinessCheck $cronScriptReadinessCheck
     * @param \Magento\Setup\Model\PhpReadinessCheck $phpReadinessCheck
     */
    public function __construct(
        protected \Magento\Framework\Setup\FilePermissions $permissions,
        protected \Magento\Framework\Filesystem $filesystem,
        protected \Magento\Setup\Model\CronScriptReadinessCheck $cronScriptReadinessCheck,
        protected \Magento\Setup\Model\PhpReadinessCheck $phpReadinessCheck
    ) {
    }

    /**
     * No index action, return 404 error page
     *
     * @return \Laminas\View\Model\JsonModel
     */
    public function indexAction(): \Laminas\View\Model\JsonModel
    {
        $view = new \Laminas\View\Model\JsonModel([]);
        $view->setTemplate('/error/404.phtml');
        $this->getResponse()->setStatusCode(\Laminas\Http\Response::STATUS_CODE_404);

        return $view;
    }

    /**
     * Verifies php version
     *
     * @return \Laminas\View\Model\JsonModel
     */
    public function phpVersionAction(): \Laminas\View\Model\JsonModel
    {
        $type = $this->getRequest()->getQuery('type');
        $data = [];

        if ($type == ReadinessCheckInstaller::INSTALLER) {
            $data = $this->phpReadinessCheck->checkPhpVersion();
        } elseif ($type == ReadinessCheckUpdater::UPDATER) {
            $data = $this->getPhpChecksInfo(ReadinessCheck::KEY_PHP_VERSION_VERIFIED);
        }

        return new \Laminas\View\Model\JsonModel($data);
    }

    /**
     * Checks PHP settings
     *
     * @return \Laminas\View\Model\JsonModel
     */
    public function phpSettingsAction(): \Laminas\View\Model\JsonModel
    {
        $type = $this->getRequest()->getQuery('type');
        $data = [];

        if ($type == ReadinessCheckInstaller::INSTALLER) {
            $data = $this->phpReadinessCheck->checkPhpSettings();
        } elseif ($type == ReadinessCheckUpdater::UPDATER) {
            $data = $this->getPhpChecksInfo(ReadinessCheck::KEY_PHP_SETTINGS_VERIFIED);
        }

        return new \Laminas\View\Model\JsonModel($data);
    }

    /**
     * Verifies php verifications
     *
     * @return \Laminas\View\Model\JsonModel
     */
    public function phpExtensionsAction(): \Laminas\View\Model\JsonModel
    {
        $type = $this->getRequest()->getQuery('type');
        $data = [];

        if ($type == ReadinessCheckInstaller::INSTALLER) {
            $data = $this->phpReadinessCheck->checkPhpExtensions();
        } elseif ($type == ReadinessCheckUpdater::UPDATER) {
            $data = $this->getPhpChecksInfo(ReadinessCheck::KEY_PHP_EXTENSIONS_VERIFIED);
        }

        return new \Laminas\View\Model\JsonModel($data);
    }

    /**
     * Gets the PHP check info from Cron status file
     *
     * @param string $type
     * @return array
     */
    private function getPhpChecksInfo($type): array
    {
        $read = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR);

        try {
            $jsonData = json_decode($read->readFile(ReadinessCheck::SETUP_CRON_JOB_STATUS_FILE), true);

            if (isset($jsonData[ReadinessCheck::KEY_PHP_CHECKS])
                && isset($jsonData[ReadinessCheck::KEY_PHP_CHECKS][$type])
            ) {
                return  $jsonData[ReadinessCheck::KEY_PHP_CHECKS][$type];
            }

            return ['responseType' => ResponseTypeInterface::RESPONSE_TYPE_ERROR];
        } catch (\Exception $e) {
            return ['responseType' => ResponseTypeInterface::RESPONSE_TYPE_ERROR];
        }
    }

    /**
     * Verifies file permissions
     *
     * @return \Laminas\View\Model\JsonModel
     */
    public function filePermissionsAction(): \Laminas\View\Model\JsonModel
    {
        $responseType = ResponseTypeInterface::RESPONSE_TYPE_SUCCESS;
        $missingWritePermissionPaths = $this->permissions->getMissingWritablePathsForInstallation(true);

        $currentPaths = [];
        $requiredPaths = [];

        if ($missingWritePermissionPaths) {
            foreach ($missingWritePermissionPaths as $key => $value) {
                if (is_array($value)) {
                    $requiredPaths[] = ['path' => $key, 'missing' => $value];
                    $responseType = ResponseTypeInterface::RESPONSE_TYPE_ERROR;
                } else {
                    $requiredPaths[] = ['path' => $key];
                    $currentPaths[] = $key;
                }
            }
        }
        $data = [
            'responseType' => $responseType,
            'data' => [
                'required' => $requiredPaths,
                'current' => $currentPaths,
            ],
        ];

        return new \Laminas\View\Model\JsonModel($data);
    }

    /**
     * Verifies updater application exists
     *
     * @return \Laminas\View\Model\JsonModel
     */
    public function updaterApplicationAction(): \Laminas\View\Model\JsonModel
    {
        $responseType = ResponseTypeInterface::RESPONSE_TYPE_SUCCESS;

        if (!$this->filesystem->getDirectoryRead(DirectoryList::ROOT)->isExist(self::UPDATER_DIR)) {
            $responseType = ResponseTypeInterface::RESPONSE_TYPE_ERROR;
        }

        $data = [
            'responseType' => $responseType
        ];

        return new \Laminas\View\Model\JsonModel($data);
    }

    /**
     * Verifies Setup and Updater Cron status
     *
     * @return \Laminas\View\Model\JsonModel
     */
    public function cronScriptAction(): \Laminas\View\Model\JsonModel
    {
        $responseType = ResponseTypeInterface::RESPONSE_TYPE_SUCCESS;

        $setupCheck = $this->cronScriptReadinessCheck->checkSetup();
        $updaterCheck = $this->cronScriptReadinessCheck->checkUpdater();
        $data = [];

        if (!$setupCheck['success']) {
            $responseType = ResponseTypeInterface::RESPONSE_TYPE_ERROR;
            $data['setupErrorMessage'] = 'Error from Setup Application Cron Script:<br/>' . $setupCheck['error'];
        }

        if (!$updaterCheck['success']) {
            $responseType = ResponseTypeInterface::RESPONSE_TYPE_ERROR;
            $data['updaterErrorMessage'] = 'Error from Updater Application Cron Script:<br/>' . $updaterCheck['error'];
        }

        if (isset($setupCheck['notice'])) {
            $data['setupNoticeMessage'] = 'Notice from Setup Application Cron Script:<br/>' . $setupCheck['notice'];
        }

        if (isset($updaterCheck['notice'])) {
            $data['updaterNoticeMessage'] = 'Notice from Updater Application Cron Script:<br/>' .
                $updaterCheck['notice'];
        }

        $data['responseType'] = $responseType;

        return new \Laminas\View\Model\JsonModel($data);
    }
}
