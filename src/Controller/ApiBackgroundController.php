<?php

namespace App\Controller;

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
        $background = $repository->find(1);

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
            $file = "../public/images/".$background->getImage();
            return new \Symfony\Component\HttpFoundation\BinaryFileResponse($file);
        }
        else{
            $reponse = new Response(json_encode(array(
            'error'     => 'Erreur de background en base'
            )
            ));
            $reponse->headers->set("Content-Type", "application/json");
            $reponse->headers->set("Access-Control-Allow-Origin", "*");
            return $reponse;
        }
    }

    /**
     * @Route("/", name="put_background", methods={"PUT"})
     */
    public function putBackground(Request $request){
        $em = $this->getDoctrine()->getManager();
        $data = [];

        //call $background ici 
        $backgrounds = $em->getRepository(Background::class)->findAll();
        if($backgrounds != null){ //vérifier s'il est n'est pas null, alors on prend le premier enregistrement
            $background = $backgrounds[0];
        } else { // sinon on en créer 
            $background = new Background();
        }

        $content = json_decode($request->getContent(), true);

        if(!is_null($content['color']) && is_null($request->files->get('image'))) {
            // $background = $em->getRepository(Background::class)->find(1);
            if($background != null){
                $background->setColor($content['color']);
                $background->setImage(null);
                $em->persist($background);
                $em->flush();
                $data = ['color' => $background->getColor()];
            } else {
                $data = ['error' => 'Background introuvable en base'];        
            }
        }
        elseif (!is_null($request->get('image')) && is_null($content)) {
            
            $pic = $request->files->get('image');
            $pic_name = 'background.'.$pic->guessExtension();
            $pic->move("../public/images/", $pic_name);

            // $background = $em->getRepository(Background::class)->find(1);
            if($background != null){
                $background->setColor(null);
                $background->setImage($pic_name);
                $em->persist($background);
                $em->flush();
                $data = ['image' => $background->getImage()];
            } else {
                $data = ['error' => 'Background introuvable en base'];        
            }   
        }
        else{
            $data = ['error' => 'Erreur de requête Background'];
        }

        $reponse = new Response(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;
    }
}
