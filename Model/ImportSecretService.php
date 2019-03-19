<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Netresearch\VaultImport\Model;

use Magento\Config\Console\Command\EmulatedAdminhtmlAreaProcessor;
use Magento\Framework\App\ScopeInterface;
use Netresearch\VaultImport\Webservice\VaultClientAdapterInterface;

/**
 * Class ImportSecretService
 *
 * @package Netresearch\VaultImport\Model
 */
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
     * @var ConfigSaveService
     */
    private $saveService;

    /**
     * ImportSecretService constructor.
     *
     * @param VaultClientAdapterInterface $vault
     * @param EmulatedAdminhtmlAreaProcessor $emulatedAreaProcessor
     * @param ConfigSaveService $saveService
     */
    public function __construct(
        VaultClientAdapterInterface $vault,
        EmulatedAdminhtmlAreaProcessor $emulatedAreaProcessor,
        ConfigSaveService $saveService
    ) {
        $this->vault = $vault;
        $this->emulatedAreaProcessor = $emulatedAreaProcessor;
        $this->saveService = $saveService;
    }

    /**
     * Import secrets from vault storage to Magento database
     *
     * @param string $vaultUri
     * @param string $vaultToken
     * @param string $secretPath
     * @param string $pathPrefix
     * @param string $scope
     * @param int|string $scopeCode
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Exception
     */
    public function importSecrets(
        $vaultUri,
        $vaultToken,
        $secretPath,
        $pathPrefix = '',
        $scope = ScopeInterface::SCOPE_DEFAULT,
        $scopeCode = 0
    ) {
        $secrets = $this->vault->fetchSecret($vaultUri, $vaultToken, $secretPath);
        $this->processSecrets($secrets, $pathPrefix, $scope, $scopeCode);
    }

    /**
     * Save all values from the secrets array in the config database.
     *
     * If there are nested arrays, this will resolve them recursively to individual values.
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
                return $this->saveService->saveConfigValue(
                    $path,
                    $value,
                    $scope,
                    $scopeCode
                );
            }
        );
    }
}
