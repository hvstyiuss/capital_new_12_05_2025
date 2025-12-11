@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Notifications</h1>
            <p class="text-muted mb-0">Consultez toutes vos notifications</p>
        </div>
        <div class="d-flex gap-2">
            @if($unreadCount > 0)
                <button class="btn btn-success" onclick="markAllAsRead()">
                    <i class="fas fa-check-double me-2"></i>Marquer tout comme lu
                </button>
            @endif
            @if($notifications->count() > 0)
                <button class="btn btn-danger" onclick="deleteAllNotifications()">
                    <i class="fas fa-trash me-2"></i>Supprimer tout
                </button>
            @endif
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            @forelse($notifications as $notification)
                <div class="notification-item p-4 border-bottom {{ $notification->isUnread() ? 'bg-light' : '' }}" 
                     data-notification-id="{{ $notification->id }}">
                    <div class="d-flex align-items-start">
                        <div class="notification-icon me-3">
                            <i class="fas {{ $notification->icon ?? 'fa-bell' }} fa-2x text-{{ $notification->color ?? 'primary' }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="mb-1">{{ $notification->title }}</h5>
                                    <p class="text-muted mb-2">{{ $notification->message }}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>{{ $notification->time_ago }}
                                    </small>
                                </div>
                                <div class="d-flex gap-2">
                                    @if($notification->isUnread())
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="markAsRead('{{ $notification->id }}')"
                                                title="Marquer comme lu">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteNotification('{{ $notification->id }}')"
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @if($notification->action_url)
                                <div class="mt-2">
                                    <a href="{{ $notification->action_url }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-1"></i>Voir les détails
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune notification</p>
                </div>
            @endforelse
        </div>
        
        @if($notifications->hasPages())
            <div class="card-footer">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.classList.remove('bg-light');
                const markAsReadBtn = notificationItem.querySelector('button[onclick*="markAsRead"]');
                if (markAsReadBtn) {
                    markAsReadBtn.remove();
                }
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function deleteNotification(notificationId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
        return;
    }
    
    fetch(`/notifications/${notificationId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationItem) {
                // Add fade out animation
                notificationItem.style.transition = 'opacity 0.3s ease';
                notificationItem.style.opacity = '0';
                setTimeout(() => {
                    notificationItem.remove();
                    
                    // Check if there are no more notifications
                    const remainingNotifications = document.querySelectorAll('.notification-item');
                    if (remainingNotifications.length === 0) {
                        location.reload();
                    }
                }, 300);
            }
        } else {
            alert('Erreur lors de la suppression de la notification.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la suppression de la notification.');
    });
}

function deleteAllNotifications() {
    if (!confirm('Êtes-vous sûr de vouloir supprimer toutes les notifications ? Cette action est irréversible.')) {
        return;
    }
    
    fetch('/notifications/delete-all', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de la suppression des notifications.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la suppression des notifications.');
    });
}
</script>
@endsection




