<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;


class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    public function allUser(UserRepository $repository) : JsonResponse
    {
        $users = $repository->findAll();
        
        $data = array_map(function(User $user)
        {
            return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            ];
        }, $users);

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    // public function show(User $user): JsonResponse
    // {
        // $data = [
        //     'id' => $user->getId(),
        //     'username' => $user->getUsername(),
        //     'email' => $user->getEmail(),
        //     'roles' => $user->getRoles()
        // ];
    //     return new JsonResponse($data, JsonResponse::HTTP_OK);
    // }

    public function show(int $id, EntityManagerInterface $em): Jsonresponse
    {
        $data = $em->getRepository(User::class)->find($id);

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user = setUsername($data['username']);
        $user = setEmail($data['email']);
        $user = setPassword($data['password']);
        $user = setRoles($data['roles'] ?? ['ROLE_USER']);

        $em->persist($user);
        $em->flush();

        return new JsonResponse(['status' => 'User created'], JsonResponse::HTTP_CREATED);
    }

    public function update(Request $request, User $user, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if(isset($data['Username'])){
            $user->setUsername($data['Username']);
        }

        if(isset($data['email'])){
            $user->setEmail($data['email']);
        }

        if(isset($data['roles'])){
            $user->setRoles($data['roles']);
        }

        $em->flush();

        return new JsonResponse (['status'=>'User updated'], JsonResponse::HTTP_OK);
    }

    public function delete(User $user, EntityManagerInterface $em) : Jsonresponse
    {
        $em->remove($user);
        $em->flush();

        return new JsonResponse(['status' => 'User deleted'],JsonResponse::HTTP_OK);
    }
}
