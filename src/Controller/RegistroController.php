<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistroController extends AbstractController
{
    #[Route('/registro', name: 'app_registro')]

    public function index(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $user->setBaneado(false);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($passwordEncoder->hashPassword($user, $form['password']->getData()));
            $em->persist($user);
            $em->flush();
            $this->addFlash('exito', 'Se ha registrado correctamente');
            return $this->redirectToRoute('app_registro');
        }
        return $this->render('registro/index.html.twig', [
            'controller_name' => 'RegistroController',
            'formulario' => $form->createView()
        ]);
    }
}
