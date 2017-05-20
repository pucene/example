<?php

namespace AppBundle\Controller;

use Pucene\Component\QueryBuilder\Query\FullText\MatchQuery;
use Pucene\Component\QueryBuilder\Query\MatchAllQuery;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\DocumentLike;
use Pucene\Component\QueryBuilder\Query\Specialized\MoreLikeThis\MoreLikeThisQuery;
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
     * @Route("/", name="create", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        $client = $this->get('pucene.client');
        $index = $client->get('my_index');

        $index->index(
            [
                'title' => $request->request->get('title'),
                'description' => $request->request->get('description'),
            ],
            'my_type'
        );

        return $this->redirectToRoute('list');
    }

    /**
     * @Route("/{id}", name="details", methods={"GET"})
     */
    public function detailsAction($id)
    {
        $client = $this->get('pucene.client');
        $index = $client->get('my_index');

        $document = $index->get('my_type', $id);
        $query = new MoreLikeThisQuery([new DocumentLike('my_index', 'my_type', $id)], ['title', 'description']);
        $query->setMinTermFreq(1);
        $query->setMinDocFreq(1);

        return $this->render(
            'default/details.html.twig',
            ['document' => $document['_source'], 'documents' => $index->search(new Search($query), 'my_type')]
        );
    }
}
