<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use App\Repository\UserRepository;

class UserProvider implements UserProviderInterface
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userRepository->findOneBy(['email' => $identifier]);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Email could not be found.');
        }

        if (!$user->isActive()) {
            throw new CustomUserMessageAuthenticationException('Your account is not active.');
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof \App\Entity\User) {
            throw new UnsupportedUserException('Invalid user class.');
        }

        $reloadedUser = $this->userRepository->find($user->getId());

        if (!$reloadedUser) {
            throw new CustomUserMessageAuthenticationException('User could not be reloaded.');
        }

        return $reloadedUser;
    }

    public function supportsClass(string $class): bool
    {
        return \App\Entity\User::class === $class || is_subclass_of($class, \App\Entity\User::class);
    }

    // Deprecated method, kept for backward compatibility
    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }
}