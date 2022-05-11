<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CategoryRepository;

class CategoryController extends AbstractController
{
    // ManagerRegistry pour aller chercher dans la database
    private function categoryManager(ManagerRegistry $doctrine): CategoryRepository
    {
        return $doctrine->getManager()->getRepository(Category::class);
    }


    //Route aller chercher la liste des cagories
    #[Route('/categoryList', name: 'app_categoryList')]
    public function getListCategory(ManagerRegistry $doctrine, Request $request): Response
    {
        $listCategory = $this->categoryManager($doctrine)->findAll();
         
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
        $json = $serializer->serialize($listCategory, 'json');
        $response = new Response($json);
        return $response;
        
    }
}
