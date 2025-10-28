<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    /**
     * Get or create a conversation between the authenticated user and another user
     */
    public function getOrCreateConversation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'other_user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $currentUserId = Auth::id();
        $otherUserId = $request->other_user_id;

        // Prevent users from creating a conversation with themselves
        if ($currentUserId == $otherUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot create conversation with yourself',
            ], 400);
        }

        // Find or create conversation
        $conversation = Conversation::findOrCreateBetween($currentUserId, $otherUserId);

        // Load relationships
        $conversation->load(['user1', 'user2', 'latestMessage.sender']);

        // Get the other user
        $otherUser = $conversation->getOtherUser($currentUserId);

        return response()->json([
            'success' => true,
            'conversation' => [
                'id' => $conversation->id,
                'other_user' => [
                    'id' => $otherUser->id,
                    'first_name' => $otherUser->first_name,
                    'last_name' => $otherUser->last_name,
                    'email' => $otherUser->email,
                    'profile_image_path' => $otherUser->profile_image_path,
                ],
                'last_message' => $conversation->latestMessage ? [
                    'id' => $conversation->latestMessage->id,
                    'message' => $conversation->latestMessage->message,
                    'sender_id' => $conversation->latestMessage->sender_id,
                    'created_at' => $conversation->latestMessage->created_at->toISOString(),
                ] : null,
                'last_message_at' => $conversation->last_message_at?->toISOString(),
                'created_at' => $conversation->created_at->toISOString(),
            ],
        ]);
    }

    /**
     * Get all conversations for the authenticated user
     */
    public function getConversations(Request $request): JsonResponse
    {
        $userId = Auth::id();

        $conversations = Conversation::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->with(['user1', 'user2', 'latestMessage.sender'])
            ->orderBy('last_message_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedConversations = $conversations->map(function ($conversation) use ($userId) {
            $otherUser = $conversation->getOtherUser($userId);
            return [
                'id' => $conversation->id,
                'other_user' => [
                    'id' => $otherUser->id,
                    'first_name' => $otherUser->first_name,
                    'last_name' => $otherUser->last_name,
                    'email' => $otherUser->email,
                    'profile_image_path' => $otherUser->profile_image_path,
                ],
                'last_message' => $conversation->latestMessage ? [
                    'id' => $conversation->latestMessage->id,
                    'message' => $conversation->latestMessage->message,
                    'sender_id' => $conversation->latestMessage->sender_id,
                    'created_at' => $conversation->latestMessage->created_at->toISOString(),
                ] : null,
                'unread_count' => $conversation->messages()
                    ->where('sender_id', '!=', $userId)
                    ->whereNull('read_at')
                    ->count(),
                'last_message_at' => $conversation->last_message_at?->toISOString(),
                'created_at' => $conversation->created_at->toISOString(),
            ];
        });

        return response()->json([
            'success' => true,
            'conversations' => $formattedConversations,
        ]);
    }

    /**
     * Get messages for a specific conversation
     */
    public function getMessages(Request $request, $conversationId): JsonResponse
    {
        $userId = Auth::id();

        $conversation = Conversation::find($conversationId);

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found',
            ], 404);
        }

        // Check if user is part of this conversation
        if (!$conversation->hasUser($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this conversation',
            ], 403);
        }

        // Mark messages as read (messages sent by the other user)
        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::where('conversation_id', $conversationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        $formattedMessages = $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender' => [
                    'id' => $message->sender->id,
                    'first_name' => $message->sender->first_name,
                    'last_name' => $message->sender->last_name,
                    'profile_image_path' => $message->sender->profile_image_path,
                ],
                'message' => $message->message,
                'read_at' => $message->read_at?->toISOString(),
                'created_at' => $message->created_at->toISOString(),
            ];
        });

        return response()->json([
            'success' => true,
            'messages' => $formattedMessages,
        ]);
    }

    /**
     * Send a message in a conversation
     */
    public function sendMessage(Request $request, $conversationId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = Auth::id();

        $conversation = Conversation::find($conversationId);

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found',
            ], 404);
        }

        // Check if user is part of this conversation
        if (!$conversation->hasUser($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this conversation',
            ], 403);
        }

        // Create the message
        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => $userId,
            'message' => $request->message,
        ]);

        // Update conversation's last_message_at
        $conversation->update([
            'last_message_at' => now(),
        ]);

        // Load sender relationship
        $message->load('sender');

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender' => [
                    'id' => $message->sender->id,
                    'first_name' => $message->sender->first_name,
                    'last_name' => $message->sender->last_name,
                    'profile_image_path' => $message->sender->profile_image_path,
                ],
                'message' => $message->message,
                'read_at' => $message->read_at?->toISOString(),
                'created_at' => $message->created_at->toISOString(),
            ],
        ], 201);
    }

    /**
     * Send a message to a user (creates conversation if it doesn't exist)
     */
    public function sendMessageToUser(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $currentUserId = Auth::id();
        $recipientId = $request->recipient_id;

        // Prevent users from sending messages to themselves
        if ($currentUserId == $recipientId) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot send message to yourself',
            ], 400);
        }

        // Find or create conversation
        $conversation = Conversation::findOrCreateBetween($currentUserId, $recipientId);

        // Create the message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $currentUserId,
            'message' => $request->message,
        ]);

        // Update conversation's last_message_at
        $conversation->update([
            'last_message_at' => now(),
        ]);

        // Load relationships
        $message->load('sender');
        $conversation->load('user1', 'user2');

        $otherUser = $conversation->getOtherUser($currentUserId);

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender' => [
                    'id' => $message->sender->id,
                    'first_name' => $message->sender->first_name,
                    'last_name' => $message->sender->last_name,
                    'profile_image_path' => $message->sender->profile_image_path,
                ],
                'message' => $message->message,
                'read_at' => $message->read_at?->toISOString(),
                'created_at' => $message->created_at->toISOString(),
            ],
            'other_user' => [
                'id' => $otherUser->id,
                'first_name' => $otherUser->first_name,
                'last_name' => $otherUser->last_name,
                'email' => $otherUser->email,
                'profile_image_path' => $otherUser->profile_image_path,
            ],
        ], 201);
    }
}
