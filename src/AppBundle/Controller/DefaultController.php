<?php

namespace AppBundle\Controller;

use Pucene\Component\QueryBuilder\Query\FullText\Match;
use Pucene\Component\QueryBuilder\Query\MatchAll;
use Pucene\Component\QueryBuilder\Search;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="list", methods={"GET"})
     */
    public function listAction(Request $request)
    {
        $index = $this->get('pucene_doctrine.indices.test');

        $query = new MatchAll();
        if ($request->get('q')) {
            $query = new Match('title', $request->get('q'));
        }

        return $this->render('default/list.html.twig', ['documents' => $index->search(new Search($query))]);
    }

    /**
     * @Route("/", name="index", methods={"POST"})
     */
    public function indexAction(Request $request)
    {
        $index = $this->get('pucene_doctrine.indices.test');
        $index->index(
            ['title' => $request->get('title')]
        );

        return $this->redirectToRoute('list');
    }
}
