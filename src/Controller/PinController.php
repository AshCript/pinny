<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(PinRepository $pinRepo): Response
    {
        $pins = $pinRepo->findBy([], ["createdAt" => "DESC"]);
        return $this->render('pin/index.html.twig', compact('pins'));
    }

    /**
     * @Route("/pin/create", name="app_pin_create")
     */
    public function create(Request $req, EntityManagerInterface $em): Response
    {
        $pin = new Pin;
        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($pin);
            $em->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->render('pin/form.html.twig', [
            'pinForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/pin/{id}/edit", name="app_pin_edit")
     */
    public function update(Pin $pin, Request $req, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->render('pin/form.html.twig', [
            'pinForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/pin/{id}/delete", name="app_pin_delete")
     */
    public function delete(Pin $pin, Request $req, EntityManagerInterface $em): Response
    {
        if($this->isCsrfTokenValid('pin_deletion_'.$pin->getId(), $req->request->get('csrf_token'))){
            $em->remove($pin);
            $em->flush();
        }

        return $this->redirectToRoute('app_home');
    }

    /**
     * @Route("/pin/{id}", name="app_pin_detail")
     */
    public function detail(Pin $pin): Response
    {
        return $this->render('pin/detail.html.twig', compact('pin'));
    }

}
