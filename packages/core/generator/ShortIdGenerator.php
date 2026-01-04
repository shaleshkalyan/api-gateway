<?php

namespace Core\ShortIdGenerator;

class ShortIdGenerator
{
    const ALPHABET = '23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ';
    const BASE = 54;

    /**
     * Encodes an integer ID into a short URL code.
     */
    public static function encode(int $id): string
    {
        if ($id === 0) {
            return self::ALPHABET[0];
        }

        $encoded = '';
        $base = self::BASE;

        while ($id > 0) {
            $encoded = self::ALPHABET[$id % $base] . $encoded;
            $id = floor($id / $base);
        }

        return $encoded;
    }

    /**
     * Decodes a short URL code back into the original integer ID.
     */
    public static function decode(string $code): int
    {
        $decoded = 0;
        $base = self::BASE;
        $alphabet = self::ALPHABET;

        for ($i = 0, $len = strlen($code); $i < $len; $i++) {
            $position = strpos($alphabet, $code[$i]);
            if ($position === false) {
                throw new \InvalidArgumentException("Invalid character in short code.");
            }
            $decoded = $decoded * $base + $position;
        }

        return $decoded;
    }
}