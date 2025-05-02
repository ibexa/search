<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Search\Form\DataTransformer;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforms between a User's ID and a domain specific User object.
 *
 * @phpstan-implements \Symfony\Component\Form\DataTransformerInterface<User, int>
 */
class UserTransformer implements DataTransformerInterface
{
    protected UserService $userService;

    /**
     * @param \Ibexa\Contracts\Core\Repository\UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Transforms a domain specific User object into a Users's ID.
     */
    public function transform(mixed $value): ?int
    {
        if (null === $value) {
            return null;
        }

        return $value->id;
    }

    /**
     * Transforms a Users's ID integer into a domain specific User object.
     */
    public function reverseTransform(mixed $value): ?User
    {
        if (empty($value)) {
            return null;
        }

        try {
            return $this->userService->loadUser((int)$value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
