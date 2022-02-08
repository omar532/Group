<?php

namespace App\Controller;

use App\Form\UserEditType;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AccountController extends AbstractController
{
    /**
     * @Route("/editAccount/{id}", name="editAccount")
     */
    public function editAccount(Request $req,$id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("App\Entity\User")->findOneBy(['username'=>$id]);

        $editForm = $this->createForm(UserEditType::class,$user);
        $editForm->handleRequest($req);

        if ($editForm->isSubmitted() && $editForm->isValid()){
            $editData=$editForm->getData();
            $em->persist($editData);
            $em->flush();
            return $this->redirectToRoute('viewAccount');
        }

        return $this->render('account/editAccount.html.twig', [
            'editUserForm' => $editForm->createView()
        ]);
    }

    /**
     * @Route("/viewAccount", name="viewAccount")
     */
    public function viewAccount(): Response
    {


        $em = $this->getDoctrine()->getManager();
        $un = $this->get('security.token_storage')->getToken()->getUser()->getUsername();

        $user = $em->getRepository("App\Entity\User")->findOneBy(['username'=>$un]);

        return $this->render('account/viewAccount.html.twig', [
            'user' => $user
        ]);
    }
}
