<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Symfony Framework Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2023-2024 scorpion3dd
 */

declare(strict_types=1);

namespace App\Controller;

use App\Util\LoggerTrait;
use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController - for Admin panel
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    use LoggerTrait;

    /**
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     */
    public function __construct(
        LoggerInterface $logger,
        ContainerInterface $container
    ) {
        $this->logger = $logger;
        $this->container = $container;
        $this->debugConstruct(self::class);
    }

    /**
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    #[Route(path: '/{_locale<%app.supportedLocales%>}/registration', name: 'app_registration')]
    public function registration(AuthenticationUtils $authenticationUtils): Response
    {
        $this->debugFunction(self::class, 'registration');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/registration.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    #[Route(path: '/{_locale<%app.supportedLocales%>}/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $this->debugFunction(self::class, 'login');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @return void
     */
    #[Route(path: '/{_locale<%app.supportedLocales%>}/logout', name: 'app_logout')]
    public function logout(): void
    {
        // @codeCoverageIgnoreStart
        $this->debugFunction(self::class, 'logout');
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
        // @codeCoverageIgnoreEnd
    }
}
