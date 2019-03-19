<?php
/**
 * See LICENSE.md for license details.
 */
namespace Netresearch\VaultImport\Test\Unit;

use Magento\Config\Console\Command\EmulatedAdminhtmlAreaProcessor;
use Magento\Framework\App\ScopeInterface;
use Netresearch\VaultImport\Model\ConfigSaveService;
use Netresearch\VaultImport\Model\ImportSecretService;
use Netresearch\VaultImport\Webservice\VaultClientAdapterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ImportSecretServiceTest
 *
 * @package Netresearch\VaultImport\Test\Unit
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ImportSecretServiceTest extends TestCase
{
    /**
     * @var EmulatedAdminhtmlAreaProcessor|MockObject
     */
    private $emulatedAreaProcessorMock;

    protected function setUp()
    {
        $this->emulatedAreaProcessorMock = $this->getMockBuilder(EmulatedAdminhtmlAreaProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        /**
         * Make sure callbacks sent to the emulatedAreaProcessorMock is executed
         */
        $this->emulatedAreaProcessorMock
            ->method('process')
            ->will(self::returnCallback(function ($param) {
                $param();
            }));

        parent::setUp();
    }

    public function testVaultValuesAreProcessedCorrectly()
    {
        $vaultMock = $this->getMockBuilder(VaultClientAdapterInterface::class)
            ->getMock();
        $saveServiceMock = $this->getMockBuilder(ConfigSaveService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $subject = new ImportSecretService(
            $vaultMock,
            $this->emulatedAreaProcessorMock,
            $saveServiceMock
        );

        $secrets = [
            'key1' => 'value1',
            'key2' => [
                'nestedkey1' => 'nestedvalue1',
                'nestedkey2' => 'nestedvalue2',
            ],
            'key3' => '{"looks like json": "but is a string"}',
        ];

        $vaultMock
            ->expects(self::once())
            ->method('fetchSecret')
            ->with('testUrl', 'testToken', 'testPath')
            ->willReturn($secrets);

        $saveServiceMock
            ->expects(self::exactly(4))
            ->method('saveConfigValue');

        /**
         * Check individual config values parsed from secrets
         */
        $saveServiceMock
            ->expects(self::at(0))
            ->method('saveConfigValue')
            ->with('key1', 'value1', ScopeInterface::SCOPE_DEFAULT, 0);

        $saveServiceMock
            ->expects(self::at(1))
            ->method('saveConfigValue')
            ->with('key2/nestedkey1', 'nestedvalue1', ScopeInterface::SCOPE_DEFAULT, 0);

        $saveServiceMock
            ->expects(self::at(2))
            ->method('saveConfigValue')
            ->with('key2/nestedkey2', 'nestedvalue2', ScopeInterface::SCOPE_DEFAULT, 0);

        $saveServiceMock
            ->expects(self::at(3))
            ->method('saveConfigValue')
            ->with('key3', '{"looks like json": "but is a string"}', ScopeInterface::SCOPE_DEFAULT, 0);

        $subject->importSecrets('testUrl', 'testToken', 'testPath');
    }

    public function testPathPrefixIsCorrectlyApplied()
    {
        $vaultMock = $this->getMockBuilder(VaultClientAdapterInterface::class)
            ->getMock();
        $saveServiceMock = $this->getMockBuilder(ConfigSaveService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $subject = new ImportSecretService(
            $vaultMock,
            $this->emulatedAreaProcessorMock,
            $saveServiceMock
        );

        $secrets = [
            'key1' => 'value1',
            'key2' => [
                'nestedkey1' => 'nestedvalue1'
            ]
        ];

        $vaultMock
            ->expects(self::once())
            ->method('fetchSecret')
            ->with('testUrl', 'testToken', 'testPath')
            ->willReturn($secrets);

        $saveServiceMock
            ->expects(self::at(0))
            ->method('saveConfigValue')
            ->with('testPrefix/key1', 'value1', ScopeInterface::SCOPE_DEFAULT, 0);
        $saveServiceMock
            ->expects(self::at(1))
            ->method('saveConfigValue')
            ->with('testPrefix/key2/nestedkey1', 'nestedvalue1', ScopeInterface::SCOPE_DEFAULT, 0);

        $subject->importSecrets('testUrl', 'testToken', 'testPath', 'testPrefix');
    }
}
