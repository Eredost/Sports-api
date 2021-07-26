<?php

namespace App\Controller;

use App\Entity\Sport;
use App\Exception\ResourceValidationException;
use App\Form\SportType;
use App\Repository\SportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/sports",
 *     name="api_sport_")
 *
 * @OA\Response(
 *     response = 405,
 *     description = "Method not allowed"
 * )
 * @OA\Response(
 *     response = 401,
 *     description = "Invalid, not found or expired JWT token"
 * )
 * @OA\Tag(name="Sport")
 * @Security(name="Bearer")
 */
class SportController extends AbstractController
{
    /**
     * @Route(
     *     name="list",
     *     methods={"GET"})
     *
     * @OA\Parameter(
     *     name = "offset",
     *     in = "query",
     *     description = "The pagination offset",
     *     allowEmptyValue = true,
     *     required = false,
     *     @OA\Schema(type="int")
     * )
     * @OA\Parameter(
     *     name = "limit",
     *     in = "query",
     *     description = "Max number of sports per page",
     *     allowEmptyValue = true,
     *     required = false,
     *     @OA\Schema(type="int")
     * )
     * @OA\Parameter(
     *     name = "limit",
     *     in = "query",
     *     description = "Max number of sports per page",
     *     allowEmptyValue = true,
     *     required = false,
     *     @OA\Schema(type="int")
     * )
     * @OA\Parameter(
     *     name = "order",
     *     in = "query",
     *     description = "Sort order by sport label (asc or desc)",
     *     allowEmptyValue = true,
     *     required = false,
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name = "keyword",
     *     in = "query",
     *     description = "The label name of the sport to be searched",
     *     allowEmptyValue = true,
     *     required = false,
     *     @OA\Schema(type="string")
     * )
     * @OA\Response(
     *     response = 200,
     *     description = "Returns a list of sports",
     *     @Model(type=Sport::class, groups={"read"})
     * )
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

        $response = new Response(
            $serializer->serialize($sports, 'json', ['groups' => 'read']),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );

        return $response->setPublic()
            ->setExpires(new \DateTime('+3 minutes'))
            ->setEtag('Sports_' . $offset . $limit . $order . $term)
        ;
    }

    /**
     * @Route("/{id}",
     *     name="show",
     *     requirements={"id"="\d+"},
     *     methods={"GET"})
     *
     * @OA\Response(
     *     response = 200,
     *     description = "Returns the sport according to his id",
     *     @Model(type=Sport::class, groups={"read"})
     * )
     * @OA\Response(
     *     response = 404,
     *     description = "Sport not found"
     * )
     */
    public function show(SerializerInterface $serializer, Sport $sport = null): Response
    {
        if (!$sport) {
            throw new NotFoundHttpException('The sport you are looking for does not exist');
        }

        $response = new Response(
            $serializer->serialize($sport, 'json', ['groups' => 'read']),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );

        return $response->setPublic()
            ->setExpires(new \DateTime('+1 hour'))
            ->setLastModified($sport->getUpdatedAt())
            ->setEtag('Sport_' . $sport->getId() . $sport->getUpdatedAt()?->getTimestamp())
        ;
    }

    /**
     * @Route("/{id}",
     *     name="edit",
     *     requirements={"id"="\d+"},
     *     methods={"PUT"})
     *
     * @OA\RequestBody(
     *     description = "Sport details",
     *     @OA\MediaType(
     *         mediaType = "application/json",
     *         @OA\Schema(
     *             @OA\Property(
     *                 property = "label",
     *                 description = "The sport label",
     *                 type = "string"
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response = 200,
     *     description = "Returns the modified sport",
     *     @Model(type=Sport::class, groups={"read"})
     * )
     * @OA\Response(
     *     response = 400,
     *     description = "Malformed JSON or constraint validation errors"
     * )
     */
    public function edit(Request $request, SerializerInterface $serializer, Sport $sport = null): Response
    {
        if (!$sport) {
            throw new NotFoundHttpException('The sport you are looking for does not exist');
        }

        try {
            $body = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException('Invalid JSON in request body');
        }

        $sportForm = $this->createForm(SportType::class, $sport);
        $sportForm->submit($body);

        if (!$sportForm->isValid()) {
            throw new ResourceValidationException($sportForm->getErrors(true));
        }

        $sport->setUpdatedAt(new \DateTime());
        $manager = $this->getDoctrine()->getManager();
        $manager->flush();

        return new Response(
            $serializer->serialize($sport, 'json'),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route(
     *     name="create",
     *     requirements={"id"="\d+"},
     *     methods={"POST"})
     *
     * @OA\RequestBody(
     *     description = "Sport details",
     *     @OA\MediaType(
     *         mediaType = "application/json",
     *         @OA\Schema(
     *             @OA\Property(
     *                 property = "label",
     *                 description = "The sport label",
     *                 type = "string"
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response = 201,
     *     description = "Returns the new sport",
     *     @Model(type=Sport::class, groups={"read"})
     * )
     * @OA\Response(
     *     response = 400,
     *     description = "Malformed JSON or constraint validation errors"
     * )
     */
    public function create(Request $request, SerializerInterface $serializer): Response
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
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($sport);
        $manager->flush();

        return new Response(
            $serializer->serialize($sport, 'json'),
            Response::HTTP_CREATED,
            [
                'location' => $this->generateUrl(
                    'api_sport_show',
                    ['id' => $sport->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'Content-Type' => 'application/json',
            ]
        );
    }

    /**
     * @Route("/{id}",
     *     name="delete",
     *     requirements={"id"="\d+"},
     *     methods={"DELETE"})
     *
     * @OA\Response(
     *     response = 204,
     *     description = "No content"
     * )
     * @OA\Response(
     *     response = 404,
     *     description = "Sport not found"
     * )
     */
    public function delete(EntityManagerInterface $manager, Sport $sport = null): Response
    {
        if (!$sport) {
            throw new NotFoundHttpException('The sport you are looking for does not exist');
        }

        $manager->remove($sport);
        $manager->flush();

        return new Response(
            '',
            Response::HTTP_NO_CONTENT,
            ['Content-Type' => 'application/json']
        );
    }
}
