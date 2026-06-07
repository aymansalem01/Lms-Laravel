<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\SubmissionFingerprint;
use App\Models\PlagiarismReport;
use Illuminate\Support\Str;

class PlagiarismService
{
    const K = 5;
    const W = 4;

    public function check(Submission $submission): PlagiarismReport
    {
        $text = $submission->notes ?? '';

        if (empty(trim($text))) {
            return PlagiarismReport::updateOrCreate(
                ['submission_id' => $submission->id],
                ['overall_similarity' => 0, 'ai_probability' => 0, 'matches' => [], 'status' => 'completed', 'checked_at' => now()]
            );
        }

        $fingerprints = $this->fingerprint($text);
        $wordCount = str_word_count($text);

        SubmissionFingerprint::updateOrCreate(
            ['submission_id' => $submission->id],
            ['fingerprints' => $fingerprints, 'word_count' => $wordCount]
        );

        $allFingerprints = SubmissionFingerprint::where('submission_id', '!=', $submission->id)->get();
        $matches = [];
        $maxSimilarity = 0;

        foreach ($allFingerprints as $other) {
            $similarity = $this->jaccardSimilarity($fingerprints, $other->fingerprints);
            if ($similarity >= 0.05) {
                $maxSimilarity = max($maxSimilarity, $similarity);
                $matches[] = [
                    'submission_id' => $other->submission_id,
                    'similarity' => round($similarity * 100, 2),
                    'matching_hashes' => $this->matchingHashes($fingerprints, $other->fingerprints),
                ];
            }
        }

        usort($matches, fn($a, $b) => $b['similarity'] <=> $a['similarity']);
        $matches = array_slice($matches, 0, 10);

        $aiProbability = $this->detectAI($text);

        return PlagiarismReport::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'overall_similarity' => round($maxSimilarity * 100, 2),
                'ai_probability' => $aiProbability,
                'matches' => $matches,
                'status' => 'completed',
                'checked_at' => now(),
            ]
        );
    }

    private function normalize(string $text): string
    {
        $text = Str::lower($text);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    private function kgrams(string $text, int $k = self::K): array
    {
        $words = explode(' ', $text);
        $grams = [];
        for ($i = 0; $i <= count($words) - $k; $i++) {
            $grams[] = implode(' ', array_slice($words, $i, $k));
        }
        return $grams;
    }

    private function hash(string $gram): int
    {
        $hash = 0x811c9dc5;
        for ($i = 0; $i < strlen($gram); $i++) {
            $hash ^= ord($gram[$i]);
            $hash *= 0x01000193;
            $hash &= 0xFFFFFFFF;
        }
        return $hash;
    }

    private function fingerprint(string $text): array
    {
        $text = $this->normalize($text);
        $grams = $this->kgrams($text);
        $hashes = array_map(fn($g) => $this->hash($g), $grams);

        $fingerprint = [];
        for ($i = 0; $i <= count($hashes) - self::W; $i++) {
            $window = array_slice($hashes, $i, self::W);
            $minHash = min($window);
            $minPos = array_search($minHash, $window) + $i;
            $fingerprint[$minPos] = $minHash;
        }

        return array_values(array_unique(array_values($fingerprint)));
    }

    private function jaccardSimilarity(array $a, array $b): float
    {
        if (empty($a) || empty($b)) return 0;
        $setA = array_unique($a);
        $setB = array_unique($b);
        $intersection = count(array_intersect($setA, $setB));
        $union = count(array_unique(array_merge($setA, $setB)));
        return $union > 0 ? $intersection / $union : 0;
    }

    private function matchingHashes(array $a, array $b): array
    {
        return array_values(array_intersect($a, $b));
    }

    public function detectAI(string $text): float
    {
        if (empty(trim($text))) return 0;

        $sentences = preg_split('/[.!?]+/', $text);
        $sentences = array_filter(array_map('trim', $sentences));
        $sentenceCount = count($sentences);
        $wordCount = str_word_count($text);
        $totalChars = strlen($text);

        if ($sentenceCount < 2 || $wordCount < 10) return 0;

        $sentenceLengths = array_map(fn($s) => str_word_count($s), $sentences);
        $meanLength = array_sum($sentenceLengths) / $sentenceCount;
        $variance = array_sum(array_map(fn($l) => ($l - $meanLength) ** 2, $sentenceLengths)) / $sentenceCount;
        $stdDev = sqrt($variance);
        $sentenceVarianceScore = min($stdDev / 5, 1);

        $burstiness = 0;
        for ($i = 1; $i < $sentenceCount; $i++) {
            $burstiness += abs($sentenceLengths[$i] - $sentenceLengths[$i - 1]);
        }
        $burstiness = $sentenceCount > 1 ? $burstiness / ($sentenceCount - 1) : 0;
        $burstinessScore = min($burstiness / 4, 1);

        $transitionWords = ['however', 'therefore', 'furthermore', 'moreover', 'consequently', 'nevertheless',
            'additionally', 'meanwhile', 'accordingly', 'subsequently', 'hence', 'thus', 'notably', 'specifically'];
        $transitionCount = 0;
        $lowerText = Str::lower($text);
        foreach ($transitionWords as $word) {
            $transitionCount += preg_match_all('/\b' . preg_quote($word, '/') . '\b/', $lowerText);
        }
        $transitionDensity = $wordCount > 0 ? $transitionCount / $wordCount : 0;
        $transitionScore = min($transitionDensity * 20, 1);

        $pronouns = ['i', 'you', 'he', 'she', 'it', 'we', 'they', 'me', 'him', 'her', 'us', 'them',
            'my', 'your', 'his', 'her', 'its', 'our', 'their', 'mine', 'yours', 'his', 'hers', 'ours', 'theirs'];
        $pronounCount = 0;
        foreach ($pronouns as $pronoun) {
            $pronounCount += preg_match_all('/\b' . preg_quote($pronoun, '/') . '\b/', $lowerText);
        }
        $pronounDensity = $wordCount > 0 ? $pronounCount / $wordCount : 0;
        $pronounScore = 1 - min($pronounDensity * 5, 1);

        $uniqueWords = count(array_unique(str_word_count($lowerText, 1)));
        $vocabRichness = $wordCount > 0 ? $uniqueWords / $wordCount : 0;
        $vocabScore = 1 - min($vocabRichness * 2, 1);

        $contractions = ["don't", "can't", "won't", "isn't", "aren't", "wasn't", "weren't", "haven't",
            "hasn't", "hadn't", "doesn't", "didn't", "couldn't", "wouldn't", "shouldn't", "mightn't",
            "mustn't", "i'm", "you're", "he's", "she's", "it's", "we're", "they're", "i've", "you've",
            "we've", "they've", "i'd", "you'd", "he'd", "she'd", "we'd", "they'd", "i'll", "you'll",
            "he'll", "she'll", "we'll", "they'll"];
        $contractionCount = 0;
        foreach ($contractions as $contraction) {
            $contractionCount += preg_match_all('/\b' . preg_quote($contraction, '/') . '\b/', $lowerText);
        }
        $contractionDensity = $wordCount > 0 ? $contractionCount / $wordCount : 0;
        $contractionScore = 1 - min($contractionDensity * 10, 1);

        $weights = [0.20, 0.15, 0.15, 0.15, 0.20, 0.15];
        $score = $weights[0] * $sentenceVarianceScore
               + $weights[1] * $burstinessScore
               + $weights[2] * $transitionScore
               + $weights[3] * $pronounScore
               + $weights[4] * $vocabScore
               + $weights[5] * $contractionScore;

        return round($score * 100, 2);
    }
}
