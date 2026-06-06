<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Services\AffiliateService;
use App\Services\MembershipService;
use App\Support\PhoneNormalizer;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class RegisterUser
{
    public function __construct(
        private readonly MembershipService $membershipService,
        private readonly AffiliateService $affiliateService,
    ) {}

    public function execute(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $normalizedPhone = PhoneNormalizer::normalize($data['phone']);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $normalizedPhone,
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole('community_member');

            $this->membershipService->assignMemberNumber($user);

            $referralCode = Session::get('pending_referral') ?? Cookie::get('pending_referral');

            if ($referralCode) {
                $this->affiliateService->trackReferral($user, $referralCode);
                Session::forget('pending_referral');
                Cookie::queue(Cookie::forget('pending_referral'));
            }

            return $user->fresh();
        });
    }
}
