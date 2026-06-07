<?php

namespace App\Services\Plagiarism;

use App\Models\Submission;
use App\Models\SubmissionFingerprint;
use App\Models\PlagiarismReport;

class PlagiarismService
{
    public function __construct(
        private FingerprintService $fingerprint,
        private AIDetectionService $ai,
    ) {}

    public function check(Submission $submission): PlagiarismReport
    {
        $textContent = $submission->notes ?? '';
        if (empty($textContent)) {
            return $this->createEmptyReport($submission);
        }

        $wordCount = str_word_count($textContent);
        $fingerprints = $this->fingerprint->fingerprintText($textContent);
        $aiResult = $this->ai->detectAI($textContent);

        $matches = [];
        $maxSimilarity = 0;
        $otherSubmissions = Submission::whereHas('assignment', function ($q) use ($submission) {
            $q->where('course_id', $submission->assignment->course_id);
        })->where('id', '!=', $submission->id)->get();

        foreach ($otherSubmissions as $other) {
            $otherFp = SubmissionFingerprint::where('submission_id', $other->id)->first();
            if (!$otherFp) {
                $otherText = $other->notes ?? '';
                $otherHash = $this->fingerprint->fingerprintText($otherText);
            } else {
                $otherHash = $otherFp->fingerprints;
            }

            $similarity = $this->fingerprint->jaccardSimilarity($fingerprints, $otherHash);
            if ($similarity > $maxSimilarity) $maxSimilarity = $similarity;
            if ($similarity > 0.1) {
                $matches[] = [
                    'submission_id' => $other->id,
                    'similarity' => round($similarity * 100, 1),
                ];
            }
        }

        SubmissionFingerprint::updateOrCreate(
            ['submission_id' => $submission->id],
            ['fingerprints' => $fingerprints, 'word_count' => $wordCount]
        );

        return PlagiarismReport::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'overall_similarity' => round($maxSimilarity * 100, 1),
                'ai_probability' => $aiResult['probability'],
                'matches' => $matches,
                'status' => 'completed',
                'checked_at' => now(),
            ]
        );
    }

    private function createEmptyReport(Submission $submission): PlagiarismReport
    {
        return PlagiarismReport::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'overall_similarity' => 0,
                'ai_probability' => 0,
                'matches' => [],
                'status' => 'completed',
                'checked_at' => now(),
            ]
        );
    }
}
