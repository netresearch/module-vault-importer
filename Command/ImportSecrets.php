<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Netresearch\VaultImport\Command;

use Magento\Config\Console\Command\ConfigSet\ProcessorFacadeFactory;
use Magento\Config\Console\Command\EmulatedAdminhtmlAreaProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportSecrets extends Command
{
    const HOST_OPTION = 'host';
    const PORT_OPTION = 'port';
    const TOKEN_OPTION = 'token';
    const PATH_PREFIX_OPTION = 'path_prefix';
    const SECRET_PATH_OPTION = 'secret_path';

    /**
     * @var EmulatedAdminhtmlAreaProcessor
     */
    private $emulatedAreaProcessor;

    /**
     * The factory for processor facade.
     *
     * @var ProcessorFacadeFactory
     */
    private $processorFacadeFactory;

    protected function configure()
    {
        $this->setName('vault:import');
        $this->setDescription('Imports secrets from a vault instance');
        $this->setDefinition(
            $this->getOptionsList()
        );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }

    /**
     * @return InputOption[]
     */
    private function getOptionsList(): array
    {
        return [
            new InputOption(
                self::HOST_OPTION,
                '-h',
                InputOption::VALUE_REQUIRED,
                'The vault server to fetch secrets from.'
            ),
            new InputOption(
                self::TOKEN_OPTION,
                '-c',
                InputOption::VALUE_REQUIRED,
                'Your authentification token for your vault'
            ),
            new InputOption(
                self::SECRET_PATH_OPTION,
                '-c',
                InputOption::VALUE_REQUIRED,
                'The storage path of the secret to fetch'
            ),
            new InputOption(
                self::PORT_OPTION,
                '-p',
                InputOption::VALUE_OPTIONAL,
                'The port your vault server listens on.',
                '8200'
            ),
            new InputOption(
                self::PATH_PREFIX_OPTION,
                '-c',
                InputOption::VALUE_OPTIONAL,
                'A config path to prepent to the secrets keys',
                ''
            ),
        ];
    }
}
