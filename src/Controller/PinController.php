<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/pin/{id}", name="app_pin_detail")
     */
    public function detail(Pin $pin): Response
    {
        return $this->render('pin/detail.html.twig', compact('pin'));
    }
}
