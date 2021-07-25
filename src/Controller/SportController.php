<?php

namespace App\Controller;

use App\Entity\Sport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/sports")
 */
class SportController extends AbstractController
{
    /**
     * @Route("/",
     *     name="api_sport_list",
     *     methods={"GET"})
     */
    public function list()
    {
    }

    /**
     * @Route("/{id}",
     *     name="api_sport_show",
     *     requirements={"id"="\d+"},
     *     methods={"GET"})
     */
    public function show(Sport $sport, SerializerInterface $serializer)
    {
        return new Response(
            $serializer->serialize($sport, 'json', ['groups' => 'read']),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/{id}",
     *     name="api_sport_edit",
     *     requirements={"id"="\d+"},
     *     methods={"PUT"})
     */
    public function edit()
    {
    }

    /**
     * @Route("/",
     *     name="api_sport_create",
     *     requirements={"id"="\d+"},
     *     methods={"POST"})
     */
    public function create()
    {
    }

    /**
     * @Route("/{id}",
     *     name="api_sport_delete",
     *     requirements={"id"="\d+"},
     *     methods={"DELETE"})
     */
    public function delete()
    {
    }
}
