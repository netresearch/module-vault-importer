<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Netresearch\VaultImport\Command;

use Magento\Framework\Console\Cli;
use Netresearch\VaultImport\Model\ImportSecretService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputArgumentFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportSecrets
 *
 * @package Netresearch\VaultImport\Command
 */
class ImportSecrets extends Command
{
    const HOST_ARG = 'host';
    const PORT_ARG = 'port';
    const TOKEN_ARG = 'token';
    const PATH_PREFIX_ARG = 'path_prefix';
    const SECRET_PATH_ARG = 'secret_path';

    /**
     * @var ImportSecretService
     */
    private $secretService;

    /**
     * @var InputArgumentFactory
     */
    private $inputArgFactory;

    /**
     * ImportSecrets constructor.
     *
     * @param ImportSecretService $secretService
     * @param InputArgumentFactory $inputArgFactory
     * @param string|null $name
     */
    public function __construct(
        ImportSecretService $secretService,
        InputArgumentFactory $inputArgFactory,
        $name = null
    ) {
        $this->inputArgFactory = $inputArgFactory;
        $this->secretService = $secretService;

        parent::__construct($name);
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('vault:import');
        $this->setDescription('Imports secrets from a vault instance');
        $this->setDefinition($this->getArgumentList());

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
                $input->getArgument(self::HOST_ARG),
                $input->getArgument(self::TOKEN_ARG),
                $input->getArgument(self::SECRET_PATH_ARG),
                $input->getArgument(self::PATH_PREFIX_ARG)
            );
        } catch (\Exception $exception) {
            $output->writeln(
                'Could not fetch secrets or write config value:' . PHP_EOL .
                $exception->getMessage()
            );

            return Cli::RETURN_FAILURE;
        }

        $output->writeln(
            'Successfully imported secrets from ' .
            $input->getArgument(self::HOST_ARG) .
            '/' .
            $input->getArgument(self::SECRET_PATH_ARG)
        );

        return Cli::RETURN_SUCCESS;
    }

    /**
     * @return InputArgument[]
     */
    private function getArgumentList(): array
    {
        return [
            $this->inputArgFactory->create(
                [
                    'name' => self::HOST_ARG,
                    'mode' => InputArgument::REQUIRED,
                    'description' => 'The vault server to fetch secrets from, e.g. "https://vault.example.com"'
                ]
            ),
            $this->inputArgFactory->create(
                [
                    'name' => self::TOKEN_ARG,
                    'mode' => InputArgument::REQUIRED,
                    'description' => 'Your vault login token (e.g. "s.KvAdq5AR1yPHOqsZVabmtdBE". Obtain it by logging into vault.)'
                ]
            ),
            $this->inputArgFactory->create(
                [
                    'name' => self::SECRET_PATH_ARG,
                    'mode' => InputArgument::REQUIRED,
                    'description' => 'The storage path of the secret to fetch, with leading "/", e.g. "/secret/a/b/c"'
                ]
            ),
            $this->inputArgFactory->create(
                [
                    'name' => self::PATH_PREFIX_ARG,
                    'mode' => InputArgument::OPTIONAL,
                    'description' => 'A Magento config path that is prepended with a "/" to every secret key',
                ]
            ),
        ];
    }
}
