<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(Request $request)
    {
        $locale = $request->getSession()->get('_locale');
        return new RedirectResponse($this->redirectToRoute('app_login',['_locale'=>$locale]));
    }

//    /**
//     * @Route("/chageLocale/{locale}", name="app_change_locale",methods={"GET"})
//     */
//    public function changeLocale(Request $request, $locale):Response
//    {
//        if(isset($locale))
//            $request->getSession()->set('_locale', $locale);
//
//        return new Response('ok');
//    }
}
