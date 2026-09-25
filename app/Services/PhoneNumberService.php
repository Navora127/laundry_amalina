<?php

namespace App\Services;

use InvalidArgumentException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class PhoneNumberService
{
    private readonly PhoneNumberUtil $phoneUtil;

    public function __construct()
    {
        $this->phoneUtil = PhoneNumberUtil::getInstance();
    }

    public function normalize(string $phone, string $defaultRegion = 'ID'): string
    {
        $phone = trim($phone);

        try {
            $parsed = $this->phoneUtil->parse($phone, $defaultRegion);
        } catch (NumberParseException $exception) {
            throw new InvalidArgumentException('Nomor telepon tidak valid.', previous: $exception);
        }

        if (! $this->phoneUtil->isValidNumber($parsed)) {
            throw new InvalidArgumentException('Nomor telepon tidak valid.');
        }

        return $this->phoneUtil->format($parsed, PhoneNumberFormat::E164);
    }

    public function isValid(string $phone, string $defaultRegion = 'ID'): bool
    {
        try {
            $this->normalize($phone, $defaultRegion);

            return true;
        } catch (InvalidArgumentException) {
            return false;
        }
    }
}
