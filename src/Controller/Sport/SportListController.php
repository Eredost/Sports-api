<?php

namespace App\Controller\Sport;

use App\Repository\SportRepository;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Entity\Sport;
use OpenApi\Annotations as OA;

class SportListController
{
    public function __construct(
        private SportRepository $repository,
        private SerializerInterface $serializer,
    ) {}

    /**
     * @Route(
     *     "/api/sports",
     *     name = "api_sport_list",
     *     methods = {"GET"}
     * )
     *
     * @OA\Parameter(
     *     name = "offset",
     *     in = "query",
     *     description = "The pagination offset",
     *     allowEmptyValue = true,
     *     required = false,
     *     @OA\Schema(type="int", default=0)
     * )
     * @OA\Parameter(
     *     name = "limit",
     *     in = "query",
     *     description = "Max number of sports per page",
     *     allowEmptyValue = true,
     *     required = false,
     *     @OA\Schema(type="int", default=10)
     * )
     * @OA\Parameter(
     *     name = "order",
     *     in = "query",
     *     description = "Sort order by sport label (asc or desc)",
     *     allowEmptyValue = true,
     *     required = false,
     *     @OA\Schema(type="string", default="asc")
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
     * @OA\Response(
     *     response = 401,
     *     description = "Invalid, not found or expired JWT token",
     *     @OA\JsonContent(example = {"code": 401, "message": "JWT Token not found"})
     * )
     * @OA\Response(
     *     response = 405,
     *     description = "Method not allowed",
     *     @OA\JsonContent(example = {"code": 405, "message": "No route found: Method Not Allowed (Allow: GET)"})
     * )
     * @OA\Tag(name="Sport")
     * @Security(name="Bearer")
     */
    public function __invoke(Request $request): Response
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

        $sports = $this->repository->getSports($offset, $limit, $order, $term);

        $response = new Response(
            $this->serializer->serialize($sports, 'json', ['groups' => 'read']),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );

        return $response->setPublic()
            ->setExpires(new \DateTime('+3 minutes'))
            ->setEtag('Sports_' . $offset . $limit . $order . $term)
        ;
    }
}
