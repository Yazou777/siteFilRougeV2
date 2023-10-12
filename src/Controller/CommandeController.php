<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Commande;
use App\Form\CommandeType;
use Doctrine\ORM\EntityManager;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/commande', name: 'app_commande_')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, ProduitRepository $produitRepository): Response
    {

        //S'assurer que l'utilisateur soit connecté
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panier = $session->get('panier', []);

        if($panier === []){
            $this->addFlash('message', 'Votre panier est vide');
            return $this->redirectToRoute('app_accueil');
        }
        // form pre remplie
        $commande = new Commande;
        $commandeData = $commande->setComAdresseLivraison($this->getUser()->getUtiRue()." ".$this->getUser()->getUtiVille()." ".$this->getUser()->getUtiCodePostal()." ".$this->getUser()->getUtiPays());
        //dd($y->getComAdresseLivraison());
        $commande->setComAdresseLivraison($commandeData->getComAdresseLivraison());
        $commande->setComAdresseFacturation($commandeData->getComAdresseLivraison());
        $form = $this->createForm(CommandeType::class, $commande);

        // vide le panier
        //$session->set('panier', []);

        // On initialise des variables
        $data = [];
        $sousTotal = 0;
        $total = 0;
        $totalQte = 0;
        $fdp = 5.5;
        foreach($panier as $id => $quantity){
            $produit = $produitRepository->find($id);

            $data[] = [
                'produit' => $produit,
                'quantite' => $quantity
            ];
            $sousTotal += $produit->getProPrix() * $quantity;
            //$total += $produit->getProPrix() * $quantity +$fdp;
            $totalQte +=  $quantity;
        }
        $total = $sousTotal+$fdp;
        return $this->render('commande/index.html.twig',compact('data','total','totalQte','form','fdp','sousTotal'));
    }





    #[Route('/ajout', name: 'add', methods: ['POST'])]
    public function add(SessionInterface $session, ProduitRepository $produitRepository, EntityManagerInterface $em, Request $request): Response
    {
        //S'assurer que l'utilisateur soit connecté
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panier = $session->get('panier', []);
       // dd($panier);
    if($panier === []){
        $this->addFlash('message', 'Votre panier est vide');
        return $this->redirectToRoute('app_accueil');
    }

    $form = $this->createForm(CommandeType::class, data: null);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        
            $adresseLivraison = $form->get('com_adresse_livraison')->getData();
            $adresseFacture = $form->get('com_adresse_facturation')->getData();
            $commentaire = $form->get('com_commentaire')->getData();
            //dd($adresseLivraison);
            // $em->persist($commande);
            // $em->flush();

           // return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        
     //Le panier n'est pas vide, on crée la commande
     $commande = new Commande();

     // On remplit la commande
     $commande->setComUti($this->getUser());
     //$commande->setComAdresseLivraison($this->getUser()->getUtiRue()." ".$this->getUser()->getUtiVille()." ".$this->getUser()->getUtiCodePostal()." ".$this->getUser()->getUtiPays());
     //dd($commande);
     $commande->setComAdresseFacturation($adresseFacture);
     $commande->setComAdresseLivraison($adresseLivraison);
     $commande->setComCommentaire($commentaire);
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
    }
       $session->remove('panier');

        $this->addFlash('message', 'Commande créée avec succès');
        return $this->redirectToRoute('app_accueil');
    }
}
