<?php

namespace AppBundle\Controller;


use AppBundle\Form\UserRegistrationForm;
use AppBundle\Security\LoginFormAuthenticator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @param Request                   $request
     * @param LoginFormAuthenticator    $authenticator
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     * @return null|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request,
                                   LoginFormAuthenticator $authenticator,
                                   GuardAuthenticatorHandler $guardAuthenticatorHandler)
    {
        $form = $this->createForm(UserRegistrationForm::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Welcome '. $user->getEmail());

            return $guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main'
                );
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}