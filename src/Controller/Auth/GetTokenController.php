<?php

namespace App\Controller\Auth;

use LogicException;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class GetTokenController
{
    /**
     * @Route(
     *     "/api/login",
     *     name = "api_auth_login",
     *     methods = {"POST"}
     * )
     *
     * @OA\Response(
     *     response = 200,
     *     description = "Returns a JWT token to authenticate the next requests",
     *     @OA\JsonContent(example = {"token": "_json_web_token_"})
     * )
     * @OA\Response(
     *     response = 400,
     *     description = "JSON data sent invalid",
     *     @OA\JsonContent(example = {"code": 400, "message": "Invalid JSON."})
     * )
     * @OA\Response(
     *     response = 401,
     *     description = "Invalid credentials",
     *     @OA\JsonContent(example = {"code": 401, "message": "Invalid credentials."})
     * )
     * @OA\Response(
     *     response = 405,
     *     description = "Method not allowed",
     *     @OA\JsonContent(example = {"code": 405, "message": "No route found: Method Not Allowed (Allow: POST)"})
     * )
     * @OA\RequestBody(
     *     description = "User credentials",
     *     @OA\JsonContent(
     *         @OA\Property(
     *             property = "username",
     *             description = "The username",
     *             type = "string",
     *             example = "_username_"
     *         ),
     *         @OA\Property(
     *             property = "password",
     *             description = "The user's password",
     *             type = "string",
     *             format = "password",
     *             example = "_password_"
     *         )
     *     )
     * )
     * @OA\Tag(name="Authentication")
     * @Security()
     */
    public function __invoke(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the login key in the firewall');
    }
}
