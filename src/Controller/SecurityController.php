<?php

namespace App\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api",
 *     name="api_")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login",
     *     name="login",
     *     methods={"POST"})
     */
    public function login(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the login key in the firewall');
    }
}
