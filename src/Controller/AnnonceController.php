<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceFormType;
use App\HttpClient\LegoHttpClient;
use App\Repository\AnnonceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnnonceController extends AbstractController
{
    #[Route('/annonce', name: 'app_annonce')]
    public function index(LegoHttpClient $httpClient, AnnonceRepository $ar): Response
    {
        $annonces = $ar->findBy([], ["publishedAt" => "DESC"]);

        $displayAnnonces = [];
        foreach($annonces as $annonce) {
            $annonce->setCover($httpClient->getSetInfo($annonce->getSetAnnonce())["set_img_url"]);
            $displayAnnonces[] = $annonce;
        }

        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces
        ]);
    }

    #[Route('/annonce/remove/{id}', name: 'remove_annonce')]
    public function remove(ManagerRegistry $doctrine, Annonce $annonce, Request $request): Response
    {
        $em = $doctrine->getManager();
        $em->remove($annonce);
        $em->flush();
        return $this->redirectToRoute("app_annonce");
    }

    #[Route('/annonce/new', name: 'new_annonce')]
    public function new(ManagerRegistry $doctrine, LegoHttpClient $httpClient, AnnonceRepository $ar, Annonce $annonce = null, Request $request): Response
    {
        if($this->getUser()){
            $annonce = new Annonce();
            $sets = $httpClient->getSets();
            $form = $this->createForm(AnnonceFormType::class, $annonce);
            $form->handleRequest($request);
    
            $em = $doctrine->getManager(); 
            
            if($form->isSubmitted() && $form->isValid()){
                $annonce = $form->getData();
                $annonce->setSetAnnonce($request->request->get('setAnnonce'));
                $annonce->setPublishedAt(new \DateTime());
                $annonce->setUser($this->getUser());
    
                $em->persist($annonce);
                $em->flush();
    
                return $this->redirectToRoute("app_annonce");
            }
        } else {
            return $this->redirectToRoute("app_login");
        }

        return $this->render('annonce/new.html.twig', [
            'formNewAnnonce' => $form->createView(),
            'sets' => $sets[3]
        ]);
    }

    #[Route('/annonce/{id}', name: 'show_annonce')]
    public function show(LegoHttpClient $httpClient, Annonce $annonce, Request $request): Response
    {
        $details = $httpClient->getSetInfo($annonce->getSetAnnonce());
        $theme_id = $details["theme_id"];
        $theme = $httpClient->getThemeInfo($theme_id);
        $minifigs = $httpClient->getMinifigs($annonce->getSetAnnonce());
        
        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
            'theme' => $theme,
            'details' => $details,
            'minifigs' => $minifigs
        ]);
    }
}
