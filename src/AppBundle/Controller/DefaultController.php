<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Entry;
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
        //get list of entries
        $query = $this->getDoctrine()->getRepository('AppBundle:Entry')->getEntries();
        $tagManager = $this->get('fpn_tag.tag_manager');

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            200/*limit per page*/
        );

        // TODO FIX THIS!!! LOT OF QUERIES!
        foreach ($pagination as $entry) {
            $tagManager->loadTagging($entry);
        }

        return $this->render(
            'AppBundle:Entry:index.html.twig',
            array(
                'pagination' => $pagination
            )
        );
    }

    /**
     * @param Request $request
     * @param Entry $entry
     * @Route("/createTag/{entry}", name="entry.tag.add")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addTagAction(Request $request, Entry $entry)
    {
        $tagManager = $this->get('fpn_tag.tag_manager');
        $fooTag = $tagManager->loadOrCreateTag('foo');
        $tagManager->addTag($fooTag, $entry);
        $tagManager->saveTagging($entry);

        return $this->redirect($this->generateUrl('homepage'));
    }
}
