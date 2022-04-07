<?php

namespace App\Controller;

use App\Entity\Matiere;
use App\Form\MatiereType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/{_locale}')]
class MatiereController extends AbstractController
{
    #[Route('/matiere', name: 'app_matiere')]
    public function index(ManagerRegistry $doctrine, Request $request, TranslatorInterface $translator): Response
    {
        $em = $doctrine->getManager();

        $matiere = new Matiere();
        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em->persist($matiere);
            $em->flush();

            $this->addFlash('success', $translator->trans('matiere.ajoute'));
        }

        $matieres = $em->getRepository(matiere::class)->findAll();

        return $this->render('matiere/index.html.twig', [
            'matieres' => $matieres,
            'ajoutMatiere' => $form->createView()
        ]);
    }

    #[Route('/matiere/{id}', name:'matiere_edit')]
    public function edit(Matiere $matiere = null, ManagerRegistry $doctrine, Request $request, TranslatorInterface $translator){
        if($matiere == null){
            $this->addFlash('danger', $translator->trans('matiere.introuvable'));
            return $this->redirectToRoute('matiere');
        }

        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $doctrine->getManager();
            $em->persist($matiere);
            $em->flush();

            $this->addFlash('success', $translator->trans('matiere.maj'));
        }

        return $this->render('matiere/edit.html.twig', [
            'matiere' => $matiere,
            'edit' => $form->createView()
        ]);
    }

    #[Route('/matiere/delete/{id}', name:'matiere_delete')]
    public function delete(Matiere $matiere = null, ManagerRegistry $doctrine, TranslatorInterface $translator){
        if($matiere == null){
            $this->addFlash('danger', $translator->trans('matiere.introuvable'));
            return $this->redirectToRoute('matiere');
        }

        $em = $doctrine->getManager();
        $em->remove($matiere);
        $em->flush();

        $this->addFlash('warning', $translator->trans('matiere.supprimee'));

        return $this->redirectToRoute('app_matiere');
    }
}
