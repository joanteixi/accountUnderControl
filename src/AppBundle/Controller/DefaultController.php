<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Entry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        //preload all tags...
        $tags = $this->getDoctrine()->getRepository('AppBundle:Tag')->findAll();

        return $this->render(
            'AppBundle:Entry:index.html.twig',
            array(
                'pagination' => $pagination,
                'tags' => $tags
            )
        );
    }

    /**
     * @param Request $request
     * @Route("/createTag", name="entry.tag.add")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addTagAction(Request $request)
    {
        $entry = $this->getDoctrine()->getRepository('AppBundle:Entry')->find($request->get('id'));
        $tagManager = $this->get('fpn_tag.tag_manager');
        $tag = $tagManager->loadOrCreateTag($request->get('tag'));
        $tagManager->addTag($tag, $entry);
        $tagManager->saveTagging($entry);

        //return json...
        $response = new JsonResponse();
        $response->setData(array(
            'id' => $entry->getId(),
            'tag' => $request->get('tag')
        ));

        return $response;
    }
}
