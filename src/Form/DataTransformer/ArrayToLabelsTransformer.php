<?php

namespace App\Form\DataTransformer;


use App\Entity\Therapy\Label;
use App\Entity\Therapy\Stub;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ArrayToLabelsTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function transform(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        return $value;
    }

    public function reverseTransform(mixed $value): mixed
    {
        if (!$value) {
            dump($value);
            return null;
        } elseif (is_array($value)) {
            dump($value);
        } else {
            dump($value);
        }

        /*$label = $this->entityManager
            ->getRepository(Label::class)
            ->find($value->getId());

        if ($label === null) {
            throw new TransformationFailedException(sprintf(
                'Label "%s" does not exist!',
                $value
            ));
        }

        return $label->getShortName();*/
        return $value;
    }
}