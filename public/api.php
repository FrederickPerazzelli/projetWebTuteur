<?php 

namespace App\Controller;


//Quelle est le type de la requête (GET, POST, PUT, DELETE, PATCH, etc.)
$request_method = $_SERVER["REQUEST_METHOD"];

//Le contenu sera formater en JSON (Peut être mis en commentaire pour fin de debogage)
header('Content-Type: application/json');

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\StatusRepository;
use App\Entity\Status;

if (isset($_REQUEST['objet'])){
    
    switch ($_REQUEST['objet']) {
        
        case 'Status':
            switch ($request_method) {
                case 'GET':
                    
                    
                    $statusController = new StatusController;
                    $Status = $statusController->listStatus( true);                                                 
                    echo $Status;

                    break;
                
                case 'POST':
                    
                    echo 'POST';
                    break; 
            
                case 'PUT':
                    echo 'PUT';
                    break;
            
                case 'DELETE':

                    echo 'DELETE'; 
                    break;
            
            default:
                    echo 'ERREUR';
                    break;
            }
            break;
    }
}
?>