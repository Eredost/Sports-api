<?php

namespace App\Controller;

use App\Entity\Sport;
use App\Exception\ResourceValidationException;
use App\Form\SportType;
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
