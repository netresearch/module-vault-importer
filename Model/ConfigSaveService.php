<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Netresearch\VaultImport\Model;

use Magento\Config\Model\PreparedValueFactory;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class ConfigSaveService
 *
 * @package Netresearch\VaultImport\Model
 */
class ConfigSaveService
{
    /**
     * @var PreparedValueFactory
     */
    private $preparedValueFactory;

    /**
     * ConfigSaveService constructor.
     *
     * @param PreparedValueFactory $preparedValueFactory
     */
    public function __construct(PreparedValueFactory $preparedValueFactory)
    {
        $this->preparedValueFactory = $preparedValueFactory;
    }

    /**
     * Store config value under given path and scope.
     *
     * Loads the backend model belonging to the config path to make sure that e.g. encrypted values are saved properly.
     *
     * @param string $path
     * @param string $value
     * @param string $scope
     * @param string|int $scopeCode
     * @return true
     * @throws CouldNotSaveException
     */
    public function saveConfigValue($path, $value, $scope, $scopeCode): bool
    {
        try {
            $backendModel = $this->preparedValueFactory->create($path, $value, $scope, $scopeCode);
            if ($backendModel instanceof Value) {
                $resourceModel = $backendModel->getResource();
                $resourceModel->save($backendModel);
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage(), $exception));
        }

        return true;
    }
}
