<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Entity\User;
use App\Form\PinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RequestContextAwareInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PinController extends AbstractController
{
    /**
     * @Route("/home", name="app_home")
     */
    public function index(PinRepository $pinRepo, Request $req): Response
    {
        if(!$this->isUserLoggedIn()){
            return $this->redirectToRoute('app_login');
        }
        $pins = $pinRepo->findBy([], ["createdAt" => "DESC"]);
        return $this->render('pin/index.html.twig', compact('pins'));
    }

    /**
     * @Route("/pin/create", name="app_pin_create")
     */
    public function create(Request $req, EntityManagerInterface $em): Response
    {
        if(!$this->isUserLoggedIn()){
            return $this->redirectToRoute('app_login');
        }
        $pin = new Pin;
        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $pin->setUser($this->getUser());
            $em->persist($pin);
            $em->flush();
            $this->addFlash('success', 'Pinny '.$pin->getTitle().' saved successfuly');
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
        if(!$this->isUserLoggedIn()){
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', 'Pinny '.$pin->getTitle().' updated successfuly');
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
        if(!$this->isUserLoggedIn()){
            return $this->redirectToRoute('app_login');
        }
        if($this->isCsrfTokenValid('pin_deletion_'.$pin->getId(), $req->request->get('csrf_token'))){
            $em->remove($pin);
            $em->flush();
            $this->addFlash('danger', 'Pinny '.$pin->getTitle().' deleted successfuly');
        }

        return $this->redirectToRoute('app_home');
    }

    /**
     * @Route("/pin/{id}", name="app_pin_detail")
     */
    public function detail(Pin $pin, Request $req): Response
    {
        if(!$this->isUserLoggedIn()){
            return $this->redirectToRoute('app_login');
        }
        return $this->render('pin/detail.html.twig', compact('pin'));
    }


    /**
     * This function just checks if an user is logged in, then, it redirects to the app_login root if no user is logged in yet.
     * NOTE : The $this->getUser() returns the current user from the previous session and based on token.
     *
     * @return void
     */
    private function isUserLoggedIn():bool
    {
        if(!($this->getUser())){
            $this->addFlash('danger', 'It seems like you\'re not logged in yet!');
            return false;
        }
        return true;
    }
}
