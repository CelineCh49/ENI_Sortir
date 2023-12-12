<?php

namespace App\Controller;

use DateTime;
use App\Entity\Sortie;
use App\Data\SearchData;
use App\Form\SearchForm;
use App\Form\SortieType;
use App\Entity\Participant;
use App\Form\AnnulationSortieType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfToken;


class SortieController extends AbstractController
{
        
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CsrfTokenManagerInterface $csrfTokenManager,
        private EtatRepository $etatRepository
    ) {
        $this->entityManager = $entityManager;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->etatRepository = $etatRepository;
    }

    #[Route('/', name: 'app_sortie_index', methods: ['GET', 'POST'])]
    public function index(SortieRepository $sortieRepository, Request $request): Response
    {
        $data = new SearchData();
        $filterForm = $this->createForm(SearchForm::class, $data);
        $filterForm->handleRequest($request);
        $sorties = $sortieRepository->findSearch($data, $this->getUser());
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
            'form' =>$filterForm->createView(),
            
        ]);
    }

    #[Route('/sortie/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, 
    EntityManagerInterface $entityManager, 
    VilleRepository $villeRepository, 
    EtatRepository $etatRepository, 
    LieuRepository $lieuRepository): Response
    {
        $sortie = new Sortie();
        $organisateur = $this->getUser();

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $nom = $form->get('nom')->getData();
            $sortie->setNom($nom);
            
            $dateHeureDebut = $form->get('dateHeureDebut')->getData();
            $sortie->setDateHeureDebut($dateHeureDebut);
            
            $duree = $form->get('duree')->getData();
            $sortie->setDuree($duree);
            
            $dateLimiteInscription = $form->get('dateLimiteInscription')->getData();
            $sortie->setDateLimiteInscription($dateLimiteInscription);
            
            $nbInscriptionsMax = $form->get('nbInscriptionsMax')->getData();
            $sortie->setNbInscriptionsMax($nbInscriptionsMax);
            
            $infosSortie = $form->get('infosSortie')->getData();
            $sortie->setInfosSortie($infosSortie);
    
            $sortie->setOrganisateur($organisateur);
    
            $sortie->setCampus($sortie->getOrganisateur()->getCampus());
    
            $libelle='Créée';
            $etat = $etatRepository->findOneBy(['libelle' => $libelle]);
            $sortie->setEtat($etat);
    
            $lieuId = $form->get('lieu')->getData();
            $lieu = $lieuRepository->find($lieuId);
            $sortie->setLieu($lieu);
            
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Votre sortie est créée');

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/sortie/{id}', name: 'app_sortie_show', methods: ['GET'])]
    public function show(Sortie $sortie): Response
    {
        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée.');
        }

        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/sortie/{id}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        // -------On ne peut éditer que les sorties que l'on a créé
        if ($this->getUser() !== $sortie->getOrganisateur()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cette sortie.');
        }

        //----------dans l'affichage, récupérer la ville 
        

        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/sortie/{id}', name: 'app_sortie_delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) 
        {
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/sortie/inscription/{id}', name: 'sortie_inscription', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function inscriptionSortie(
        Request $request, 
        Sortie $sortie, 
        EtatRepository $etatRepository, 
        CsrfTokenManagerInterface $csrfTokenManager, 
        EntityManagerInterface $entityManager): RedirectResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isCsrfTokenValid('inscription_sortie', $request->request->get('_csrf_token'))) 
        {
            throw new AccessDeniedException('Jeton CSRF invalide.');
        }

        $participantConnecte = $this->getUser();

        // Vérification de la validité du jeton CSRF
        $token = new CsrfToken('inscription_sortie', $request->request->get('_csrf_token'));
        if (!$csrfTokenManager->isTokenValid($token)) 
        {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        // Si le participant peut s'inscrire, alors il est inscrit et un message de succès est affiché
        if ($this->canInscribe($sortie, $participantConnecte)) 
        {
            $this->handleInscription($sortie, $participantConnecte, $etatRepository, $entityManager);
            $this->addFlash('success', 'Vous êtes bien inscrit/e pour la sortie ' . $sortie->getNom() . ' !');
        } else {
            // Sinon, un message d'erreur est affiché
            $this->addFlash('error', 'Vous ne pouvez pas vous inscrire pour la sortie ' . $sortie->getNom() . ' !');
        }
        return $this->handleRedirection($request);
    }

    #[Route('/sortie/desistement/{id}', name: 'sortie_desistement', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function desistementSortie(
        Request $request, 
        Sortie $sortie, 
        EtatRepository $etatRepository, 
        CsrfTokenManagerInterface $csrfTokenManager, 
        EntityManagerInterface $entityManager): RedirectResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isCsrfTokenValid('desistement_sortie', $request->request->get('_csrf_token'))) 
        {
            throw new AccessDeniedException('Jeton CSRF invalide.');
        }

        $participantConnecte = $this->getUser();
        // Vérification de la validité du jeton CSRF
        $token = new CsrfToken('desistement_sortie', $request->request->get('_csrf_token'));
        if (!$csrfTokenManager->isTokenValid($token)) 
        {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        // Si le participant peut se désinscrire, alors il est désinscrit et un message de succès est affiché
        if ($this->canDesist($sortie, $participantConnecte)) 
        {
            $this->handleDesistement($sortie, $participantConnecte, $etatRepository, $entityManager);
            $this->addFlash('success', 'Vous êtes bien désinscrit/e pour la sortie ' . $sortie->getNom() . ' !');
        } else {
            // Sinon, un message d'erreur est affiché
            $this->addFlash('error', 'Vous ne pouvez pas vous désinscrire pour la sortie ' . $sortie->getNom() . ' !');
        }
        return $this->handleRedirection($request);
    }

    // Vérifie si le participant peut s'inscrire à la sortie
    private function canInscribe(Sortie $sortie, Participant $participant): bool {
        return $sortie->getEtat()->getLibelle() == 'Ouverte'
            && $sortie->getDateLimiteInscription() >= new DateTime('now')
            && !$sortie->getInscrits()->contains($participant);
    }

    // Vérifie si le participant peut se désinscrire de la sortie
    private function canDesist(Sortie $sortie, Participant $participant): bool {
        return $sortie->getDateHeureDebut() >= new DateTime('now')
            && $sortie->getInscrits()->contains($participant);
    }

    // Gère l'inscription du participant à la sortie
    private function handleInscription(
        Sortie $sortie, 
        Participant $participant, 
        $etatRepository, 
        $entityManager): void 
    {
        $sortie->addInscrit($participant);
        if (count($sortie->getInscrits()) == $sortie->getNbInscriptionsMax()) {
            $etatCloture = $etatRepository->findbyLibelle('Clôturée');
            $sortie->setEtat($etatCloture);
        }
        $entityManager->persist($sortie);
        $entityManager->flush();
    }

    // Gère la désinscription du participant de la sortie
    private function handleDesistement(Sortie $sortie, Participant $participant, $etatRepository, $entityManager): void {
        $sortie->removeInscrit($participant);
        if (count($sortie->getInscrits()) < $sortie->getNbInscriptionsMax() && $sortie->getDateLimiteInscription() >= new DateTime('now')) {
            $etatOuvert = $etatRepository->findbyLibelle('Ouverte');
            $sortie->setEtat($etatOuvert);
        }
        $entityManager->persist($sortie);
        $entityManager->flush();
    }

    // Redirige vers l'URL précédente ou vers la liste des sorties si l'URL précédente n'est pas disponible
    private function handleRedirection(Request $request): Response {
        $url = $request->headers->get('referer');
        if ($url) {
            return $this->redirect($url);
        } else {
            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/sortie/publier/{id}', name: 'sortie_publier', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function publierSortie(
        Request $request, 
        Sortie $sortie, 
        EtatRepository $etatRepository, 
        CsrfTokenManagerInterface $csrfTokenManager, 
        EntityManagerInterface $entityManager): RedirectResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isCsrfTokenValid('publier_sortie', $request->request->get('_csrf_token'))) 
        {
            throw new AccessDeniedException('Jeton CSRF invalide.');
        }

        $participantConnecte = $this->getUser();

        // -------------a voir
        $token = new CsrfToken('publier_sortie', $request->request->get('_csrf_token'));
        if (!$csrfTokenManager->isTokenValid($token)) 
        {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        // ----------------

        if ($participantConnecte == $sortie->getOrganisateur()) 
        {
            $this->handlePublication($sortie,$etatRepository, $entityManager);
            $this->addFlash('success', 'Votre sortie est publiée ');
            
        } else {
            // Sinon, un message d'erreur est affiché
            $this->addFlash('error', 'Vous ne pouvez pas publier cette sortie ');
        }
        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
      
    }
    private function handlePublication(Sortie $sortie, $etatRepository, EntityManagerInterface $entityManager): void 
    {
        $etatOuvert = $etatRepository->findbyLibelle('Ouverte');
        $sortie->setEtat($etatOuvert);

        $entityManager->persist($sortie);
        $entityManager->flush();
    }
    #[Route('/cancel/{id}', name: 'app_sortie_cancel')]
    public function cancelSortie(int $id, Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository): Response
    {
        $sortie=$sortieRepository->find($id);
    
        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }
    
        $user = $this->getUser();
    
        // Vérification si l'organisateur et si la sortie peut être annulée
        if ($sortie->getOrganisateur() !== $user || $sortie->getDateHeureDebut() <= new \DateTime()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas annuler cette sortie');
        }
    
        // Vérification si la sortie peut être annulée (jusqu'à 1 mois après la date prévue)
        $dateLimiteAnnulation = (new \DateTime())->modify('-1 month');
        if ($sortie->getDateHeureDebut() <= $dateLimiteAnnulation) {
            throw $this->createAccessDeniedException('La sortie ne peut plus être annulée');
        }
    
        // Créez une instance du formulaire
        $form = $this->createForm(AnnulationSortieType::class, $sortie);
    
        // Gérez la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Effectuez l'annulation de la sortie
            $sortie->setEtat($this->etatRepository->findOneBy(['libelle' => 'Annulée']));
            $sortie->setInfosSortie($form->get('infosSortie')->getData());
    
            $entityManager->flush();
    
            $this->addFlash('success', 'La sortie a été annulée avec succès');
    
            return $this->redirectToRoute('app_sortie_show', ['id' => $id]);
        }
    
        return $this->render('sortie/_cancel_form.html.twig', [
            'form' => $form->createView(),
            'sortie' => $sortie,
        ]);
    }
 
    

}



