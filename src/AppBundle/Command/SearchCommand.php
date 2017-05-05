<?php

namespace AppBundle\Command;

use Pucene\Component\QueryBuilder\Query\FullText\MatchQuery;
use Pucene\Component\QueryBuilder\Search;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SearchCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:search')
            ->addArgument('index')
            ->addArgument('query');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $this->getContainer()->get('pucene.client');
        $index = $client->get($input->getArgument('index'));

        $search = new Search(new MatchQuery('title', $input->getArgument('query')));

        $start = microtime(true);
        $result = $index->search($search, 'my_type');
        $time = microtime(true) - $start;

        $output->writeln('Elapsed time: ' . $time);

        $table = new Table($output);

        $hits = $result['hits'];
        for ($i = 0, $length = count($hits); $i < $length; ++$i) {
            $table->addRow(
                [
                    $hits[$i]['_id'],
                    $hits[$i]['_score'],
                    $hits[$i]['_source']['title'],
                ]
            );
        }

        $table->render();
    }
}
