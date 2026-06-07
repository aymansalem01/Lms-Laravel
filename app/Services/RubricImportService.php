<?php

namespace App\Services;

class RubricImportService
{
    public function importFromXML(string $xmlContent): array
    {
        $xml = simplexml_load_string($xmlContent);

        if ($xml === false) {
            throw new \InvalidArgumentException(__('Invalid XML format.'));
        }

        $criteria = [];
        $levels = [];
        $cells = [];

        if (isset($xml->criteria->criterion)) {
            $levelNames = [];

            foreach ($xml->criteria->criterion as $criterion) {
                $criterionName = (string) $criterion->name;
                $criterionIndex = count($criteria);

                $criteria[] = ['name' => $criterionName];

                if (isset($criterion->levels->level)) {
                    $levelIndex = 0;

                    foreach ($criterion->levels->level as $level) {
                        $levelName = (string) $level->name;
                        $score = (float) $level->score;
                        $description = (string) $level->description;

                        if (!in_array($levelName, $levelNames)) {
                            $levelNames[] = $levelName;
                            $levels[] = ['name' => $levelName];
                        }

                        $levelRef = array_search($levelName, $levelNames);

                        $cells[] = [
                            'criterion' => $criterionIndex,
                            'level' => $levelRef,
                            'score' => $score,
                            'description' => $description,
                        ];
                    }
                }
            }
        }

        return [
            'criteria' => $criteria,
            'levels' => $levels,
            'cells' => $cells,
        ];
    }
}
