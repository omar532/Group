<?php

namespace App\Controller;

use App\Entity\Publication;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PublicationType;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Common\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;




class PublicationController extends AbstractController
{
    /**
     * @Route("/publication", name="publication")
     */
    public function index(): Response
    {
        $em= $this->getDoctrine()->getManager();



        $query = $em->createQuery('SELECT p FROM App\Entity\Publication p WHERE p.usr_Id = :num ')
            ->setParameter('num', $this->getUser()->getUserIdentifier());



        $publication = $em->getRepository(Publication::class)->findAll();

        return $this->render('publication/index.html.twig', [
            'ListPublications' => $publication ,'User'=>$this->getUser()->getUsername() , 'user_Id' => $this->getUser()->getUserIdentifier()
        ]);
    }


    /**
     * @Route("/addPublication", name="add_Publication")
     */
    public function addPublication(Request $request): Response
    {
        $Publication = new Publication();
        $form = $this->createForm(PublicationType::class, $Publication);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
            $em= $this->getDoctrine()->getManager();
            $Publication->setUsrId($this->getUser());
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['imageFile']->getData();
            $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
          $h=$uploadedFile->getClientOriginalName();

            $uploadedFile->move(
                $destination,
                $uploadedFile->getClientOriginalName()
            );



            $Publication->setPhotoUrl($h);
            $date = new \DateTime();
            $Publication->setDateCreation($date);

            $em->persist($Publication);
            $em->flush();

            return $this->redirectToRoute('publication');
        }

        return $this->render('publication/add.html.twig', [
            'formPublication'=>$form->createView()
        ]);
    }

    /**
     * @Route("/deletePublication/{id}", name="PublicationDelete")
     */
    public function deletePublication($id): Response
    {
        $em= $this->getDoctrine()->getManager();
        $publication = $em->getRepository("App\Entity\Publication")->find($id);

        if($publication!== null){

            $em->remove($publication);
            $em->flush();

        }else{
            throw new NotFoundHttpException("La publication d'id ".$id."n'existe pas");
        }

        return $this->redirectToRoute('publication');
    }

    /**
     * @Route("/updatePublication/{id}", name="PublicationUpdate")
     */
    public function updatePublication(Request $request, $id): Response
    {

        $em= $this->getDoctrine()->getManager();
        $publication = $em->getRepository("App\Entity\Publication")->find($id);

        $editform = $this->createForm(PublicationType::class, $publication);

        $editform->handleRequest($request);

        if($editform->isSubmitted() and $editform->isValid()){
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $editform['imageFile']->getData();
            $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
            $h=$uploadedFile->getClientOriginalName();

            $uploadedFile->move(
                $destination,
                $uploadedFile->getClientOriginalName()
            );



            $publication->setPhotoUrl($h);
            $date = new \DateTime();
            $publication->setDateCreation($date);



            $em->persist($publication);
            $em->flush();

            return $this->redirectToRoute('publication');
        }

        return $this->render('publication/update.html.twig', [
            'editFormPublication'=>$editform->createView()
        ]);
    }



}
