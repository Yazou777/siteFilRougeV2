<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findAll();
        $findby = $categorieRepository->findBy(['cat_parent'=>null]);
        $findpc = $categorieRepository->findpcn();
        //dd($findby);
        return $this->render('accueil/index.html.twig', [
            'categories' => $categories, 'findby' => $findby ,'findpc' => $findpc ,
        // return $this->render('accueil/index.html.twig', [
        //     'controller_name' => 'AccueilController',
        ]);
    }

    #[Route('/accueil/{cat_nom}', name: 'app_guitares_basses')]
    public function indexSousCat(CategorieRepository $categorieRepository, $cat_nom): Response
    {
        $id =  $categorieRepository->findId($cat_nom);
        $cat = $categorieRepository->findOneBy([ "cat_nom" => $cat_nom])->getId();
        $findby = $categorieRepository->findBy(['cat_parent'=> $cat]);
    //dd($findby);
        //$idSql = $categorieRepository->findIdSql($cat_nom);
     //  dd($id->getId());
      // dd($cat->getId());

        return $this->render('accueil/indexSousCat.html.twig', [
            'findby' => $findby,
         ]);
    }
}
