<?php

namespace App\Controller;

use App\Entity\Authentification;
use App\Entity\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;;


/**
 * @Route("/api/options", name="api_options")
 */
class ApiOptionsController extends AbstractController
{
    /**
     * @Route("/", name="get_options", methods={"GET"})
     */
    public function getOptions(){

        $repository = $this->getDoctrine()->getRepository(Options::class);
        $options = $repository->findOneBy(array('veille' => 0));

        if(!is_null($options->getColor())) {
            $reponse = new Response(json_encode(array(
            'color'     => $options->getColor()
            )
            ));

            $reponse->headers->set("Content-Type", "application/json");
            $reponse->headers->set("Access-Control-Allow-Origin", "*");
            return $reponse;
        }
        elseif (!is_null($options->getImage())) {
            $file = "../public/Images/".$options->getImage();
            return new \Symfony\Component\HttpFoundation\BinaryFileResponse($file);
        }
        else{
            $reponse = new Response(json_encode(array(
            'error'     => 'Erreur options en base'
            )
            ));
            $reponse->headers->set("Content-Type", "application/json");
            $reponse->headers->set("Access-Control-Allow-Origin", "*");
            return $reponse;
        }
    }

    /**
     * @Route("/veille/", name="get_options_veille", methods={"GET"})
     */
    public function getOptionsVeille(){

        $repository = $this->getDoctrine()->getRepository(Options::class);
        $options = $repository->findOneBy(array('veille' => 1));

        if(!is_null($options->getColor())) {
            $reponse = new Response(json_encode(array(
            'color'     => $options->getColor(),
            'temps_veille_1' => $options->getTempsVeille1(),
            'temps_veille_2' => $options->getTempsVeille2()
            )
            ));
        }
        elseif (!is_null($options->getImage())) {
            $file = "../public/Images/".$options->getImage();
            return new \Symfony\Component\HttpFoundation\BinaryFileResponse($file);
            
            //AJOUT IK 31/03/2020
            /*$reponse = new Response(json_encode(array(
            'img'     => \Symfony\Component\HttpFoundation\BinaryFileResponse($file),
            'temps_veille_1' => $options->getTempsVeille1(),
            'temps_veille_2' => $options->getTempsVeille2()
            )
            ));*/
        }
        else{
            $reponse = new Response(json_encode(array(
            'error'     => 'Erreur options en base'
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
     * @Route("/", name="post_options", methods={"POST"})
     */
    public function postOptions(Request $request){
        $em = $this->getDoctrine()->getManager();
        $data = [];
        $options = null;
        $veille = "";

        if($request->headers->get('X-Auth-Token') !== null) {
            $authentication = $em->getRepository(Authentification::class)->findOneBy(["token" => $request->headers->get('X-Auth-Token')]);
            if($authentication !== null) {
                if($request->get('veille') === 'false') {
                    $options = $em->getRepository(Options::class)->findOneBy(array('veille' => 0));
                    if($options === null) {
                        $options = new Options();
                        $options->setVeille(0);
                    }

                } elseif ($request->get('veille') === 'true'){
                    $options = $em->getRepository(Options::class)->findOneBy(array('veille' => 1));
                    if($options === null) {
                        $options = new Options();
                        $options->setVeille(1);
                    }
                    $veille = "_veille";
                }
                $pic = $request->files->get('image');
                $color = $request->get('color');                
                
                if($options !== null) {
                    if($color !== null && $pic === null) {
                        $options->setColor($color);
                        $options->setImage(null);
                        $em->persist($options);
                        $em->flush();
                        $data = ['color' => $options->getColor()];
                    }
                    elseif ($color === null && $pic !== null) {
                        $pic_name = 'options'.$veille.".".$pic->guessExtension();
                        $pic->move("../public/Images/", $pic_name);    
                        $options->setColor(null);
                        $options->setImage($pic_name);
                        $em->persist($options);
                        $em->flush();
                        $data = ['image' => $options->getImage()];
                    } else {
                        $data = ["error" => "Erreur requête options"];
                    }
                } else{
                    $data = ['error' => 'Options introuvables en base'];
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
