<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative p-1 text-gray-600 hover:text-blue-600">
        <i class="fas fa-bell text-xl"></i>
        @if($unreadCount = auth()->user()->unreadNotifications->count())
            <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                {{ $unreadCount }}
            </span>
        @endif
    </button>
    
    <div 
        x-show="open" 
        @click.away="open = false"
        class="absolute right-0 mt-2 w-72 md:w-96 bg-white rounded-md shadow-lg py-1 z-50"
        style="display: none;"
    >
        <div class="px-4 py-2 border-b flex justify-between items-center">
            <h3 class="text-sm font-medium">Notifications</h3>
            <button 
                id="markAllAsRead"
                class="text-xs text-blue-600 hover:text-blue-800"
            >
                Mark all as read
            </button>
        </div>
        
        <div class="max-h-96 overflow-y-auto">
            @forelse(auth()->user()->notifications()->latest()->take(10)->get() as $notification)
                <div 
                    class="px-4 py-3 border-b hover:bg-gray-50 notification-item"
                    data-id="{{ $notification->id }}"
                    :class="{ 'bg-gray-50': {{ $notification->read_at ? 'true' : 'false' }} }"
                >
                    <div class="flex items-start">
                        <div class="flex-shrink-0 pt-1">
                            <div class="h-8 w-8 rounded-full flex items-center justify-center 
                                bg-{{ $notification->data['color'] ?? 'blue' }}-100 text-{{ $notification->data['color'] ?? 'blue' }}-600">
                                <i class="fas fa-{{ $notification->data['icon'] ?? 'bell' }} text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $notification->data['title'] ?? 'Notification' }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                        @if(!$notification->read_at)
                            <div class="ml-2 flex-shrink-0">
                                <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-3 text-center text-sm text-gray-500">
                    No notifications found
                </div>
            @endforelse
        </div>
        
        <div class="px-4 py-2 border-t text-center">
            <a 
                href="{{ route('notifications.index') }}" 
                class="text-sm text-blue-600 hover:text-blue-800"
            >
                View all notifications
            </a>
        </div>
    </div>
</div>