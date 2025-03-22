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
            $nom = $_POST['nom'];
            if (isset($_POST['modif'])) {
                $p = $this->getPaysId($_POST['id'], $entityManager);
                if (!$p) {
                    throw new \Exception("Pays non trouve");
                } else {
                    $p->setNom($nom);
                }
                $entityManager->flush();
                return $this->redirect('/pays');
            }
            if (isset($_POST['enreg'])) {

                // Crée une nouvelle instance de Pays
                $pays = new Pays();
                $pays->setNom($nom);

                // Persiste l'objet
                $entityManager->persist($pays);
                $entityManager->flush();
                return $this->redirect('/pays');
            }
        }

        return $this->render('pays/add.html.twig', [
            'message' => "Nom du pays est vide"
        ]);
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
        return $this->render('pays/add.html.twig', [
            'pays' => $pays
        ]);
    }
    #[Route('/pays/delete/{id}', name: 'app_pays_delete')]
    function removePays(int $id, EntityManagerInterface $entityManager):Response
    {
        $pays = $this->getPaysId($id, $entityManager);
        $entityManager->remove($pays);
        $entityManager->flush();
        return $this->redirect('/pays');

    }

    function getPaysId($id, EntityManagerInterface $entityManager)
    {
        return $entityManager->getRepository(Pays::class)->find($id);
    }
}
