<?php

namespace App\Controller\Sport;

use App\Entity\Sport;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class SportShowController
{
    public function __construct(
        private SerializerInterface $serializer,
    ) {}

    /**
     * @Route(
     *     "/api/sports/{id}",
     *     name = "api_sport_show",
     *     requirements = {"id" = "\d+"},
     *     methods = {"GET"}
     * )
     *
     * @OA\Response(
     *     response = 200,
     *     description = "Returns the sport according to his id",
     *     @Model(type=Sport::class, groups={"read"})
     * )
     * @OA\Response(
     *     response = 401,
     *     description = "Invalid, not found or expired JWT token",
     *     @OA\JsonContent(example = {"code": 401, "message": "JWT Token not found"})
     * )
     * @OA\Response(
     *     response = 404,
     *     description = "Sport not found",
     *     @OA\JsonContent(example = {"code": 404, "message": "The sport you are looking for does not exist"})
     * )
     * @OA\Response(
     *     response = 405,
     *     description = "Method not allowed",
     *     @OA\JsonContent(example = {"code": 405, "message": "No route found: Method Not Allowed (Allow: GET)"})
     * )
     * @OA\Tag(name="Sport")
     * @Security(name="Bearer")
     */
    public function __invoke(Sport $sport = null): Response
    {
        if (!$sport) {
            throw new NotFoundHttpException('The sport you are looking for does not exist');
        }

        $response = new Response(
            $this->serializer->serialize($sport, 'json', ['groups' => 'read']),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );

        return $response->setPublic()
            ->setExpires(new \DateTime('+1 hour'))
            ->setLastModified($sport->getUpdatedAt())
            ->setEtag('Sport_' . $sport->getId() . $sport->getUpdatedAt()?->getTimestamp())
        ;
    }
}
