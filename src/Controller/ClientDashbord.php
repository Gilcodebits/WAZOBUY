<?php
// src/Controller/ClientDashbord.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientDashbord extends AbstractController
{
    #[Route('/client/dashbord', name: 'app_client_dashbord')]
    public function index(): Response
    {
        return $this->render('clientdashbord/clientdashbord.html.twig');
    }
}
?>