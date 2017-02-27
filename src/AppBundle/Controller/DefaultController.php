<?php

namespace AppBundle\Controller;

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
        $index = $this->get('pucene_doctrine.indices.test');

        return $this->render('default/list.html.twig', ['documents' => $index->search($request->get('q'))]);
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
