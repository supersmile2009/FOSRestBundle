<?php

/*
 * This file is part of the FOSRestBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\RestBundle\EventListener;

use FOS\RestBundle\FOSRestBundle;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Firewall\ExceptionListener as HttpFirewallExceptionListener;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * ExceptionListener.
 *
 * @author Ener-Getick <egetick@gmail.com>
 * @author Daniel West <daniel@silverback.is>
 *
 * @internal
 */
class ExceptionListener extends HttpFirewallExceptionListener
{
    /**
     * @var HttpKernelExceptionListener
     */
    protected $exception_listener;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthenticationTrustResolverInterface $trustResolver,
        HttpUtils $httpUtils,
        $providerKey,
        AuthenticationEntryPointInterface $authenticationEntryPoint = null,
        $errorPage = null,
        AccessDeniedHandlerInterface $accessDeniedHandler = null,
        LoggerInterface $logger = null,
        $stateless = false,
        $controller
    )
    {
        parent::__construct($tokenStorage, $trustResolver, $httpUtils, $providerKey, $authenticationEntryPoint, $errorPage, $accessDeniedHandler, $logger, $stateless);
        $this->exception_listener = new HttpKernelExceptionListener($controller, $logger);
    }

    /**
     * {@inheritdoc}
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->attributes->get(FOSRestBundle::ZONE_ATTRIBUTE, true)) {
            parent::onKernelException($event);

            return;
        }

        $this->exception_listener->onKernelException($event);
    }
}
