<?php

namespace App\Controller;

use LogicException;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api",
 *     name="api_")
 *
 * @OA\Response(
 *     response = 405,
 *     description = "Method not allowed"
 * )
 * @OA\Tag(name="Authentication")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login",
     *     name="login",
     *     methods={"POST"})
     *
     * @OA\Response(
     *     response = 200,
     *     description = "Returns a JWT token to authenticate the next requests"
     * )
     * @OA\Response(
     *     response = 400,
     *     description = "JSON data sent invalid "
     * )
     * @OA\Response(
     *     response = 401,
     *     description = "Invalid credentials"
     * )
     * @OA\RequestBody(
     *     description = "User credentials",
     *     @OA\MediaType(
     *         mediaType = "application/json",
     *         @OA\Schema(
     *             @OA\Property(
     *                 property = "username",
     *                 description = "The username",
     *                 type = "string"
     *             ),
     *             @OA\Property(
     *                 property = "password",
     *                 description = "The user's password",
     *                 type = "string",
     *                 format = "password"
     *             )
     *         )
     *     )
     * )
     * @Security()
     */
    public function login(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the login key in the firewall');
    }
}
