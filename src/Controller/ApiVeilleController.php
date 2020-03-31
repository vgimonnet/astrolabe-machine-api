<?php

namespace App\Controller;

use App\Entity\Authentification;
use App\Entity\Background;
use App\Entity\Veille;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/veille", name="api_veille")
 */
class ApiVeilleController extends AbstractController
{
    /**
     * @Route("/", name="get_temps_veille", methods={"GET"})
     */
    public function getTempsVeille() {
        $data = [];
        $em = $this->getDoctrine()->getManager();
        $veilleNormal = $em->getRepository(Veille::class) ->findOneBy(['label' => 'temps_normal']);
        $veilleMedia = $em->getRepository(Veille::class) ->findOneBy(['label' => 'temps_media']);
        $veilleNormal !== null ? array_push($data, ["temps_normal" => $veilleNormal->getTemps()]) : array_push($data, ["error" => "Temps de veille normal non trouvé en base"]);
        $veilleMedia !== null ? array_push($data, ["temps_media" => $veilleNormal->getTemps()]) : array_push($data, ["error" => "Temps de veille media non trouvé en base"]);
        
        $reponse = new Response();
        $reponse->setContent(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;        
    }

    /**
     * @Route("/", name="put_temps_veille", methods={"POST"})
     */
    public function postTempsVeille(Request $request) {
        if($request->headers->get('X-Auth-Token') !== null) {
            $em = $this->getDoctrine()->getManager();
            $authentication = $em->getRepository(Authentification::class)->findOneBy(["token" => $request->headers->get('X-Auth-Token')]);
            if($authentication !== null) {
                $veilleNormal = null;
                $veilleMedia = null;
                if($request->get('temps_normal') !== null && $request->get('temps_media') !== null) {
                    $veilleNormal = $em->getRepository(Veille::class) ->findOneBy(['label' => 'temps_normal']);
                    if($veilleNormal === null) {
                        $veilleNormal = new Veille();
                        $veilleNormal->setLabel('temps_normal');
                    }
                    $veilleNormal->setTemps($request->get('temps_normal'));

                    $veilleMedia = $em->getRepository(Veille::class) ->findOneBy(['label' => 'temps_media']);
                    if($veilleMedia === null) {
                        $veilleMedia = new Veille();
                        $veilleMedia->setLabel('temps_media');
                    }
                    $veilleMedia->setTemps($request->get('temps_media'));
                    
                    $em->persist($veilleNormal);
                    $em->persist($veilleMedia);
                    $em->flush();

                    array_push($data, [
                        "label" => $veilleNormal->getLabel(),
                        "temps" => $veilleNormal->getTemps(),
                    ]);

                    array_push($data, [
                        "label" => $veilleMedia->getLabel(),
                        "temps" => $veilleMedia->getTemps(),
                    ]);

                } else {
                    $data = ["error" => "Paramètre non valide"];
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
