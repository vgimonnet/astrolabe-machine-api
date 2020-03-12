<?php

namespace App\Controller;

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
                    $data = ['message' => 'Connexion accepté'];    
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
     * @Route("/create", name="user_create", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        if($request->get('username') != null && $request->get('password') != null) {
            $user_exist = $em->getRepository(User::class)->findOneBy(array('username' => $request->get('username')));
            if($user_exist == null){
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
            }
            $data = ['reponse' => "user existe deja "];    
        }
        $data = ['reponse' => 'Mot de passe ou identifiant invalide'];

        $reponse = new Response();
        $reponse->setContent(json_encode($data));
        $reponse->headers->set("Content-Type", "application/json");
        $reponse->headers->set("Access-Control-Allow-Origin", "*");
        return $reponse;         
    }
}
