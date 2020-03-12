<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/api/user", name="api_user")
 */
class ApiUserController extends AbstractController
{
    /**
     * @Route("/", name="api_user_index")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiUserController.php',
        ]);
    }

    /**
     * @Route("/login", name="user_login", methods={"POST"})
     */
    public function loginAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if($request->get('username') != null && $request->get('password') != null) {
            $user = $em->getRepository(User::class)->findOneBy(array('username' => $request->get('username'), 'password' => $request->get('password')));
            if($user != null) {
                $plainPassword = $request->get('password');
                $options = [
                    'cost' => 12,
                ];
                if(password_verify($plainPassword, password_hash($plainPassword, PASSWORD_BCRYPT, $options))) {
                    return $this->json(['message' => 'Connexion accepté']);    
                } else {
                    return $this->json(['error' => 'Mot de passe ou identifiant invalide 3']);    
                }
            } else {
                return $this->json(['error' => 'Mot de passe ou identifiant invalide 2']);    
            }                    
        } else {
            return $this->json(['error' => 'Mot de passe ou identifiant invalide 1']);
        }
    }

    /**
     * @Route("/create", name="user_create", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

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
                return $this->json(['reponse' => "user créé"]);
            }
            return $this->json(['reponse' => "user existe deja "]);    

        }
        

            

        return $this->json(['reponse' => 'Mot de passe ou identifiant invalide']);

            // dump($user_exist);
            // die;

            // $encoded = 
            // $user = $em->getRepository(UserRepository::class)->findByUsernameAndPassword($request->get('username'), $password);
        // } else {
            
        // }
    }

    

}
