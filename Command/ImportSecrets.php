<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Netresearch\VaultImport\Command;

use Magento\Framework\Console\Cli;
use Netresearch\VaultImport\Model\ImportSecretService;
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
     * @var ImportSecretService
     */
    private $secretService;

    /**
     * ImportSecrets constructor.
     *
     * @param ImportSecretService $secretService
     * @param string|null $name
     */
    public function __construct(ImportSecretService $secretService, $name = null)
    {
        $this->secretService = $secretService;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('vault:import');
        $this->setDescription('Imports secrets from a vault instance');
        $this->setDefinition(
            $this->getOptionsList()
        );

        parent::configure();
    }

    /**
     * Imports secrets from a specified vault storage api
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->secretService->importSecrets(
                $input->getOption(self::HOST_OPTION),
                $input->getOption(self::TOKEN_OPTION),
                $input->getOption(self::SECRET_PATH_OPTION),
                $input->getOption(self::PATH_PREFIX_OPTION)
            );
        } catch (\Exception $exception) {
            $output->writeln("Could not fetch secrets or write config value: {$exception->getMessage()}");

            return Cli::RETURN_FAILURE;
        }

        $output->writeln(
            "Successfully imported secrets from  {$input->getOption(self::HOST_OPTION)}/{$input->getOption(self::SECRET_PATH_OPTION)}"
        );

        return Cli::RETURN_SUCCESS;
    }

    /**
     * {@inheritdoc}
     *
     * @return InputOption[]
     */
    private function getOptionsList(): array
    {
        return [
            new InputOption(
                self::HOST_OPTION,
                '-u',
                InputOption::VALUE_REQUIRED,
                'The vault server to fetch secrets from.'
            ),
            new InputOption(
                self::TOKEN_OPTION,
                '-t',
                InputOption::VALUE_REQUIRED,
                'Your authentification token for your vault'
            ),
            new InputOption(
                self::SECRET_PATH_OPTION,
                '-s',
                InputOption::VALUE_REQUIRED,
                'The storage path of the secret to fetch'
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
