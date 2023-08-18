<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\PageRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MonologBundle\SwiftMailer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
  
    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        TranslatorInterface $translator,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $formAuthenticator,
        Swift_Mailer $mailer,
        PageRepository $pageRepository
    ): Response {
        $user = new User();
        if ($request->request->has('registration_form')) {
            $userForm = $request->get('registration_form');
            $userForm['login'] = $userForm['email'];
            $request->request->set('registration_form', $userForm);
            $user->setLogin($userForm['email']);
        }
        // conditions d'utilisation
        $cuPage = $pageRepository->find(5);

        $form = $this->createForm(RegistrationFormType::class, $user, ['translator' => $translator]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->addRole('ROLE_WAITING_VALIDATION');
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Security.messages.accountCreatedAndWaitingAdminValidation');

            // send email to admin
            // recherche des admins
            $destsMail = [];
            $admins = $this->em->getRepository(User::class)->search(['role'=>'ROLE_ADMIN']);
            foreach($admins as $admin) {
                $destsMail[] = $admin->getEmail();
            }
            $message = (new Swift_Message($translator->trans('Security.messages.newAccountWaitValidation')))
                    ->setFrom($this->getParameter('app.genericMail'))
                    ->setTo($destsMail)
                    ->setBody(
                        $this->renderView(
                            'emails/newAccountWaitValidation.html.twig',
                            compact('user')
                        ),
                        'text/html'
                    )
            ;
            $mailer->send($message);

            // log user
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $formAuthenticator,
                'main'
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'cuPage' => $cuPage,
        ]);
    }

    /**
     * @Route("/register/waiting-validation", name="app_registration-waiting-validation")
     */
    public function registrationWaitingValidation()
    {
        return $this->render('registration/waitingValidation.html.twig', [
        ]);
    }
}
