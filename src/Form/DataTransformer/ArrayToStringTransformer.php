<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ArrayToStringTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
      $jsonString = (string) json_encode($value);
      return $jsonString;
    }

    public function reverseTransform($value)
    {
      $array = json_decode($value, true);
      return $array;
    }
}