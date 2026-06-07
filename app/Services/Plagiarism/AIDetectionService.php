<?php

namespace App\Services\Plagiarism;

class AIDetectionService
{
    public function detectAI(string $text): array
    {
        $sentences = preg_split('/[.!?]+/', $text);
        $sentences = array_filter(array_map('trim', $sentences));
        $tokens = preg_split('/\s+/', trim(preg_replace('/[^\w\s]/u', '', mb_strtolower($text))));
        $tokens = array_filter($tokens);

        if (count($tokens) < 20 || count($sentences) < 3) {
            return ['probability' => 0, 'label' => 'likely_human', 'signals' => []];
        }

        $signals = [];

        $lengths = array_map('strlen', $sentences);
        $avgLength = array_sum($lengths) / count($lengths);
        $variance = array_sum(array_map(fn($l) => ($l - $avgLength) ** 2, $lengths)) / count($lengths);
        $signals['sentence_length_variance'] = min(100, $variance / 10);
        $signals['burstiness'] = min(100, sqrt($variance) / 5);

        $uniqueTokens = array_unique($tokens);
        $ttr = count($uniqueTokens) / max(1, count($tokens));
        $signals['vocabulary_richness'] = $ttr > 0.8 ? min(100, ($ttr - 0.5) * 200) : max(0, ($ttr - 0.3) * 200);

        $transitionWords = ['however', 'therefore', 'furthermore', 'moreover', 'consequently', 'additionally', 'nevertheless', 'meanwhile', 'subsequently', 'accordingly', 'notably', 'specifically', 'conversely', 'ultimately', 'overall'];
        $transitionCount = 0;
        foreach ($tokens as $token) {
            if (in_array($token, $transitionWords)) $transitionCount++;
        }
        $signals['transition_word_frequency'] = min(100, ($transitionCount / max(1, count($tokens))) * 500);

        $firstPerson = ['i', 'me', 'my', 'mine', 'we', 'us', 'our', 'ours'];
        $pronounCount = 0;
        foreach ($tokens as $token) {
            if (in_array($token, $firstPerson)) $pronounCount++;
        }
        $pronounDensity = $pronounCount / max(1, count($tokens));
        $signals['pronoun_density'] = $pronounDensity < 0.01 ? 80 : max(0, (1 - $pronounDensity * 50) * 100);

        $weights = [
            'sentence_length_variance' => 0.15,
            'burstiness' => 0.20,
            'vocabulary_richness' => 0.15,
            'transition_word_frequency' => 0.20,
            'pronoun_density' => 0.30,
        ];

        $score = 0;
        foreach ($weights as $key => $weight) {
            $score += ($signals[$key] ?? 0) * $weight;
        }

        return [
            'probability' => round($score, 1),
            'label' => $score > 60 ? 'likely_ai' : ($score > 35 ? 'mixed' : 'likely_human'),
            'signals' => $signals,
        ];
    }
}
