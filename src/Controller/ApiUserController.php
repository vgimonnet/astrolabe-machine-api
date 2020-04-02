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
                    $authentification = null;

                    //Si un token est déjà associé à l'utilisateur on le modifie
                    $authentification_exist = $em->getRepository(Authentification::class)->findOneBy(array('user' => $user->getId()));
                    if($authentification_exist){
                        $authentification = $authentification_exist;
                    } else {
                        $authentification = new Authentification(); 
                        $authentification->setUser($user);
                    }
                    
                    $options = [
                        'cost' => 12,
                    ];
                    $token = base64_encode(password_hash($request->get('username'), PASSWORD_BCRYPT, $options));
                    $token .= bin2hex(openssl_random_pseudo_bytes(50)); //revoir génération du token

                    $authentification->setToken($token);                    

                    $em->persist($authentification);
                    $em->flush();

                    $data = ['token' => $token];    
                } else {
                    $data = ['error' => 'Mot de passe invalide'];    
                }
            } else {
                $data = ['error' => 'Identifiant invalide'];    
            }                    
        } else {
            $data = ['error' => 'Mot de passe ou identifiant invalide'];
        }

        $reponse = new Response();
        $reponse->setContent(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;
    }

    /**
     * @Route("/create", name="user_create", methods={"POST"}, options={"expose"=false})
     */
    // public function createAction(Request $request)
    // {
    //     $em = $this->getDoctrine()->getManager();
    //     $data = [];
    //     if($request->get('username') != null && $request->get('password') != null) {
    //         $user_exist = $em->getRepository(User::class)->findOneBy(array('username' => $request->get('username')));
    //         if(!$user_exist){
    //             $user = new User();
    //             $plainPassword = $request->get('password');
    //             $options = [
    //                 'cost' => 12,
    //             ];
    //             $password = password_hash($plainPassword, PASSWORD_BCRYPT, $options);
    //             $user->setPassword($password);
    //             $user->setUsername($request->get('username'));
    //             $user->setIsAdmin(0);
    //             $em->persist($user);
    //             $em->flush();
    //             $data = [
    //                 'id' => $user->getId(),
    //                 'username' => $user->getUsername(),
    //             ];
    //         } else {
    //             $data = ['error' => "user existe deja "];    
    //         }            
    //     } else {
    //         $data = ['error' => 'Mot de passe ou identifiant invalide'];
    //     }        

    //     $reponse = new Response();
    //     $reponse->setContent(json_encode($data));
    //     $reponse->headers->set("Content-Type", "application/json");
    //     $reponse->headers->set("Access-Control-Allow-Origin", "*");
    //     return $reponse;         
    // }

    /**
     * @Route("/changepassword", name="user_changepassword", methods={"POST"})
     */
    public function changePassword(Request $request){
        $data = [];
        $em = $this->getDoctrine()->getManager();

        if($request->headers->get('X-Auth-Token') !== null) {
            $authentication = $em->getRepository(Authentification::class)->findOneBy(["token" => $request->headers->get('X-Auth-Token')]);
            if($authentication !== null) {
                if ($request->get('password') !== null && $request->get('newpassword') !== null) {
                    $user = $authentication->getUser();                
                    if(password_verify($request->get('password'), $user->getPassword())) {
                        $options = [
                            'cost' => 12,
                        ];
                        $password = password_hash($request->get('newpassword'), PASSWORD_BCRYPT, $options);
                        $user->setPassword($password);
                        $em->persist($user);
                $em->flush();
                        $data = ['success' => 'Mot de passe changé avec succès'];
                    } else {
                        $data = ['error' => 'Mot de passe incorrect'];
                    }
                } else {
                    $data = ["error" => "Le mot de passe actuel ou le nouveau mot de passe est invalide"];
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
