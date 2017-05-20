<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Imports data from json.
 */
class ImportJsonCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('app:import')
            ->addArgument('file', InputArgument::REQUIRED)
            ->addOption('index', null, InputOption::VALUE_REQUIRED, '', 'my_index')
            ->addOption('type', null, InputOption::VALUE_REQUIRED, '', 'my_type');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $this->getContainer()->get('pucene.client');
        $index = $client->get($input->getOption('index'));

        $content = json_decode(file_get_contents($input->getArgument('file')), true);

        $progressBar = new ProgressBar($output, count($content));
        $progressBar->setFormat('debug');

        foreach ($content as $id => $item) {
            $index->index($item, $input->getOption('type'), $id);

            $progressBar->advance();
        }

        $progressBar->finish();
    }
}
