<?php

namespace App\Service\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class UserAuthenticator extends AbstractAuthenticator
{
    public const LOGIN_ROUTE = 'api_security_login';

    public function __construct(
        private readonly UserRepository       $userRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = json_decode($request->getContent(), true) ?? [];
        try {
            $this->userRepository->findOneBy(['login' => $credentials['login']]);
        } catch(\Exception $e) {
            echo $e->getMessage();
        }

        return new Passport(
            new UserBadge($credentials['login'], function($userIdentifier) {
                return $this->userRepository->findOneBy(['login' => $userIdentifier]);
            }),
            new PasswordCredentials($credentials['password']),
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): RedirectResponse
    {
        $targetPath = $this->urlGenerator->generate('app_post_list');echo $targetPath;

        return new RedirectResponse($targetPath);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $data = [
            'error' => 'Wrong login or password'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function fetchCredentials(Request $request): array
    {
        return json_decode($request->getContent(), true) ?? [];
    }
}