<?php

namespace App\Livewire;

use App\Enums\ChatStatus;
use App\Models\ChatMessage;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Chat extends Component
{
    public $selectedUserId = null;
    public $message = '';
    public $messages = [];
    public $users = [];
    public $isTyping = false;

    protected $firebaseService;

    public function mount(): void
    {
        $this->firebaseService = app(FirebaseService::class);
        $this->loadUsers();
        $this->updateDeliveredStatus(); // وضعیت Delivered رو موقع لود چک می‌کنیم
    }

    public function loadUsers(): void
    {
        $currentUser = Auth::user();

        $friends = $currentUser->friends()->pluck('target_id');
        $chatPartners = ChatMessage::where(function ($query) use ($currentUser) {
            $query->where('sender_id', $currentUser->id)
                  ->orWhere('receiver_id', $currentUser->id);
        })
        ->pluck('sender_id')
        ->merge(ChatMessage::where(function ($query) use ($currentUser) {
            $query->where('sender_id', $currentUser->id)
                  ->orWhere('receiver_id', $currentUser->id);
        })
        ->pluck('receiver_id'))
        ->unique();

        $blocked = $currentUser->blocks()->pluck('target_id');

        $this->users = User::whereIn('id', $friends->merge($chatPartners))
            ->whereNotIn('id', $blocked)
            ->where('gender', '!=', $currentUser->gender)
            ->whereRaw('ABS(YEAR(birth_date) - YEAR(?)) <= 10', [$currentUser->birth_date])
            ->where('id', '!=', $currentUser->id)
            ->select('id', 'display_name', 'last_seen')
            ->get()
            ->map(function ($user) {
                $user->is_online = $user->last_seen && now()->diffInMinutes($user->last_seen) < 5;
                return $user;
            })
            ->toArray();
    }

    public function selectUser(int $userId): void
    {
        $this->selectedUserId = $userId;
        $this->loadMessages();
        $this->markAsRead();
        $this->isTyping = false;
    }

    public function loadMessages(): void
    {
        if ($this->selectedUserId) {
            $this->messages = ChatMessage::between(Auth::id(), $this->selectedUserId)->orderBy('created_at', 'asc')->get()->toArray();
            $this->updateDeliveredStatus(); // هر بار که پیام‌ها لود می‌شه، Delivered رو آپدیت می‌کنیم
        }
    }

    public function sendMessage(): void
    {
        if (!$this->selectedUserId || empty(trim($this->message))) {
            return;
        }

        $chatMessage = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedUserId,
            'content' => $this->message,
            'status' => ChatStatus::Sent,
            'delivered_at' => null,
        ]);

        $this->message = '';
        $this->loadMessages();
        $this->dispatchFirebaseNotification($chatMessage);
    }

    public function markAsRead(): void
    {
        if ($this->selectedUserId) {
            ChatMessage::withStatus($this->selectedUserId, ChatStatus::Delivered)
                ->where('receiver_id', Auth::id())
                ->update([
                    'status' => ChatStatus::Read,
                    'updated_at' => now(),
                ]);
            $this->loadMessages();
        }
    }

    public function typing(): void
    {
        if ($this->selectedUserId) {
            Cache::put("typing_" . Auth::id() . "_to_{$this->selectedUserId}", true, 3);
        }
    }

    public function checkTyping(): void
    {
        if ($this->selectedUserId) {
            $this->isTyping = Cache::get("typing_{$this->selectedUserId}_to_" . Auth::id(), false);
        }
    }

    public function resetTyping(): void
    {
        $this->isTyping = false;
    }

    private function dispatchFirebaseNotification(ChatMessage $chatMessage): void
    {
        $receiver = User::find($chatMessage->receiver_id);

        if ($receiver && $receiver->fcm_token) {
            try {
                $this->firebaseService->sendNotification($receiver->fcm_token, 'New Message from ' . Auth::user()->display_name, $chatMessage->content);
                $chatMessage->update([
                    'status' => ChatStatus::Delivered,
                    'delivered_at' => now(),
                ]);
                $this->loadMessages();
            } catch (\Exception $e) {
                Log::error('Failed to send Firebase notification', ['error' => $e->getMessage()]);
            }
        } else {
            Log::info('No FCM token for receiver or receiver not found', ['receiver_id' => $chatMessage->receiver_id]);
        }
    }

    private function updateDeliveredStatus(): void
    {
        // پیام‌هایی که برای من فرستاده شدن و هنوز Sent هستن رو به Delivered تغییر بده
        ChatMessage::where('receiver_id', Auth::id())
            ->where('status', ChatStatus::Sent)
            ->update([
                'status' => ChatStatus::Delivered,
                'delivered_at' => now(),
            ]);
    }

    public function render()
    {
        Auth::user()->update(['last_seen' => now()]);
        return view('livewire.chat')
            ->with(['page_title' => 'Chat'])
            ->layout('components.layouts.app');
    }
}
