<?php

namespace Pulecal\Service\Controller;

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class FormAuthController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(FormFactoryInterface $fi, AuthenticationUtils $authenticationUtils): Response
    {
        $form = $fi->createBuilder(FormType::class, null, [
            'action' => $this->generateUrl("login_check"),
            'method' => 'POST'
        ])
            ->add('username', null, [
                'label' => 'Username'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'constraints' => [
                    new PasswordStrength(minScore: PasswordStrength::STRENGTH_MEDIUM)
                ]
            ])
            ->add('_submit', SubmitType::class, [
            ])
        ->getForm();

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        $form->get("username")->setData($lastUsername);

        if($error) {
            $form->addError(new FormError($error->getMessage()));
        }

        return $this->render('form_auth/index.html.twig', [
            "form" => $form
        ]);
    }

    #[Route('/register', name: 'register')]
    public function register(
        \Symfony\Component\HttpFoundation\Request $request, 
        FormFactoryInterface $fi, 
        \Pulecal\Service\Service\UserService $userService
    ): Response {
        $form = $fi->createBuilder(FormType::class, null, [
            'method' => 'POST'
        ])
            ->add('username', \Symfony\Component\Form\Extension\Core\Type\TextType::class, ['label' => 'Username'])
            ->add('email', \Symfony\Component\Form\Extension\Core\Type\EmailType::class, ['label' => 'Email'])
            ->add('password', PasswordType::class, [
                'label' => 'Password'
            ])
            ->add('_submit', SubmitType::class, ['label' => 'Register'])
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $userService->newUser(
                (string)$data['username'], 
                (string)$data['email'], 
                (string)$data['password']
            );

            $this->addFlash('success', 'Account created successfully!');
            return $this->redirectToRoute('login');
        }

        return $this->render('form_auth/index.html.twig', [
            "form" => $form,
            "title" => "Register"
        ]);
    }

    #[Route(path: '/login_check', name: 'login_check')]
    public function erroricek(): never {
        throw new RuntimeException('Begone, thot!');
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
