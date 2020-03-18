<?php

namespace App\Controller;

use App\Entity\Fenetre;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("api/", name="api_fenetre")
 */
class ApiFenetreController extends AbstractController
{
    /**
     * @Route("/get/fenetre", name="get_fenetre", methods={"GET"})
     */
    public function getFenetre(Request $request){
        $data = [];
        
        $em = $this->getDoctrine()->getManager();

        $fenetres = $em->getRepository(Fenetre::class)->findAll();
        foreach ($fenetres as $fenetre) {
            array_push($data, [
                "id" => $fenetre->getId(),
                "url" => $fenetre->getUrl(),
                "width" => $fenetre->getWidth(),
                "height" => $fenetre->getHeight(),
                "posX" => $fenetre->getPosx(),
                "posY" =>  $fenetre->getPosy(),
            ]);
        }

        $reponse = new Response();
        $reponse->setContent(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;
    }

    /**
     * @Route("/get/fenetre/{id}", name="get_fenetrebyid", methods={"GET"})
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
            ]);
        } else {
            $data = ["erreur" => "Aucune fenêtre ne correspond à cet id"];
        }            

        $reponse = new Response();
        $reponse->setContent(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;
    }

    /**
     * @Route("/post/fenetre", name="post_fenetre", methods={"POST"})
     */
    public function postFenetre(Request $request){
        $em = $this->getDoctrine()->getManager();
        $data = [];

        if( $request->get('url') != null && $request->get('width') != null && $request->get('height') != null && $request->get('posX') != null && $request->get('posY') != null )
        {
            $fenetre = new Fenetre();
            $fenetre->setUrl($request->get('url'));
            $fenetre->setWidth($request->get('width'));
            $fenetre->setHeight($request->get('height'));
            $fenetre->setPosx($request->get('posX'));
            $fenetre->setPosy($request->get('posY'));

            $em->persist($fenetre);
            $em->flush();
            $data = [
                "id" => $fenetre->getId(),
                "url" => $fenetre->getUrl(),
                "width" => $fenetre->getWidth(),
                "height" => $fenetre->getHeight(),
                "posX" => $fenetre->getPosx(),
                "posY" =>  $fenetre->getPosy(),
            ];
        } else {
           $data = ['erreur' => 'Erreur lors de l\'enregistrement de la fenêtre'];
        }
        
        $reponse = new Response();
        $reponse->setContent(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;
    }

    /**
     * @Route("/put/fenetre/{id}", name="put_fenetre", methods={"PUT"})
     */
    public function putFenetre(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $data = [];

        if( $id != null && $request->get('url') != null && $request->get('width') != null && $request->get('height') != null && $request->get('posX') != null && $request->get('posY') != null )
        {
            $fenetre = $em->getRepository(Fenetre::class)->find($id);
            if($fenetre != null){
                $fenetre->setUrl($request->get('url'));
                $fenetre->setWidth($request->get('width'));
                $fenetre->setHeight($request->get('height'));
                $fenetre->setPosx($request->get('posX'));
                $fenetre->setPosy($request->get('posY'));

                $em->flush();
                $data = [
                    "id" => $fenetre->getId(),
                    "url" => $fenetre->getUrl(),
                    "width" => $fenetre->getWidth(),
                    "height" => $fenetre->getHeight(),
                    "posX" => $fenetre->getPosx(),
                    "posY" =>  $fenetre->getPosy(),
                ];
            } else {
                $data = ['erreur' => 'Aucune fenêtre ne correspond à cet id'];        
            }
        } else {
            $data = ['erreur' => 'Erreur lors de la modification de la fenêtre'];
        }        

        $reponse = new Response();
        $reponse->setContent(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;
    }

     /**
     * @Route("/delete/fenetre/{id}", name="delete_fenetre", methods={"DELETE"})
     */
    public function deleteFenetre(Request $request, $id){
        //Suppression d'une fenêtre
        $em = $this->getDoctrine()->getManager();
        $data = [];

        if( $id != null)
        {
            $fenetre = $em->getRepository(Fenetre::class)->find($id);
            if($fenetre != null){
                $em->remove($fenetre);
                $em->flush();
                $data = ['success' => 'Fenêtre supprimée avec succès'];
            } else {
                $data = ['erreur' => 'Aucune fenêtre ne correspond à cet id'];        
            }
        } else {
            $data = ['erreur' => 'Erreur lors de la suppression de la fenêtre'];
        }        

        $reponse = new Response();
        $reponse->setContent(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;
    }
}
