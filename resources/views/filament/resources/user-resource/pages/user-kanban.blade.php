<x-filament::page>
    <div class="flex justify-between space-x-4">
        <!-- Pending Column -->
        <div class="flex-1 p-4 border rounded-lg" id="pending-column">
            <h3 class="text-xl font-semibold">Pending</h3>
            @foreach ($usersByStatus['pending'] as $user)
                <div class="bg-gray-200 p-4 mb-2 rounded" data-user-id="{{ $user->id }}">
                    <p><strong>Name:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Address:</strong> {{ $user->address }}</p>
                    <p><strong>NIC:</strong> {{ $user->nic }}</p>
                </div>
            @endforeach
        </div>

        <!-- Approved Column -->
        <div class="flex-1 p-4 border rounded-lg" id="approved-column">
            <h3 class="text-xl font-semibold">Approved</h3>
            @foreach ($usersByStatus['approved'] as $user)
                <div class="bg-gray-200 p-4 mb-2 rounded" data-user-id="{{ $user->id }}">
                    <p><strong>Name:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Address:</strong> {{ $user->address }}</p>
                    <p><strong>NIC:</strong> {{ $user->nic }}</p>
                </div>
            @endforeach
        </div>

        <!-- Rejected Column -->
        <div class="flex-1 p-4 border rounded-lg" id="rejected-column">
            <h3 class="text-xl font-semibold">Rejected</h3>
            @foreach ($usersByStatus['rejected'] as $user)
                <div class="bg-gray-200 p-4 mb-2 rounded" data-user-id="{{ $user->id }}">
                    <p><strong>Name:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Address:</strong> {{ $user->address }}</p>
                    <p><strong>NIC:</strong> {{ $user->nic }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Include JavaScript for Drag and Drop -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pendingColumn = document.getElementById('pending-column');
            const approvedColumn = document.getElementById('approved-column');
            const rejectedColumn = document.getElementById('rejected-column');

            // Initialize Sortable.js for each column
            new Sortable(pendingColumn, {
                group: 'users',
                onEnd: (evt) => handleDragEnd(evt, 'pending')
            });
            new Sortable(approvedColumn, {
                group: 'users',
                onEnd: (evt) => handleDragEnd(evt, 'approved')
            });
            new Sortable(rejectedColumn, {
                group: 'users',
                onEnd: (evt) => handleDragEnd(evt, 'rejected')
            });

            // Handle drag and drop event
            function handleDragEnd(evt, newStatus) {
                const userId = evt.item.dataset.userId;
                if (userId) {
                    updateUserStatus(userId, newStatus);
                }
            }

            // Update user status by making an AJAX call to the backend
            function updateUserStatus(userId, newStatus) {
                fetch(`/update-user-status/${userId}/${newStatus}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('User status updated successfully');
                    } else {
                        console.error('Failed to update user status:', data.message);
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
            }
        });
    </script>

    <!-- Add CSRF Token Meta Tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</x-filament::page>
