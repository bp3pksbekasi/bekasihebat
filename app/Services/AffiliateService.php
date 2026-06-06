<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

class AffiliateService
{
    public function trackReferral(User $user, ?string $referralCode): ?array
    {
        if (! is_string($referralCode) || trim($referralCode) === '') {
            return null;
        }

        return [
            'user_id' => $user->id,
            'referral_code' => trim($referralCode),
        ];
    }
}
