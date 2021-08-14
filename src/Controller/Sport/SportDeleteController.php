<?php

namespace App\Controller\Sport;

use App\Entity\Sport;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class SportDeleteController
{
    public function __construct(
        private EntityManagerInterface $manager,
    ) {}

    /**
     * @Route(
     *     "/api/sports/{id}",
     *     name = "api_sport_delete",
     *     requirements = {"id" = "\d+"},
     *     methods = {"DELETE"}
     * )
     *
     * @OA\Response(
     *     response = 204,
     *     description = "No content"
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
     *     @OA\JsonContent(example = {"code": 405, "message": "No route found: Method Not Allowed (Allow: DELETE)"})
     * )
     * @OA\Tag(name="Sport")
     * @Security(name="Bearer")
     */
    public function __invoke(Sport $sport = null): Response
    {
        if (!$sport) {
            throw new NotFoundHttpException('The sport you are looking for does not exist');
        }

        $this->manager->remove($sport);
        $this->manager->flush();

        return new Response(
            '',
            Response::HTTP_NO_CONTENT,
            ['Content-Type' => 'application/json']
        );
    }
}
