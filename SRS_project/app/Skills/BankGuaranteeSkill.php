<?php

namespace App\Skills;

use Modules\Finance\Models\BankGuarantee;

class BankGuaranteeSkill extends AbstractSkill
{
    protected string $name = 'Bank Guarantee Skill';
    protected string $description = 'Handles BG lifecycle, validity tracking, and classification.';

    /**
     * Check if a BG is expiring within a certain range.
     */
    public function isExpiringSoon(BankGuarantee $bg, int $days = 30): bool
    {
        $expiryDate = $bg->validity_date;
        $diff = now()->diffInDays($expiryDate, false);
        return $diff >= 0 && $diff <= $days;
    }
}
