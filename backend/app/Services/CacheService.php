<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function cacheExam($examKey, $examData, $ttlMinutes = 60)
    {
        return Cache::put("exam_{$examKey}", $examData, now()->addMinutes($ttlMinutes));
    }

    public function getCachedExam($examKey)
    {
        return Cache::get("exam_{$examKey}");
    }

    public function invalidateExamCache($examKey)
    {
        Cache::forget("exam_{$examKey}");
    }

    public function cacheExamResults($examKey, $studentId, $resultData, $ttlMinutes = 30)
    {
        return Cache::put("exam_results_{$examKey}_{$studentId}", $resultData, now()->addMinutes($ttlMinutes));
    }

    public function getCachedResults($examKey, $studentId)
    {
        return Cache::get("exam_results_{$examKey}_{$studentId}");
    }

    public function cacheRanking($examKey, $rankingData, $ttlMinutes = 30)
    {
        return Cache::put("ranking_{$examKey}", $rankingData, now()->addMinutes($ttlMinutes));
    }

    public function getCachedRanking($examKey)
    {
        return Cache::get("ranking_{$examKey}");
    }

    public function invalidateRankingCache($examKey)
    {
        Cache::forget("ranking_{$examKey}");
    }
}
