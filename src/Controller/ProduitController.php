<?php

namespace App\Controller;


use App\Entity\Categorie;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    #[Route('/produit/{cat_nom}', name: 'app_produit')]
    public function index( CategorieRepository $categorieRepository ,ProduitRepository $produitRepository ,$cat_nom): Response
    {
       $cat = $categorieRepository->findOneBy([ "cat_nom" => $cat_nom])->getId();
       $findby = $produitRepository->findBy(['cat' => $cat]);
       $catParentId = $categorieRepository->findOneBy([ "cat_nom" => $cat_nom])->getCatParent()->getId();
       $catParentName= $categorieRepository->findOneBy(['id' => $catParentId])->getCatNom();
        //dd($findby);
        return $this->render('produit/index.html.twig', [
            // 'Produit' => $Produit,
            'findby' => $findby,
            "chemin_de_fer" => [
                ["name" => $catParentName, "link" => "/accueil/".$catParentName],
                 ["name" => $cat_nom, "link" => "/produit/".$cat_nom],
            ]
        ]);
    }
}
