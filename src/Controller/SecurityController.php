<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/bookmarks', name: 'app_bookmarks')]
    public function bookmarks(UserRepository $ur): Response
    {
        $user = $ur->find($this->getUser());
        $bookmarks = $user->getBookmarks();
        return $this->render('security/bookmarks.html.twig', [
            'bookmarks' => $bookmarks
        ]);
    }

    #[Route(path: '/bookmark/add/{idAnnonce}', name: 'add_bookmark')]
    #[ParamConverter('annonce', options:['mapping' => ['idAnnonce' => 'id']])]
    public function addBookmark(Annonce $annonce, ManagerRegistry $doctrine, UserInterface $user): Response
    {
        $user->addBookmark($annonce);

        $em = $doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute("app_annonce");
    }

    #[Route(path: '/bookmark/remove/{idAnnonce}', name: 'remove_bookmark')]
    #[ParamConverter('annonce', options:['mapping' => ['idAnnonce' => 'id']])]
    public function removeBookmark(Annonce $annonce, ManagerRegistry $doctrine, UserInterface $user): Response
    {
        $user->removeBookmark($annonce);

        $em = $doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute("app_annonce");
    }
}
