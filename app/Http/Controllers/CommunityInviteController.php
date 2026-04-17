<?php

namespace App\Http\Controllers;

use App\Models\CommunityInvite;
use App\Models\User;
use App\Services\TwilioSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CommunityInviteController extends Controller {
    public function send(Request $request, TwilioSmsService $smsService) {
        $user = $request->user();

        $validated = $request->validate([
            'type' => 'required|in:group,networking',
            'target_id' => 'required|integer',
            'contacts' => 'required|array|min:1',
            'contacts.*.name' => 'nullable|string|max:255',
            'contacts.*.phone' => 'nullable|string|max:30',
            'contacts.*.email' => 'nullable|string|email|max:255',
        ]);

        $results = [];

        foreach ($validated['contacts'] as $contact) {
            $phone = $contact['phone'] ?? null;
            $email = $contact['email'] ?? null;

            $existingUser = User::query()
                ->when($phone, fn($q) => $q->orWhere('phone_number', $phone))
                ->when($email, fn($q) => $q->orWhere('email', $email))
                ->first();

            if ($existingUser) {
                $invite = CommunityInvite::firstOrCreate([
                    'sender_id' => $user->id,
                    'recipient_user_id' => $existingUser->id,
                    'type' => $validated['type'],
                    'target_id' => $validated['target_id'],
                ], [
                    'contact_name' => $contact['name'] ?? null,
                    'contact_phone' => $phone,
                    'contact_email' => $email,
                    'status' => 'pending',
                ]);

                if ($validated['type'] === 'group') {
                    $this->addGroupMemberIfNeeded($validated['target_id'], $existingUser->id);
                }

                if ($validated['type'] === 'networking') {
                    $this->createNetworkingConnectionIfNeeded($validated['target_id'], $existingUser->id, $user->id);
                }

                $results[] = [
                    'name' => $contact['name'] ?? null,
                    'phone' => $phone,
                    'email' => $email,
                    'has_account' => true,
                    'status' => 'added_or_invited',
                    'recipient_user_id' => $existingUser->id,
                ];
            } else {
                $token = Str::random(40);

                CommunityInvite::create([
                    'sender_id' => $user->id,
                    'recipient_user_id' => null,
                    'type' => $validated['type'],
                    'target_id' => $validated['target_id'],
                    'contact_name' => $contact['name'] ?? null,
                    'contact_phone' => $phone,
                    'contact_email' => $email,
                    'invite_token' => $token,
                    'status' => 'sent',
                ]);

                if ($phone) {
                    $inviteUrl = rtrim(config('app.url'), '/') . '/invite/' . $token;
                    $message = "You've been invited to join SpaceGig. Create your account here: {$inviteUrl}";
                    $smsService->send($phone, $message);
                }

                $results[] = [
                    'name' => $contact['name'] ?? null,
                    'phone' => $phone,
                    'email' => $email,
                    'has_account' => false,
                    'status' => 'sms_sent',
                ];
            }
        }

        return response()->json([
            'message' => 'Invitations processed successfully.',
            'results' => $results,
        ]);
    }

    protected function addGroupMemberIfNeeded(int $groupId, int $userId): void {
        $exists = DB::table('group_members')
            ->where('group_id', $groupId)
            ->where('user_id', $userId)
            ->exists();

        if (! $exists) {
            DB::table('group_members')->insert([
                'group_id' => $groupId,
                'user_id' => $userId,
                'status' => 'accepted',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function createNetworkingConnectionIfNeeded(int $profileId, int $recipientUserId, int $senderUserId): void {
        $exists = DB::table('networking_connections')
            ->where('profile_id', $profileId)
            ->where('user_id', $recipientUserId)
            ->exists();

        if (! $exists) {
            DB::table('networking_connections')->insert([
                'profile_id' => $profileId,
                'user_id' => $recipientUserId,
                'invited_by' => $senderUserId,
                'status' => 'accepted',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function accept(string $token) {
        $invite = CommunityInvite::where('invite_token', $token)->firstOrFail();
        return response()->json([
            'type' => $invite->type,
            'target_id' => $invite->target_id,
            'contact_phone' => $invite->contact_phone,
            'contact_email' => $invite->contact_email,
        ]);
    }
}
