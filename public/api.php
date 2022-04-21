<?php 

//Quelle est le type de la requête (GET, POST, PUT, DELETE, PATCH, etc.)
$request_method = $_SERVER["REQUEST_METHOD"];

//Le contenu sera formater en JSON (Peut être mis en commentaire pour fin de debogage)
header('Content-Type: application/json');

if (isset($_REQUEST['objet'])){
    
    switch ($_REQUEST['objet']) {
        
        case 'produit':
            switch ($request_method) {
                case 'GET':
                    
                    if(isset($_REQUEST['id'])){
                        
                        require('controller/controllerProduit.php');
                        $produit = produit($_REQUEST['id'], true);
                                                  
                        echo $produit;
                                                                        
                    } else {
                        
                        require('controller/controllerProduit.php');                     
                        $produits = listProduits(true);

                        echo $produits;
                    }
                    
                    break;
                
                case 'POST':

                    $data = json_decode(file_get_contents('php://input'), true);
                    
                    if(isset($data['produit']) && isset($data['id_categorie']) && isset($data['description'])){
                        
                        if(!is_numeric($data['id_categorie'])){
                            header('400 Bad Request');
                            echo '{"erreur":"id_categorie manquant"}';
                            die();
                        }

                        require('controller/controllerProduit.php');

                        $success = ajoutezProduits($data['produit'], $data['id_categorie'], $data['description']);
                        if($success){
                            header('200 OK');
                            echo '{"OK":"produit rajoutez"}';
                        }else{
                            header('400 Bad Request');
                            echo '{"erreur":"produit non-rajouter"}';
                            die();
                        }

                    }
                    elseif(!isset($data['produit'])){
                        header('400 Bad Request');
                        echo '{"erreur":"produit manquant"}';
                        die();
                    }
                    elseif(!isset($data['id_categorie'])) {
                        header('400 Bad Request');
                        echo '{"erreur":"id_categorie manquant"}';
                        die();
                    }elseif(!isset($data['description'])){
                        header('400 Bad Request');
                        echo '{"erreur":"description manquant"}';
                        die();
                    }

                    break; 
            
                case 'PUT':
                    echo 'Hello PUT';
                    break;
            
                case 'DELETE':

                    if(isset($_REQUEST['id'])){
                        
                        require('controller/controllerProduit.php');
                        if(deleteProduit($_REQUEST['id'])){
                            header('200 OK');
                            echo '{"OK":"produit supprimer"}';
                        }else{
                            header('400 Bad Request');
                            echo '{"erreur":"produit non-supprimer"}';
                            die();
                        }      
                                         
                    }else {
                        header('400 Bad Request');
                        echo '{"erreur":"id manquant"}';
                        die();
                    }
                    
                    break;
            
            default:
                    echo 'Hello ERREUR';
                    break;
            }
            break;
    }
}
?>