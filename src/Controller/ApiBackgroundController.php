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

        if(!is_null($request->get('color'))) {
            $background = $em->getRepository(Background::class)->find(1);
            if($background != null){
                $background->setColor($request->get('color'));
                $background->setImage(null);
                $em->flush();
                $data = ['color' => $background->getColor()];
            } else {
                $data = ['error' => 'Background introuvable en base'];        
            }
        }
        elseif (!is_null($request->get('image'))) {
            
            $pic = $request->files->get('image');
            $pic_name = 'background.'.$pic->guessExtension();
            $pic->move("../public/images/", $pic_name);

            $background = $em->getRepository(Background::class)->find(1);
            if($background != null){
               $background->setColor(null);
                $background->setImage($pic_name);
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
