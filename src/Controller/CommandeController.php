<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Commande;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/commande', name: 'app_commande_')]
class CommandeController extends AbstractController
{
    #[Route('/ajout', name: 'add')]
    public function add(SessionInterface $session, ProduitRepository $produitRepository, EntityManagerInterface $em): Response
    {
        //S'assurer que l'utilisateur soit connecté
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panier = $session->get('panier', []);
       // dd($panier);
    if($panier === []){
        $this->addFlash('message', 'Votre panier est vide');
        return $this->redirectToRoute('app_accueil');
    }

     //Le panier n'est pas vide, on crée la commande
     $commande = new Commande();

     // On remplit la commande
     $commande->setComUti($this->getUser());
     //$order->setReference(uniqid());

     // On parcourt le panier pour créer les détails de commande
     foreach($panier as $proId => $quantite){
         $panier = new Panier();

         // On va chercher le produit
         $produit = $produitRepository->find($proId);
         
         $prix = $produit->getProPrix();

         // On crée le détail de commande
         $panier->setPanPro($produit);
         $panier->setPanPrixUnite($prix);
         $panier->setPanQuantite($quantite);

         $commande->addPanier($panier);
     }

     // On persiste et on flush
     $em->persist($commande);
     $em->flush();

       $session->remove('panier');

        $this->addFlash('message', 'Commande créée avec succès');
        return $this->redirectToRoute('app_accueil');
    }
}
