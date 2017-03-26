<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wikibase\JsonDumpReader\JsonDumpFactory;

class ImportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('app:import')
            ->addArgument('file', InputArgument::REQUIRED)
            ->addOption('count', null, InputOption::VALUE_REQUIRED, '', 1000);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $factory = new JsonDumpFactory();
        $dumpReader = $factory->newBz2DumpReader($input->getArgument('file'));

        $puceneIndex = $this->getContainer()->get('pucene_doctrine.indices.test');
        $elasticIndex = $this->getContainer()->get('pucene_elastic.indices.test');

        $count = $input->getOption('count');
        $progressBar = new ProgressBar($output, $count);
        $progressBar->setFormat('debug');
        for ($i = 0; $i < $count; ++$i) {
            $item = json_decode($dumpReader->nextJsonLine(), true);

            if (!array_key_exists('en', $item['labels']) || !array_key_exists('en', $item['descriptions'])) {
                $progressBar->advance();

                continue;
            }

            $document = [
                'title' => $item['labels']['en']['value'],
                'description' => $item['descriptions']['en']['value'],
            ];

            $elasticIndex->index($document, $item['id']);
            $puceneIndex->index($document, $item['id']);

            $progressBar->advance();
        }

        $progressBar->finish();
    }
}
