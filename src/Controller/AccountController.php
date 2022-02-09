<?php

namespace App\Controller;

use App\Form\UserEditType;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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

        if ($editForm->isSubmitted() and $editForm->isValid()){
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

    /**
     * @Route("/searchAccount", name="searchAccount")
     */
    public function searchAccount(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('keyword', TextType::class)
            ->add('search', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->get('keyword')->getData();
            return $this->redirectToRoute('viewAccountSearchResults',['keyword'=>$data]);
        }

        return $this->render('account/searchAccount.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/viewAccountSearchResults/{keyword}", name="viewAccountSearchResults")
     */
    public function viewAccountSearchResults(Request $request,$keyword): Response
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository("App\Entity\User")->findBy(array('lastname'=>$keyword));
        $users = array_merge( $users,$em->getRepository("App\Entity\User")->findBy(array('firstname'=>$keyword)));
        $users = array_merge( $users,$em->getRepository("App\Entity\User")->findBy(array('username'=>$keyword)));
        return $this->render('account/viewAccountSearchResults.html.twig', [
            'users'=>$users
        ]);
    }
}
