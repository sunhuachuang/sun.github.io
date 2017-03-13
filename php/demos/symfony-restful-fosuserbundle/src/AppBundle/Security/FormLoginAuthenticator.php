<?php
namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class FormLoginAuthenticator extends AbstractGuardAuthenticator
{
    private $router;
    private $em;
    private $passwordEncoder;
    private $pathInfo;

    public function __construct(EntityManager $em, Router $router,  $passwordEncoder)
    {
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getCredentials(Request $request)
    {
        $this->pathInfo = $request->getPathInfo();
        if ('/login_check' != $this->pathInfo && '/api/login_check' != $this->pathInfo) {
            return;
        }

        return array(
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
        );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['username'];
        return $this->em->getRepository('AppBundle:User')
            ->findOneByUsername($username);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if (is_null($user)) {
            return;
        }

        $password = $credentials['password'];

        $plainPassword = $credentials['password'];
        if (!$this->passwordEncoder->isPasswordValid($user, $password)) {
            // throw any AuthenticationException
            throw new BadCredentialsException();
        }

        return true;
    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ('/api/login_check' == $this->pathInfo) {
            $user = $token->getUser();

            //每次登陆重新生成apikey
            $apiKey = time().($user->getUsername());
            $user->setApiKey($apiKey);
            $this->em->persist($user);
            $this->em->flush();

            return new JsonResponse($apiKey);
        }

        $url = $this->router->generate('homepage');
        return new RedirectResponse($url);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        );

        return new JsonResponse($data, 403);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $url = $this->router->generate('login');
        return new RedirectResponse($url);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}