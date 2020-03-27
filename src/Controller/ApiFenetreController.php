<?php

namespace App\Controller;

use App\Entity\Authentification;
use App\Entity\Fenetre;
use Doctrine\Migrations\Finder\Exception\FinderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("api/fenetre", name="api_fenetre")
 */
class ApiFenetreController extends AbstractController
{
    /**
     * @Route("/", name="get_fenetre", methods={"GET"})
     */
    public function getFenetre(Request $request){

        $em = $this->getDoctrine()->getManager();
        $data = []; 
        $fenetres = $em->getRepository(Fenetre::class)->findAll();
        if($fenetres !== null) {
            foreach ($fenetres as $fenetre) {
                if(!$fenetre->getVeille()) {
                    array_push($data, [
                        "id" => $fenetre->getId(),
                        "url" => $fenetre->getUrl(),
                        "width" => $fenetre->getWidth(),
                        "height" => $fenetre->getHeight(),
                        "posX" => $fenetre->getPosx(),
                        "posY" =>  $fenetre->getPosy(),
                        "veille" =>  $fenetre->getVeille(),
                        "youtube" =>  $fenetre->getIsYoutube(),
                        "playlist" =>  $fenetre->getIsPlaylist(),
                    ]);
                }
            }
        } else {
            $data = ["error" => "Aucune fenêtre enregistrée"];    
        }   

        $reponse = new Response();
        $reponse->setContent(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;

    }

    /**
     * @Route("/veille", name="get_fenetreveille", methods={"GET"})
     */
    public function getFenetreVeille(Request $request){

        $em = $this->getDoctrine()->getManager();
        $data = []; 

        $fenetres = $em->getRepository(Fenetre::class)->findAll();
        if($fenetres !== null) {
            foreach ($fenetres as $fenetre) {
                if($fenetre->getVeille()) {
                    array_push($data, [
                        "id" => $fenetre->getId(),
                        "url" => $fenetre->getUrl(),
                        "width" => $fenetre->getWidth(),
                        "height" => $fenetre->getHeight(),
                        "posX" => $fenetre->getPosx(),
                        "posY" =>  $fenetre->getPosy(),
                        "veille" =>  $fenetre->getVeille(),
                        "youtube" =>  $fenetre->getIsYoutube(),
                        "playlist" =>  $fenetre->getIsPlaylist(),
                    ]);
                }
            }
        } else {
            $data = ["error" => "Aucune fenêtre enregistrée"];    
        }  

        $reponse = new Response();
        $reponse->setContent(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;

    }


    /**
     * @Route("/{id}", name="get_fenetrebyid", methods={"GET"})
     */
    public function getFenetreById(Request $request, $id){
        $data = [];
        
        $em = $this->getDoctrine()->getManager();

        $fenetre = $em->getRepository(Fenetre::class)->find($id);
        if($fenetre != null){
            array_push($data, [
                "id" => $fenetre->getId(),
                "url" => $fenetre->getUrl(),
                "width" => $fenetre->getWidth(),
                "height" => $fenetre->getHeight(),
                "posX" => $fenetre->getPosx(),
                "posY" =>  $fenetre->getPosy(),
                "veille" =>  $fenetre->getVeille(),
                "youtube" =>  $fenetre->getIsYoutube(),
                "playlist" =>  $fenetre->getIsPlaylist(),
            ]);
        } else {
            $data = ["error" => "Aucune fenêtre ne correspond à cet id"];
        }      

        $reponse = new Response();
        $reponse->setContent(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;
    }

    /**
     * @Route("/", name="post_fenetre", methods={"POST"})
     */
    public function postFenetre(Request $request){
        $em = $this->getDoctrine()->getManager();
        $data = [];

        if($request->headers->get('X-Auth-Token') !== null) {
            $authentication = $em->getRepository(Authentification::class)->findOneBy(["token" => $request->headers->get('X-Auth-Token')]);
            if($authentication !== null) {
                if( $request->get('url') != null && $request->get('width') != null && $request->get('height') != null && $request->get('posX') != null && $request->get('posY') != null && $request->get('veille') != null && $request->get('youtube') != null && $request->get('playlist') != null )
                {
                    $fenetre = new Fenetre();
                    $fenetre->setUrl($request->get('url'));
                    $fenetre->setWidth($request->get('width'));
                    $fenetre->setHeight($request->get('height'));
                    $fenetre->setPosx($request->get('posX'));
                    $fenetre->setPosy($request->get('posY'));
                    $request->get('veille') === 'false' ? $fenetre->setVeille(false) : $fenetre->setVeille(true);
                    $request->get('youtube') === 'false' ? $fenetre->setIsYoutube(false) : $fenetre->setIsYoutube(true); 
                    $request->get('playlist') === 'false' ? $fenetre->setIsPlaylist(false) : $fenetre->setIsPlaylist(true); 

                    $em->persist($fenetre);
                    $em->flush();
                    $data = [
                        "id" => $fenetre->getId(),
                        "url" => $fenetre->getUrl(),
                        "width" => $fenetre->getWidth(),
                        "height" => $fenetre->getHeight(),
                        "posX" => $fenetre->getPosx(),
                        "posY" =>  $fenetre->getPosy(),
                        "veille" =>  $fenetre->getVeille(),
                        "youtube" =>  $fenetre->getIsYoutube(),
                        "playlist" =>  $fenetre->getIsPlaylist(),
                    ];
                } else {
                    $data = ['error' => 'Erreur lors de l\'enregistrement de la fenêtre'];
                }
            } else {
                $data = ["error" => "X-Auth-Token invalide"];
            }
        } else {
            $data = ["error" => "X-Auth-Token est requis"];
        }
        
        $reponse = new Response();
        $reponse->setContent(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");        
        $reponse->headers->set("Access-Control-Allow-Methods", "DELETE, POST, GET, PUT");
        $reponse->headers->set("Access-Control-Allow-Headers", "Content-Type, Access-Control-Allow-Headers, Authorization, X-Auth-Token");
        return $reponse;
    }

    /**
     * @Route("/{id}", name="put_fenetre", methods={"PUT"})
     */
    public function putFenetre(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $data = [];
        $content = json_decode($request->getContent(), true);
        if($request->headers->get('X-Auth-Token') !== null) {
            $authentication = $em->getRepository(Authentification::class)->findOneBy(["token" => $request->headers->get('X-Auth-Token')]);
            if($authentication !== null) {
                    if( $id != null && $content['url'] != null && $content['width'] != null && $content['height'] != null && $content['posX'] != null && $content['posY'] != null && $content['veille'] != null )
                    {
                        $fenetre = $em->getRepository(Fenetre::class)->find($id);
                        if($fenetre != null){
                            $fenetre->setUrl($content['url']);
                            $fenetre->setWidth($content['width']);
                            $fenetre->setHeight($content['height']);
                            $fenetre->setPosx($content['posX']);
                            $fenetre->setPosy($content['posY']);
                            $content['veille'] === 'false' ? $fenetre->setVeille(false) : $fenetre->setVeille(true);
                            $content['youtube'] === 'false' ? $fenetre->setIsYoutube(false) : $fenetre->setIsYoutube(true); 
                            $content['playlist'] === 'false' ? $fenetre->setIsPlaylist(false) : $fenetre->setIsPlaylist(true); 

                            $em->flush();
                            $data = [
                                "id" => $fenetre->getId(),
                                "url" => $fenetre->getUrl(),
                                "width" => $fenetre->getWidth(),
                                "height" => $fenetre->getHeight(),
                                "posX" => $fenetre->getPosx(),
                                "posY" =>  $fenetre->getPosy(),
                                "veille" =>  $fenetre->getVeille(),
                                "youtube" =>  $fenetre->getIsYoutube(),
                                "playlist" =>  $fenetre->getIsPlaylist(),
                            ];
                    } else {
                        $data = ['error' => 'Aucune fenêtre ne correspond à cet id'];        
                    }
                } else {
                    $data = ['error' => 'Erreur lors de la modification de la fenêtre'];
                }        
            } else {
                $data = ["error" => "X-Auth-Token invalide"];
            }
        } else {
            $data = ["error" => "X-Auth-Token est requis"];
        }

        $reponse = new Response();
        $reponse->setContent(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");        
        $reponse->headers->set("Access-Control-Allow-Methods", "DELETE, POST, GET, PUT");
        $reponse->headers->set("Access-Control-Allow-Headers", "Content-Type, Access-Control-Allow-Headers, Authorization, X-Auth-Token");
        return $reponse;
    }

     /**
     * @Route("/{id}", name="delete_fenetre", methods={"DELETE"})
     */
    public function deleteFenetre(Request $request, $id){
        //Suppression d'une fenêtre
        $em = $this->getDoctrine()->getManager();
        $data = [];
        if($request->headers->get('X-Auth-Token') !== null) {
            $authentication = $em->getRepository(Authentification::class)->findOneBy(["token" => $request->headers->get('X-Auth-Token')]);
            if($authentication !== null) {
                if( $id != null)
                {
                    $fenetre = $em->getRepository(Fenetre::class)->find($id);
                    if($fenetre != null){
                        $em->remove($fenetre);
                        $em->flush();
                        $data = ['success' => 'Fenêtre supprimée avec succès'];
                    } else {
                        $data = ['error' => 'Aucune fenêtre ne correspond à cet id'];        
                    }
                } else {
                    $data = ['error' => 'Erreur lors de la suppression de la fenêtre'];
                }    
            } else {
                $data = ["error" => "X-Auth-Token invalide"];
            }
        } else {
            $data = ["error" => "X-Auth-Token est requis"];
        }    

        $reponse = new Response();
        $reponse->setContent(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        $reponse->headers->set("Access-Control-Allow-Methods", "DELETE, POST, GET, PUT");
        $reponse->headers->set("Access-Control-Allow-Headers", "Content-Type, Access-Control-Allow-Headers, Authorization, X-Auth-Token");
        return $reponse;
    }
}
