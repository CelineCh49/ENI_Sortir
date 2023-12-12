<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantController extends AbstractController
{

    // Ajout du constructeur pour le password hasher pour pouvoir éditer le profil
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher=$passwordHasher;
        $this->entityManager = $entityManager;

    }
    
    #[IsGranted("ROLE_USER")]
    #[Route('/mon_profil', name: 'mon_profil')]
    public function afficherMonProfil(Request $request, EntityManagerInterface $entityManager): Response
    {
        $participant = $this->getUser(); // Récupère l'utilisateur actuellement connecté

        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                
            $participant = $form->getData();

            // Si le mot de passe est modifié
            if ($form->get('motPasse')->getData()) {
                $participant->setMotPasse($this->passwordHasher->hashPassword(
                    $participant,
                    $form->get('motPasse')->getData()
                ));
            }
            
            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès!');

            // Redirection vers le même route pour voir les modifications
            return $this->redirectToRoute('mon_profil', [
                'id'=>$participant->getId()
            ]);
        }else{
            // Si le formulaire n'est pas soumis, on rafraichit l'objet participant pour éviter les erreurs
            $entityManager->refresh($participant);
        }

        return $this->render('participant/mon_profil.html.twig', [
            'form' => $form->createView(),
            'participant'=>$participant,
        ]);
    }

    // #[IsGranted("ROLE_USER")]
    // #[Route('/profil/{id}', name: 'app_profil_view')]
    // public function view(?Participant $participant): Response
    // {  
    //     if (!$participant) {
    //         throw new NotFoundHttpException('Ressource non trouvée');
    //     }

    //     return $this->render('participant/profil.html.twig', [
    //         'participant' => $participant,
    //     ]);
    // }

        
    //Méthode pour afficher les détails du profil d'un utilisateur en fonction de son id.
    #[Route('/profil/{id}', name: 'participant_profil')]
    public function afficherProfil(?Participant $participant): Response
    {

        if (!$participant) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        return $this->render('participant/profil.html.twig', [
            'participant' => $participant,
        ]);
    }

}
