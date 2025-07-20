{{-- resources/views/components/avatar-column.blade.php --}}
@props(['user'])

@php
    // Default avatar berdasarkan nama atau default image
    $defaultAvatar = 'images/avatar/default.jpg';
    $avatarPath = $user->avatar ? 'images/avatar/' . $user->avatar : $defaultAvatar;

    // Generate initials dari nama sebagai fallback
    $nameParts = explode(' ', trim($user->name));
    $initials = '';
    foreach ($nameParts as $part) {
        if (!empty($part)) {
            $initials .= strtoupper(substr($part, 0, 1));
        }
        if (strlen($initials) >= 2) {
            break;
        }
    }
    if (empty($initials)) {
        $initials = 'U'; // Default initial
    }
@endphp

<div class="flex items-center">
    <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-300 flex items-center justify-center relative">
        @if ($user->avatar && file_exists(public_path($avatarPath)))
            <img src="{{ asset($avatarPath) }}" alt="{{ $user->name }}" class="w-full h-full object-cover"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div
                class="absolute inset-0 bg-blue-500 text-white text-xs font-semibold rounded-full hidden items-center justify-center">
                {{ $initials }}
            </div>
        @else
            <div
                class="w-full h-full bg-blue-500 text-white text-xs font-semibold rounded-full flex items-center justify-center">
                {{ $initials }}
            </div>
        @endif
    </div>
</div>
