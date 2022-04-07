<?php

namespace App\Controller;

use App\Entity\Matiere;
use App\Entity\Note;
use App\Form\NoteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/{_locale}')]
class NoteController extends AbstractController
{
    #[Route('/note', name: 'app_note')]
    public function index(ManagerRegistry $doctrine, Request $request, TranslatorInterface $translator): Response
    {
        $em = $doctrine->getManager();

        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        dump($form);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($note);
            $em->flush();

            $this->addFlash('success', $translator->trans('note.ajoute'));
        }

        $notes = $em->getRepository(note::class)->findAll();
        $matiere = $em->getRepository(Matiere::class)->findAll();

        return $this->render('note/index.html.twig', [
            'notes' => $notes,
            'matiere' => $matiere,
            'ajoutNote' => $form->createView()
        ]);
    }

    #[Route('/note/{id}', name:'note_edit')]
    public function edit(Note $note = null, ManagerRegistry $doctrine, Request $request, TranslatorInterface $translator){
        if($note == null){
            $this->addFlash('danger', $translator->trans('note.introuvable'));
            return $this->redirectToRoute('note');
        }

        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $doctrine->getManager();
            $em->persist($note);
            $em->flush();

            $this->addFlash('success', $translator->trans('note.ajoute'));
        }

        return $this->render('note/edit.html.twig', [
            'note' => $note,
            'edit' => $form->createView()
        ]);
    }

    #[Route('/note/delete/{id}', name:'note_delete')]
    public function delete(Note $note = null, ManagerRegistry $doctrine, TranslatorInterface $translator){
        if($note == null){
            $this->addFlash('danger', $translator->trans('note.ajoute'));
            return $this->redirectToRoute('note');
        }

        $em = $doctrine->getManager();
        $em->remove($note);
        $em->flush();

        $this->addFlash('warning', $translator->trans('note.ajoute'));

        return $this->redirectToRoute('app_note');
    }
}
