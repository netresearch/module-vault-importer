<?php
/**
 * See LICENSE.md for license details.
 */
namespace Netresearch\VaultImport\Test;

use Magento\Config\App\Config\Type\System;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\Writer;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\ObjectManager;
use Netresearch\VaultImport\Model\ConfigSaveService;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigSaveServiceTest
 *
 * @package Netresearch\VaultImport\Test
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ConfigSaveServiceTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Writer
     */
    private $configWriter;

    /**
     * @var System
     */
    private $systemConfigType;

    /**
     * Initialize Dependencies
     */
    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->scopeConfig = $this->objectManager->get(ScopeConfigInterface::class);
        $this->configWriter = $this->objectManager->create(Writer::class);
        $this->systemConfigType = $this->objectManager->get(System::class);

        parent::setUp();
    }

    /**
     * @magentoDbIsolation enabled
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testValuesAreCorrectlyStoredInMagentoConfigTable()
    {
        /** @var ConfigSaveService $subject */
        $subject = $this->objectManager->create(ConfigSaveService::class);

        $subject->saveConfigValue('testpath', 'testvalue_default', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $subject->saveConfigValue('testpath', 'testvalue_website', ScopeInterface::SCOPE_WEBSITES, 1);
        /** Invalidate the system config cache to allow reading the new values in the same request. */
        $this->systemConfigType->clean();

        $valueDefault = $this->scopeConfig->getValue(
            'testpath',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
        self::assertEquals('testvalue_default', $valueDefault);

        $valueWebsite1 = $this->scopeConfig->getValue(
            'testpath',
            ScopeInterface::SCOPE_WEBSITES,
            1
        );
        self::assertEquals('testvalue_website', $valueWebsite1);

        $valueWebsite0 = $this->scopeConfig->getValue(
            'testpath',
            ScopeInterface::SCOPE_WEBSITES,
            0
        );
        self::assertEquals('testvalue_default', $valueWebsite0);

        $valueStore1 = $this->scopeConfig->getValue(
            'testpath',
            ScopeInterface::SCOPE_STORES,
            1
        );
        self::assertEquals('testvalue_website', $valueStore1);

        $valueStore0 = $this->scopeConfig->getValue(
            'testpath',
            ScopeInterface::SCOPE_STORES,
            0
        );
        self::assertEquals('testvalue_default', $valueStore0);
    }
}
