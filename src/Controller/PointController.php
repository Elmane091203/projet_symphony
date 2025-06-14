<?php

namespace App\Controller;

use App\Entity\Point;
use App\Entity\Zone;
use App\Form\PointType;
use App\Repository\PointRepository;
use App\Repository\ZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/point')]
class PointController extends AbstractController
{
    #[Route('/', name: 'app_point_index', methods: ['GET'])]
    public function index(PointRepository $pointRepository): Response
    {
        return $this->render('point/index.html.twig', [
            'points' => $pointRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_point_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ZoneRepository $zoneRepository): Response
    {
        $point = new Point();
        $form = $this->createForm(PointType::class, $point);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier le nombre de points dans la zone
            $zone = $point->getZone();
            $nombrePoints = count($zone->getPoints());
            
            if ($nombrePoints >= 4) {
                $this->addFlash('error', 'Une zone ne peut pas avoir plus de 4 points de surveillance.');
                return $this->render('point/new.html.twig', [
                    'point' => $point,
                    'form' => $form,
                    'zones' => $zoneRepository->findAll(),
                ]);
            }

            $entityManager->persist($point);
            $entityManager->flush();

            $this->addFlash('success', 'Le point a été créé avec succès.');
            return $this->redirectToRoute('app_point_index');
        }

        return $this->render('point/new.html.twig', [
            'point' => $point,
            'form' => $form,
            'zones' => $zoneRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_point_show', methods: ['GET'])]
    public function show(Point $point): Response
    {
        return $this->render('point/show.html.twig', [
            'point' => $point,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_point_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Point $point, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PointType::class, $point);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le point a été modifié avec succès.');
            return $this->redirectToRoute('app_point_index');
        }

        return $this->render('point/edit.html.twig', [
            'point' => $point,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_point_delete', methods: ['POST'])]
    public function delete(Request $request, Point $point, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$point->getId(), $request->request->get('_token'))) {
            $entityManager->remove($point);
            $entityManager->flush();
            $this->addFlash('success', 'Le point a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_point_index');
    }
} 