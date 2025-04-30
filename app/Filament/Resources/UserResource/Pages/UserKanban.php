<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Enums\UserStatus;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;

class UserKanban extends Page
{
    // Define the property to hold users by status
    public $usersByStatus = [];

    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.user-kanban';

    /**
     * Fetch users by their status
     * @return array
     */
    public function getUsersByStatus()
    {
        return [
            'approved' => User::where('status', UserStatus::Approved)->get(),
            'pending' => User::where('status', UserStatus::Pending)->get(),
            'rejected' => User::where('status', UserStatus::Rejected)->get(),
        ];
    }

    /**
     * Mount method with void return type.
     * @return void
     */
    public function mount(): void
    {
        // Fetch the users by status and pass it to the view
        $this->usersByStatus = $this->getUsersByStatus();
    }

    /**
     * Update the user's status after drag-and-drop.
     * @param int $userId
     * @param string $newStatus
     */
    public function updateUserStatus($userId, $newStatus)
    {
        $user = User::find($userId);

        if ($user) {
            $user->status = $newStatus;
            $user->save();

            // Reload the users by status after the update
            $this->usersByStatus = $this->getUsersByStatus();
        }
    }
}
