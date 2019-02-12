<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Netresearch\VaultImport\Model;

use Magento\Config\Console\Command\EmulatedAdminhtmlAreaProcessor;
use Netresearch\VaultImport\Webservice\VaultClientAdapterInterface;

class ImportSecretService
{
    /**
     * @var VaultClientAdapterInterface
     */
    private $vault;

    /**
     * @var EmulatedAdminhtmlAreaProcessor
     */
    private $emulatedAreaProcessor;

    /**
     * @var ConfigSaveServiceFactory
     */
    private $saveServiceFactory;

    /**
     * ImportSecretService constructor.
     *
     * @param VaultClientAdapterInterface $vault
     * @param EmulatedAdminhtmlAreaProcessor $emulatedAreaProcessor
     * @param ConfigSaveServiceFactory $saveServiceFactory
     */
    public function __construct(
        VaultClientAdapterInterface $vault,
        EmulatedAdminhtmlAreaProcessor $emulatedAreaProcessor,
        ConfigSaveServiceFactory $saveServiceFactory
    ) {
        $this->vault = $vault;
        $this->emulatedAreaProcessor = $emulatedAreaProcessor;
        $this->saveServiceFactory = $saveServiceFactory;
    }

    /**
     * Import secrets from vault storage recursively
     *
     * @param string $vaultUri
     * @param string $vaultToken
     * @param string $secretPath
     * @param string $pathPrefix
     * @param string $scope
     * @param int|string $scopeCode
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function importSecrets(
        $vaultUri,
        $vaultToken,
        $secretPath,
        $pathPrefix = '',
        $scope = 'default',
        $scopeCode = 0
    ) {
        $secrets = $this->vault->fetchSecret($vaultUri, $vaultToken, $secretPath);
        $this->processSecrets($secrets, $pathPrefix, $scope, $scopeCode);
    }

    /**
     * Recursive processing of secrets array
     *
     * @param string[] $secrets
     * @param string $basePath
     * @param string $scope
     * @param int|string $scopeCode
     * @throws \Exception
     */
    private function processSecrets($secrets, $basePath, $scope, $scopeCode)
    {
        foreach ($secrets as $key => $secret) {
            $path = $basePath ? $basePath . '/' . $key : $key;
            if (is_array($secret)) {
                $this->processSecrets($secret, $path, $scope, $scopeCode);
            } else {
                $this->setConfigValue($path, $secret, $scope, $scopeCode);
            }
        }
    }

    /**
     * Set config value with emulated admin area
     *
     * @param string $path
     * @param string $value
     * @param string $scope
     * @param int|string $scopeCode
     * @throws \Exception
     */
    private function setConfigValue($path, $value, $scope, $scopeCode)
    {
        $this->emulatedAreaProcessor->process(
            function () use ($path, $value, $scope, $scopeCode) {

                return $this->saveServiceFactory->create()->saveConfigValue(
                    $path,
                    $value,
                    $scope,
                    $scopeCode
                );
            }
        );
    }
}
