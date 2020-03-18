<?php

namespace App\Controller;

use App\Entity\Authentification;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/api/user", name="api_user")
 */
class ApiUserController extends AbstractController
{
    /**
     * @Route("/login", name="user_login", methods={"POST"})
     */
    public function loginAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        if($request->get('username') != null && $request->get('password') != null) {
            $user = $em->getRepository(User::class)->findOneBy(array('username' => $request->get('username')));
            if($user != null) {
                $plainPassword = $request->get('password');
                if(password_verify($plainPassword, $user->getPassword())) {
                    //Si un token est déjà associé à l'utilisateur on en créer un nouveau
                    $authentification_exist = $em->getRepository(Authentification::class)->findOneBy(array('user' => $user->getId()));
                    if($authentification_exist){
                        $em->remove($authentification_exist);
                        $em->flush();
                    }        
                    
                    $options = [
                        'cost' => 12,
                    ];
                    $token = base64_encode(password_hash($request->get('username'), PASSWORD_BCRYPT, $options));
                    $token .= bin2hex(openssl_random_pseudo_bytes(50)); //revoir génération du token

                    $authentification = new Authentification(); 
                    $authentification->setUser($user);
                    $authentification->setToken($token);

                    $em->persist($authentification);
                    $em->flush();

                    $data = ['message' => $token];    
                } else {
                    $data = ['erreur' => 'Mot de passe invalide'];    
                }
            } else {
                $data = ['erreur' => 'Identifiant invalide'];    
            }                    
        } else {
            $data = ['erreur' => 'Mot de passe ou identifiant invalide'];
        }

        $reponse = new Response();
        $reponse->setContent(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;
    }

    /**
     * @Route("/create", name="user_create", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];
        if($request->get('username') != null && $request->get('password') != null) {
            $user_exist = $em->getRepository(User::class)->findOneBy(array('username' => $request->get('username')));
            if(!$user_exist){
                $user = new User();
                $plainPassword = $request->get('password');
                $options = [
                    'cost' => 12,
                ];
                $password = password_hash($plainPassword, PASSWORD_BCRYPT, $options);
                $user->setPassword($password);
                $user->setUsername($request->get('username'));
                $user->setIsAdmin(0);
                $em->persist($user);
                $em->flush();
                $data = ['reponse' => "user créé"];
            } else {
                $data = ['erreur' => "user existe deja "];    
            }            
        } else {
            $data = ['erreur' => 'Mot de passe ou identifiant invalide'];
        }        

        $reponse = new Response();
        $reponse->setContent(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;         
    }

    /**
     * @Route("/test", name="test", methods={"GET"})
     */
    public function test(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // $token = $request->headers->get('X-Auth-Token');
        $authentification = $em->getRepository(Authentification::class)->findOneBy(array('token' => $request->headers->get('X-Auth-Token')));
        if($authentification){
            $data = ['reponse' => "authentification réussie "];    
        } else {
            $data = ['erreur' => "authentification échouée "];    
        }

        $reponse = new Response();
        $reponse->setContent(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse; 
    }
}
