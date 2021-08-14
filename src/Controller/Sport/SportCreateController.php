<?php

namespace App\Controller\Sport;

use App\Entity\Sport;
use App\Exception\ResourceValidationException;
use App\Form\SportType;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class SportCreateController
{
    public function __construct(
        private SerializerInterface $serializer,
    ) {}

    /**
     * @Route(
     *     "/api/sports",
     *     name = "api_sport_create",
     *     requirements = {"id" = "\d+"},
     *     methods = {"POST"}
     * )
     *
     * @OA\RequestBody(
     *     description = "Sport details",
     *     required = true,
     *     @Model(type=Sport::class, groups={"write"})
     * )
     * @OA\Response(
     *     response = 201,
     *     description = "Returns the new sport",
     *     @Model(type=Sport::class, groups={"read"})
     * )
     * @OA\Response(
     *     response = 400,
     *     description = "Malformed JSON or constraint validation errors",
     *     @OA\JsonContent(example = {"code": 400, "message": "Invalid JSON in request body"})
     * )
     * @OA\Response(
     *     response = 401,
     *     description = "Invalid, not found or expired JWT token",
     *     @OA\JsonContent(example = {"code": 401, "message": "JWT Token not found"})
     * )
     * @OA\Response(
     *     response = 405,
     *     description = "Method not allowed",
     *     @OA\JsonContent(example = {"code": 405, "message": "No route found: Method Not Allowed (Allow: POST)"})
     * )
     * @OA\Tag(name="Sport")
     * @Security(name="Bearer")
     */
    public function __invoke(Request $request): Response
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
            $this->serializer->serialize($sport, 'json'),
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
}
