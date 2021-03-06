<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Entry;
use AppBundle\Type\Filters\EntryFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param $request Request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        //prepare filters
        $form = $this->createForm('AppBundle\Type\Filters\EntryFilterType');
        $qb = $this->getDoctrine()->getRepository('AppBundle:Entry')->getEntriesQueryBuilder();;

        if ($request->query->has($form->getName())) {
            // manually bind values from the request
            $form->submit($request->query->get($form->getName()));

            // build the query from the given form object
            $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($form, $qb);
        }

        //get list of entries
        $query = $qb->getQuery();
        $tagManager = $this->get('fpn_tag.tag_manager');

        $paginator = $this->get('knp_paginator');
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
                'tags'       => $tags,
                'form'       => $form->createView()
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
        $tagManager->loadTagging($entry);
        $tagManager->addTag($tag, $entry);
        $tagManager->saveTagging($entry);

        //return json...
        $response = new JsonResponse();
        $response->setData(
            array(
                'id'  => $entry->getId(),
                'tag' => $request->get('tag')
            )
        );

        return $response;
    }

    /**
     * @Route("/guessTags", name="entry.tag.guess")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function guessTags()
    {
        //troba tots entry sense tag
        $tagManager = $this->get('fpn_tag.tag_manager');
        $entries = $this->getDoctrine()->getRepository('AppBundle:Entry')->getEntriesWithoutTags();
        foreach ($entries as $entry) {
            $tags = $this->getDoctrine()->getRepository('AppBundle:Entry')->matchEntryByNameWithTag($entry);
            if ($tags) {


                foreach ($tags as $tag) {
                    $entry = $this->getDoctrine()->getRepository('AppBundle:Entry')->find($entry['id']);
                    $tagManager->loadTagging($entry);
                    $tagManager->addTag($tag, $entry);
                    $tagManager->saveTagging($entry);
                }
            }
        }

        //per cara entry sense tag, busca un entry amb mateix nom
        //si té tag, assigna-li
        return $this->redirectToRoute('homepage');

    }

    /**
     * @Route("/tag/{tag}", name="entry.tag.list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listByTag(Request $request, $tag)
    {
        $query = $this->getDoctrine()->getRepository('AppBundle:Entry')->getEntriesInTag($tag);
        $tagManager = $this->get('fpn_tag.tag_manager');

        $paginator = $this->get('knp_paginator');
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
                'tags'       => $tags
            )
        );
    }
}
