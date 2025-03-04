<?php

namespace App\Livewire;

use App\Enums\MessageStatus;
use App\Enums\Gender;
use App\Enums\NotificationType;
use App\Models\Message;
use App\Models\User;
use App\Models\Notification;
use Livewire\Component;
use Carbon\Carbon;

class MessagesCompose extends Component
{
    public $subject = '';
    public $message = '';
    public $receiverId = null;
    public $searchTerm = '';
    public $filteredRecipients = [];

    public function mount()
    {
        $this->loadRecipients();
        \Log::info('Initial filtered recipients count:', ['count' => count($this->filteredRecipients)]);
    }

    public function loadRecipients()
    {
        $currentUser = auth()->user();
        $genderFilter = $currentUser->gender === Gender::Male ? Gender::Female->value : Gender::Male->value;

        \Log::info('Loading recipients with search term:', ['searchTerm' => $this->searchTerm, 'genderFilter' => $genderFilter]);

        $users = User::where('id', '!=', auth()->id())
            ->where('gender', $genderFilter)
            ->when($this->searchTerm, function ($query) {
                $query->where('display_name', 'like', '%' . $this->searchTerm . '%');
            })
            ->whereNotNull('birth_date')
            ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 40')
            ->get();

        \Log::info('Found users:', ['count' => $users->count()]);

        $this->filteredRecipients = $users->map(function ($user) {
            $profilePicture = $user->profilePicture();
            return [
                'id' => $user->id,
                'display_name' => $user->display_name,
                'profile_picture' => $profilePicture ? [
                    'media' => [
                        'path' => $profilePicture->media->path ?? null,
                    ],
                ] : null,
            ];
        })->toArray();

        \Log::info('Filtered recipients structure:', ['first_recipient' => $this->filteredRecipients[0] ?? 'No recipients']);
    }

    public function updateSearch($value)
    {
        \Log::info('Search term updated:', ['searchTerm' => $value]);
        $this->searchTerm = $value;
        $this->loadRecipients();
        \Log::info('Filtered recipients after search:', ['count' => count($this->filteredRecipients)]);
    }

    public function addEmoji($emojiUnicode)
    {
        \Log::info('Adding emoji to message:', ['emojiUnicode' => $emojiUnicode]);
        $this->message = $this->message . ($this->message ? ' ' : '') . $emojiUnicode;
    }

    public function selectRecipient($userId)
    {
        \Log::info('Selected recipient:', ['userId' => $userId]);
        $this->receiverId = $userId;
        $this->searchTerm = '';
        $this->loadRecipients();
    }

    public function sendMessage()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'receiverId' => 'required|exists:users,id',
        ]);

        // دیباگ برای چک کردن همه مقادیر قبل از ذخیره
        $data = [
            'sender_id' => auth()->id(),
            'receiver_id' => $this->receiverId,
            'subject' => $this->subject,
            'message' => $this->message,
            'sent_at' => now()->toDateTimeString(),
            'status' => 'sent',
        ];
        \Log::info('Data before saving message:', $data);

        // چک اضافی برای اطمینان از مقدار receiverId
        if (!is_numeric($this->receiverId)) {
            \Log::error('Invalid receiverId:', ['receiverId' => $this->receiverId]);
            throw new \Exception('Invalid receiver ID');
        }

        // ذخیره پیام
        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => (int) $this->receiverId,
            'subject' => $this->subject,
            'message' => $this->message,
            'sent_at' => now(),
            'status' => 'sent',
        ]);

        // ارسال اعلان به گیرنده
        Notification::create([
            'user_id' => $this->receiverId,
            'sender_id' => auth()->id(),
            'type' => NotificationType::NewMessage->value,
            'title' => \Illuminate\Support\Str::limit(auth()->user()->display_name . ' sent you a new message', 100, '...'),
            'content' => 'Click to view the message.',
            'action_url' => route('messages.read', ['id' => $message->id]),
            'related_id' => $message->id,
            'related_type' => Message::class,
            'priority' => 2,
            'is_read' => false,
            'read_at' => null,
            'data' => [
                'sender_name' => auth()->user()->display_name ?? 'Unknown',
                'subject' => $this->subject,
            ],
        ]);

        $this->reset(['subject', 'message', 'receiverId', 'searchTerm']);
        session()->flash('success', 'Message sent successfully!');
    }

    public function render()
    {
        $currentUser = auth()->user();
        $genderFilter = $currentUser->gender === Gender::Male ? Gender::Female->value : Gender::Male->value;

        $recipients = User::where('id', '!=', auth()->id())
            ->where('gender', $genderFilter)
            ->whereNotNull('birth_date')
            ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 40')
            ->get();

        \Log::info('Recipients Count: ' . $recipients->count());
        \Log::info('Current receiverId:', ['receiverId' => $this->receiverId]);
        \Log::info('Current searchTerm:', ['searchTerm' => $this->searchTerm]);

        return view('livewire.messages-compose', [
            'recipients' => $recipients,
            'emojis' => get_emojis(),
        ]);
    }
}
