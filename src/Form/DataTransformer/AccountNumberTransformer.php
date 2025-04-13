<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class AccountNumberTransformer implements DataTransformerInterface
{
    public function transform($value): mixed
    {
        if (null === $value) {
            return '';
        }

        return $value;
    }

    public function reverseTransform($value): mixed
    {
        if (null === $value) {
            return null;
        }

        // Remove any dashes from the account number
        $cleanNumber = str_replace('-', '', $value);

        // If length is less than 18, pad with zeros after the first 3 digits
        if (strlen($cleanNumber) < 18) {
            $prefix = substr($cleanNumber, 0, 3);
            $rest = substr($cleanNumber, 3);
            $paddedRest = str_pad($rest, 15, '0', STR_PAD_LEFT);
            $cleanNumber = $prefix.$paddedRest;
        }

        return $cleanNumber;
    }
}
