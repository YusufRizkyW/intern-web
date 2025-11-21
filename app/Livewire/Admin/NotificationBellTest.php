<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class NotificationBellTest extends Component
{
    public function render()
    {
        return <<<'HTML'
        <div style="background: blue; color: white; padding: 10px; border-radius: 5px; margin: 5px;">
            <strong>🔔 NOTIFICATION BELL TEST</strong>
            <p>Component berhasil di-render!</p>
        </div>
        HTML;
    }
}