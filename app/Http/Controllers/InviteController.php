<?php

namespace App\Http\Controllers;

use App\Models\CommunityInvite;
use Illuminate\Http\Request;

class InviteController extends Controller {
    public function open(string $token) {
        $invite = CommunityInvite::where('invite_token', $token)->firstOrFail();

        return view('invite-open', [
            'token' => $token,
            'androidStoreUrl' => 'https://play.google.com/store/apps/details?id=com.sinfoniac.spacegig',
            'iosStoreUrl' => 'https://apps.apple.com/app/idYOUR_APP_ID',
            'appDeepLink' => "spacegig://invite/$token",
        ]);
    }
}
