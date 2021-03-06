<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        // $repo = $this->getDoctrine()->getRepository(Ad::class);

        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }


      /**
     * Permet de créer une annonce
     * @Route("/ads/new", name="ads_create")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager){
        $ad = new Ad();
        /*
        $image1 = new Image();

        $image1->setUrl('http://placehold.it/400x200')
            ->setCaption('Titre 1');

        $ad->addImage($image1);

        $image2 = new Image();
        
        $image2->setUrl('http://placehold.it/400x200')
            ->setCaption('Titre 2');

        $ad->addImage($image2);    
        */
        $form = $this->createForm(AnnonceType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser());

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée ! "
            );

            return $this->redirectToRoute('ads_show',[
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/new.html.twig', [
           'myForm' => $form->createView()
        ]);

    }

    /**
     * Permet d'afficher le formulaire d'édition
     * 
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @Security("(is_granted('ROLE_USER') and user === ad.getAuthor()) or is_granted('ROLE_ADMIN')", message="Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier")
     * @return Response
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager){

        $form = $this->createForm(AnnonceType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);

            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été modifiée"
            );

            return $this->redirectToRoute('ads_show',[
                'slug' => $ad->getSlug()
            ]);




        }

        return $this->render('ad/edit.html.twig',[
            'ad' => $ad,
            "myForm" => $form->createView()
        ]);


    }



    /**
     * @Route("/ads/{slug}", name="ads_show")
     *
     * @return Response
     */
    public function show($slug, Ad $ad){

        //$ad = $repo->findOneBySlug($slug);

        return $this->render('ad/show.html.twig',[
          'ad' => $ad
        ]);

    }


    /**
     * Permet de supprimer une annonce 
     * @Route("/ads/{slug}/delete", name="ads_delete")
     * @Security("(is_granted('ROLE_USER') and user === ad.getAuthor()) or is_granted('ROLE_ADMIN')", message="Cette annonce ne vous appartient pas, vous ne pouvez pas la supprimer")
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Ad $ad, EntityManagerInterface $manager){
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
            "success",
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée"
        );

        return $this->redirectToRoute("ads_index");
    }


  
}
