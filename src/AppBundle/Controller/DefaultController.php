<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $index = $this->get('pucene_doctrine.indices.test');

        $index->index(
            ['title' => 'Die tolle Farbkombination macht diesen Salat zum absoluten Augenschmaus']
        );
        $index->index(
            ['title' => 'Das könnten die neuen Lieblingsbrownies werden: optisch ein Hingucker und geschmacklich eine Wucht']
        );
        $index->index(
            ['title' => 'Auch die Anhänger der mediterranen Küche sollen in der Kürbiszeit zu ihrem Recht kommen. Hier ein Rezept für eine Tomaten-Kürbis-Suppe']
        );

        dump($index->search('ein'));

        return $this->render(
            'default/index.html.twig',
            [
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
            ]
        );
    }
}
