<?php

namespace App\Livewire\Layouts;

use Livewire\Volt\Component;
use Illuminate\Contracts\View\View;

class MessagesDropdown extends Component
{
    public array $messages = [
        [
            'avatar' => '/dist/assets/img/user1-128x128.jpg',
            'name' => 'Brad Diesel',
            'message' => 'Call me whenever you can...',
            'time' => '4 Hours Ago',
            'star_class' => 'text-danger',
        ],
        [
            'avatar' => '/dist/assets/img/user8-128x128.jpg',
            'name' => 'John Pierce',
            'message' => 'I got your message bro',
            'time' => '4 Hours Ago',
            'star_class' => 'text-secondary',
        ],
        [
            'avatar' => '/dist/assets/img/user3-128x128.jpg',
            'name' => 'Nora Silvester',
            'message' => 'The subject goes here',
            'time' => '4 Hours Ago',
            'star_class' => 'text-warning',
        ],
    ];

    public int $messageCount = 3;

    public function render(): View
    {
        return view('components.layouts.messages-dropdown', [
            'messages' => $this->messages,
            'messageCount' => $this->messageCount,
        ]);
    }
}
