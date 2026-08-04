@switch($name)
    @case('dashboard')
        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/>
            <rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/>
            <rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/>
            <rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/>
        </svg>
        @break
    @case('applications')
        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M8 3h8l3 3v15H5V3h3Z"/>
        </svg>
        @break
    @case('payments')
        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <rect x="3" y="5" width="18" height="14" rx="2" stroke-width="2"/>
            <path d="M3 10h18" stroke-width="2"/>
        </svg>
        @break
    @case('office')
        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" stroke-linejoin="round" d="M4 21V5l8-3 8 3v16M9 21v-4h6v4M8 8h1m6 0h1M8 12h1m6 0h1"/>
        </svg>
        @break
    @case('consultations')
        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h7m-9 9 3-3h9a4 4 0 0 0 4-4V7a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v7a4 4 0 0 0 2 3.46V21Z"/>
        </svg>
        @break
    @default
        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="3" stroke-width="2"/>
            <path stroke-width="2" stroke-linecap="round" d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.12 2.12-.06-.06a1.7 1.7 0 0 0-1.88-.34 1.7 1.7 0 0 0-1.03 1.56V20.3h-3v-.08a1.7 1.7 0 0 0-1.03-1.56 1.7 1.7 0 0 0-1.88.34l-.06.06-2.12-2.12.06-.06A1.7 1.7 0 0 0 7.02 15a1.7 1.7 0 0 0-1.56-1.03H5.4v-3h.06A1.7 1.7 0 0 0 7.02 9.94a1.7 1.7 0 0 0-.34-1.88L6.62 8l2.12-2.12.06.06a1.7 1.7 0 0 0 1.88.34A1.7 1.7 0 0 0 11.71 4.7v-.08h3v.08a1.7 1.7 0 0 0 1.03 1.56 1.7 1.7 0 0 0 1.88-.34l.06-.06L19.8 8l-.06.06a1.7 1.7 0 0 0-.34 1.88 1.7 1.7 0 0 0 1.56 1.03h.08v3h-.08A1.7 1.7 0 0 0 19.4 15Z"/>
        </svg>
@endswitch
