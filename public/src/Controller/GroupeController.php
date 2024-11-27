<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupeController extends AbstractController
{
#[Route('/user', name: 'user_liste')]
public function index(UserRepository $repository): Response
{
$utilisateurs = $repository->findAll();

return $this->render('user/index.html.twig', [
'user' => $utilisateurs,
]);
}
}
