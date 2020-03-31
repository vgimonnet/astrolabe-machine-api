<?php

namespace App\Controller;

use App\Entity\Authentification;
use App\Entity\Background;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;;


/**
 * @Route("/api/background", name="api_background")
 */
class ApiBackgroundController extends AbstractController
{
    /**
     * @Route("/", name="get_background", methods={"GET"})
     */
    public function getBackground(){

        $repository = $this->getDoctrine()->getRepository(Background::class);
        $background = $repository->findOneBy(array('veille' => 0));

        if(!is_null($background->getColor())) {
            $reponse = new Response(json_encode(array(
            'color'     => $background->getColor()
            )
            ));

            $reponse->headers->set("Content-Type", "application/json");
            $reponse->headers->set("Access-Control-Allow-Origin", "*");
            return $reponse;
        }
        elseif (!is_null($background->getImage())) {
            $file = "../public/Images/".$background->getImage();
            return new \Symfony\Component\HttpFoundation\BinaryFileResponse($file);
        }
        else{
            $reponse = new Response(json_encode(array(
            'error'     => 'Erreur background en base'
            )
            ));
            $reponse->headers->set("Content-Type", "application/json");
            $reponse->headers->set("Access-Control-Allow-Origin", "*");
            return $reponse;
        }
    }

    /**
     * @Route("/veille/", name="get_background_veille", methods={"GET"})
     */
    public function getBackgroundVeille(){

        $repository = $this->getDoctrine()->getRepository(Background::class);
        $background = $repository->findOneBy(array('veille' => 1));

        if(!is_null($background->getColor())) {
            $reponse = new Response(json_encode(array(
            'color'     => $background->getColor(),
            )
            ));
        }
        elseif (!is_null($background->getImage())) {
            $file = "../public/Images/".$background->getImage();
            return new \Symfony\Component\HttpFoundation\BinaryFileResponse($file);
        }
        else{
            $reponse = new Response(json_encode(array(
            'error'     => 'Erreur background en base'
            )
            ));
        }

        if($reponse) {
            $reponse->headers->set("Content-Type", "application/json");
            $reponse->headers->set("Access-Control-Allow-Origin", "*");
            return $reponse;
        }
    }

    /**
     * @Route("/", name="post_background", methods={"POST"})
     */
    public function postBackground(Request $request){
        $em = $this->getDoctrine()->getManager();
        $data = [];
        $background = null;
        $veille = "";

        if($request->headers->get('X-Auth-Token') !== null) {
            $authentication = $em->getRepository(Authentification::class)->findOneBy(["token" => $request->headers->get('X-Auth-Token')]);
            if($authentication !== null) {
                if($request->get('veille') === 'false') {
                    $background = $em->getRepository(Background::class)->findOneBy(array('veille' => 0));
                    if($background === null) {
                        $background = new Background();
                        $background->setVeille(0);
                    }

                } elseif ($request->get('veille') === 'true'){
                    $background = $em->getRepository(Background::class)->findOneBy(array('veille' => 1));
                    if($background === null) {
                        $background = new Background();
                        $background->setVeille(1);
                    }
                    $veille = "_veille";
                }
                $pic = $request->files->get('image');
                $color = $request->get('color');                
                
                if($background !== null) {
                    if($color !== null && $pic === null) {
                        $background->setColor($color);
                        $background->setImage(null);
                        $em->persist($background);
                        $em->flush();
                        $data = ['color' => $background->getColor()];
                    }
                    elseif ($color === null && $pic !== null) {
                        $pic_name = 'background'.$veille.".".$pic->guessExtension();
                        $pic->move("../public/Images/", $pic_name);    
                        $background->setColor(null);
                        $background->setImage($pic_name);
                        $em->persist($background);
                        $em->flush();
                        $data = ['image' => $background->getImage()];
                    } else {
                        $data = ["error" => "Erreur requête background"];
                    }
                } else{
                    $data = ['error' => 'Background introuvables en base'];
                }
            } else {
                $data = ["error" => "X-Auth-Token invalide"];
            }
        } else {
            $data = ["error" => "X-Auth-Token est requis"];
        }
        $reponse = new Response(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;
    }
}
