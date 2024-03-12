<?php
declare(strict_types=1);

namespace Pramod\CustomerImport\Console\Command;

use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pramod\CustomerImport\Console\FileArgument;
use Symfony\Component\Console\Input\InputOption;
use Pramod\CustomerImport\Model\Import as ImportModel;

/**
 *
 */
class Import extends Command
{
    /**
     * Import constructor
     *
     */
    public function __construct(
        FileArgument $fileArgument,
        ImportModel $importModel
    ) {
        $this->fileArgument = $fileArgument;
        $this->importModel = $importModel;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("customer:import");
        $this->setDescription("Customer import using CSV, JSON file");
        $this->setDefinition($this->fileArgument->getArgumentList());
        parent::configure();
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws \Exception
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        try {
            $fileData = $input->getArguments();
            if (array_key_exists('file-type',$fileData) && $fileData['file-type'] == 'sample-csv') {
                 $this->importModel->importCsvData($fileData,$output);

            }else if (array_key_exists('file-type',$fileData) && $fileData['file-type'] == 'sample-json') {
                $this->importModel->importJsonData($fileData,$output);
            }

        } catch (LocalizedException $e) {
            $output->writeln("There is Error On Importing customer data Error::".$e->getMessage()." ::From line ::".$e->getLine());
        }
        $output->writeln("<info>Customer Imported Successfully</info>");
        return;
    }
}

