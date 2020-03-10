<?php

namespace App\Controller;

use App\Entity\Fenetre;
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
     * @Route("/", name="api_fenetre_accueil")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiFenetreController.php',
        ]);
    }

    /**
     * @Route("/get", name="get_fenetre", methods={"GET"})
     */
    public function getFenetre(Request $request){
        $data = [];
        
        $em = $this->getDoctrine()->getManager();

        $fenetres = $em->getRepository(Fenetre::class)->findAll();
        foreach ($fenetres as $fenetre) {
            array_push($data, [
                "id" => $fenetre->getId(),
                "url" => $fenetre->getUrl(),
                "widht" => $fenetre->getWidth(),
                "height" => $fenetre->getHeight(),
                "posx" => $fenetre->getPosx(),
                "posy" =>  $fenetre->getPosy(),
            ]);
        }

        $reponse = new Response();
        $reponse->setContent(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;
    }

    /**
     * @Route("/post", name="post_fenetre", methods={"POST"})
     */
    public function postFenetre(Request $request){
        //Ajout d'une nouvelle fenêtre
        $em = $this->getDoctrine()->getManager();

        if( $request->get('url') != null && $request->get('width') != null && $request->get('height') != null && $request->get('posx') != null && $request->get('posy') != null )
        {
            $fenetre = new Fenetre();
            $fenetre->setUrl($request->get('url'));
            $fenetre->setWidth($request->get('width'));
            $fenetre->setHeight($request->get('height'));
            $fenetre->setPosx($request->get('posx'));
            $fenetre->setPosy($request->get('posy'));

            $em->persist($fenetre);
            $em->flush();
        } else {
            return $this->json(['reponse' => 'Erreur lors de l\'enregistrement de la fenêtre']);
        }
        
        return $this->json(['reponse' => 'Fenêtre enregistrée avec succès']);        
    }

    /**
     * @Route("/put", name="put_fenetre", methods={"PUT"})
     */
    public function putFenetre(Request $request){
        //Mise à jour d'une fenêtre
        $em = $this->getDoctrine()->getManager();

        if( $request->get('id') != null && $request->get('url') != null && $request->get('width') != null && $request->get('height') != null && $request->get('posx') != null && $request->get('posy') != null )
        {
            $fenetre = $em->getRepository(Fenetre::class)->find($request->get('id'));
            if($fenetre != null){
                $fenetre->setUrl($request->get('url'));
                $fenetre->setWidth($request->get('width'));
                $fenetre->setHeight($request->get('height'));
                $fenetre->setPosx($request->get('posx'));
                $fenetre->setPosy($request->get('posy'));

                $em->flush();
            } else {
                return $this->json(['reponse' => 'Aucune fenêtre existante pour cet Id']);        
            }

        } else {
            return $this->json(['reponse' => 'Erreur lors de la modification de la fenêtre']);
        }

        return $this->json(['reponse' => 'Fenêtre modifiée avec succès']);

    }

     /**
     * @Route("/delete", name="delete_fenetre", methods={"DELETE"})
     */
    public function deleteFenetre(Request $request){
        //Suppression d'une fenêtre
    }
}
