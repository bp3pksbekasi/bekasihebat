<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class MembershipService
{
    public function generateMemberNumber(): string
    {
        return DB::transaction(function () {
            $lastUser = User::whereNotNull('member_number')
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $nextNumber = 1;

            if ($lastUser && preg_match('/BKH-(\d+)/', $lastUser->member_number, $matches)) {
                $nextNumber = (int) $matches[1] + 1;
            }

            return sprintf('BKH-%06d', $nextNumber);
        });
    }

    public function assignMemberNumber(User $user): void
    {
        if ($user->member_number) {
            return;
        }

        $user->update([
            'member_number' => $this->generateMemberNumber(),
        ]);
    }
}
