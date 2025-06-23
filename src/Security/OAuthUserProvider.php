<?php

namespace App\Security;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class OAuthUserProvider implements OAuthAwareUserProviderInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $email = $response->getEmail();
        $socialId = $response->getNickname();

        $user = $this->em
            ->getRepository(Utilisateur::class)
            ->findOneBy(['socialId' => $socialId]);

        if (!$user) {
            $user = new Utilisateur();
            $user->setEmail($email);
            $user->setPrenom($response->getFirstName());
            $user->setNom($response->getLastName());
            $user->setSocialId($socialId);
            $user->setRoles(['ROLE_CLIENT']);
            $user->setPassword(''); // Pas de mot de passe pour les utilisateurs sociaux

            $this->em->persist($user);
            $this->em->flush();
        }

        return $user;
    }

    public function loadUser($username)
    {
        return $this->em
            ->getRepository(Utilisateur::class)
            ->findOneBy(['email' => $username]);
    }
}
