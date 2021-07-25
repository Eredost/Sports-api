<?php

namespace App\Controller;

use App\Entity\Sport;
use App\Exception\ResourceValidationException;
use App\Form\SportType;
use App\Repository\SportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/sports")
 */
class SportController extends AbstractController
{
    /**
     * @Route(
     *     name="api_sport_list",
     *     methods={"GET"})
     */
    public function list(SportRepository $repository, SerializerInterface $serializer, Request $request): Response
    {
        # Pagination parameters
        $offset = (int) $request->get('offset', 0);
        $offset = (!is_int($offset) || $offset < 0 ? 0 : $offset);
        $limit = (int) $request->get('limit', 10);
        $limit = (!is_int($limit) || $limit < 1 || $limit > 50 ? 10 : $limit);

        # Filter parameters
        $order = $request->get('order', 'asc');
        $order = (!is_string($order) || !in_array(strtolower($order), ['asc', 'desc']) ? 'asc' : $order);
        $term = $request->get('term');

        $sports = $repository->getSports($offset, $limit, $order, $term);

        return new Response(
            $serializer->serialize($sports, 'json', ['groups' => 'read']),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/{id}",
     *     name="api_sport_show",
     *     requirements={"id"="\d+"},
     *     methods={"GET"})
     */
    public function show(SerializerInterface $serializer, Sport $sport = null): Response
    {
        if (!$sport) {
            throw new NotFoundHttpException('The sport you are looking for does not exist');
        }

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
     * @Route(
     *     name="api_sport_create",
     *     requirements={"id"="\d+"},
     *     methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer): Response
    {
        try {
            $body = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException('Invalid JSON in request body');
        }

        $sport = new Sport();
        $sportForm = $this->createForm(SportType::class, $sport);
        $sportForm->submit($body);

        if (!$sportForm->isValid()) {
            throw new ResourceValidationException($sportForm->getErrors(true));
        }

        $sport->setCreatedAt(new \DateTime());
        $manager->persist($sport);
        $manager->flush();

        return new Response(
            $serializer->serialize($sport, 'json'),
            Response::HTTP_CREATED,
            [
                'location' => $this->generateUrl(
                    'api_sport_show',
                    ['id' => $sport->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL),
                'Content-Type' => 'application/json',
            ]
        );
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
