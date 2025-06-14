<?php

namespace App\Controller;

use App\Entity\Pays;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PaysController extends AbstractController
{
    #[Route('/pays', name: 'app_pays', methods: 'GET')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $pays = $entityManager->getRepository(Pays::class)->findAll();
        return $this->render('pays/index.html.twig', [
            'controller_name' => 'PaysController',
            'pays' => $pays
        ]);
    }
    #[Route('/pays', name: 'create_pays', methods: 'POST')]
    public function createProduct(EntityManagerInterface $entityManager): Response
    {
        if (isset($_POST['nom']) && !empty($_POST['nom'])) {
            $nom = trim($_POST['nom']);
            
            // Vérifier si le pays existe déjà
            $existingPays = $entityManager->getRepository(Pays::class)->findOneBy(['nom' => $nom]);
            
            if (isset($_POST['modif'])) {
                $p = $this->getPaysId($_POST['id'], $entityManager);
                if (!$p) {
                    $this->addFlash('error', 'Pays non trouvé');
                    return $this->redirectToRoute('app_pays');
                }
                
                // Si le nom est différent et qu'il existe déjà, on refuse la modification
                if ($p->getNom() !== $nom && $existingPays) {
                    $this->addFlash('error', 'Un pays avec ce nom existe déjà');
                    return $this->redirectToRoute('app_pays_update', ['id' => $p->getId()]);
                }
                
                $p->setNom($nom);
                $entityManager->flush();
                $this->addFlash('success', 'Le pays a été modifié avec succès');
                return $this->redirectToRoute('app_pays');
            }
            
            if (isset($_POST['enreg'])) {
                // Vérifier si le pays existe déjà
                if ($existingPays) {
                    $this->addFlash('error', 'Un pays avec ce nom existe déjà');
                    return $this->redirectToRoute('app_pays_add');
                }

                // Crée une nouvelle instance de Pays
                $pays = new Pays();
                $pays->setNom($nom);

                // Persiste l'objet
                $entityManager->persist($pays);
                $entityManager->flush();
                
                $this->addFlash('success', 'Le pays a été créé avec succès');
                return $this->redirectToRoute('app_pays');
            }
        }

        $this->addFlash('error', 'Le nom du pays ne peut pas être vide');
        return $this->redirectToRoute('app_pays_add');
    }
    #[Route('/add_pays', name: 'app_pays_add')]
    public function app_pays_add(): Response
    {
        return $this->render('pays/add.html.twig');
    }
    #[Route('/app_pays_update/{id}', name: 'app_pays_update')]
    public function app_pays_update(EntityManagerInterface $entityManager, int $id): Response
    {
        $pays = $this->getPaysId($id, $entityManager);
        if (!$pays) {
            $this->addFlash('error', 'Pays non trouvé');
            return $this->redirectToRoute('app_pays');
        }
        return $this->render('pays/add.html.twig', [
            'pays' => $pays
        ]);
    }
    #[Route('/pays/delete/{id}', name: 'app_pays_delete')]
    function removePays(int $id, EntityManagerInterface $entityManager): Response
    {
        $pays = $this->getPaysId($id, $entityManager);
        if (!$pays) {
            $this->addFlash('error', 'Pays non trouvé');
            return $this->redirectToRoute('app_pays');
        }
        
        $entityManager->remove($pays);
        $entityManager->flush();
        
        $this->addFlash('success', 'Le pays a été supprimé avec succès');
        return $this->redirectToRoute('app_pays');
    }

    function getPaysId($id, EntityManagerInterface $entityManager)
    {
        return $entityManager->getRepository(Pays::class)->find($id);
    }
}
