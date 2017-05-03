<?php

namespace AppBundle\Controller;

use Pucene\Component\QueryBuilder\Query\FullText\MatchQuery;
use Pucene\Component\QueryBuilder\Query\MatchAllQuery;
use Pucene\Component\QueryBuilder\Search;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="list", methods={"GET"})
     */
    public function listAction(Request $request)
    {
        $client = $this->get('pucene.client');
        $index = $client->get('my_index');

        $query = new MatchAllQuery();
        if ($request->get('q')) {
            $query = new MatchQuery('title', $request->get('q'));
        }

        return $this->render('default/list.html.twig', ['documents' => $index->search(new Search($query), 'my_type')]);
    }

    /**
     * @Route("/", name="index", methods={"POST"})
     */
    public function indexAction(Request $request)
    {
        $client = $this->get('pucene.client');
        $index = $client->get('my_index');

        $index->index(
            ['title' => $request->get('title')],
            'my_type'
        );

        return $this->redirectToRoute('list');
    }
}
