<?php

namespace App\Services\Plagiarism;

class FingerprintService
{
    public function normalizeText(string $text): string
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[^\w\s]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    public function tokenize(string $text): array
    {
        return preg_split('/\s+/', $this->normalizeText($text));
    }

    public function shingle(array $tokens, int $k = 5): array
    {
        $shingles = [];
        $count = count($tokens);
        for ($i = 0; $i <= $count - $k; $i++) {
            $shingles[] = array_slice($tokens, $i, $k);
        }
        return $shingles;
    }

    public function hashShingle(array $shingle): int
    {
        $text = implode(' ', $shingle);
        $hash = 0x811c9dc5;
        $len = strlen($text);
        for ($i = 0; $i < $len; $i++) {
            $hash ^= ord($text[$i]);
            $hash *= 0x01000193;
            $hash &= 0xFFFFFFFF;
        }
        return $hash;
    }

    public function fingerprintText(string $text): array
    {
        $tokens = $this->tokenize($text);
        if (count($tokens) < 10) return [];

        $shingles = $this->shingle($tokens, 5);
        $hashes = array_map([$this, 'hashShingle'], $shingles);

        $windowSize = 4;
        $fingerprint = [];
        for ($i = 0; $i <= count($hashes) - $windowSize; $i++) {
            $window = array_slice($hashes, $i, $windowSize);
            $minHash = min($window);
            $minIndex = $i + array_search($minHash, $window);
            $fingerprint[$minIndex] = $minHash;
        }

        return array_values(array_unique($fingerprint));
    }

    public function jaccardSimilarity(array $fp1, array $fp2): float
    {
        if (empty($fp1) && empty($fp2)) return 0.0;
        $intersection = count(array_intersect($fp1, $fp2));
        $union = count(array_unique(array_merge($fp1, $fp2)));
        return $union > 0 ? $intersection / $union : 0.0;
    }
}
