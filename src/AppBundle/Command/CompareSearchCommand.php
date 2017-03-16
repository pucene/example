<?php

namespace AppBundle\Command;

use Pucene\Component\QueryBuilder\Query\FullText\Match;
use Pucene\Component\QueryBuilder\Search;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompareSearchCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:compare:search')->addArgument('query');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $puceneIndex = $this->getContainer()->get('pucene_doctrine.indices.test');
        $elasticIndex = $this->getContainer()->get('pucene_elastic.indices.test');

        $search = new Search(new Match('title', $input->getArgument('query')));

        $start = microtime(true);
        $esResult = $elasticIndex->search($search);
        $esTime = microtime(true) - $start;

        $start = microtime(true);
        $puceneResult = $puceneIndex->search($search);
        $puceneTime = microtime(true) - $start;

        $output->writeln('Elasticsearch: ' . $esTime);
        $output->writeln('Pucene:        ' . $puceneTime);

        $table = new Table($output);

        for ($i = 0; $i < 10; ++$i) {
            $table->addRow(
                [
                    count($esResult['hits']) > $i ? $esResult['hits'][$i]['_id'] : '',
                    count($esResult['hits']) > $i ? $esResult['hits'][$i]['_score'] : '',
                    count($esResult['hits']) > $i ? $esResult['hits'][$i]['_source']['title'] : '',
                    count($puceneResult['hits']) > $i ? $puceneResult['hits'][$i]['_id'] : '',
                    count($puceneResult['hits']) > $i ? $puceneResult['hits'][$i]['_score'] : '',
                    count($puceneResult['hits']) > $i ? $puceneResult['hits'][$i]['_source']['title'] : '',
                ]
            );
        }

        $table->render();
    }
}
