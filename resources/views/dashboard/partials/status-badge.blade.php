@php
    $statusData = match ($status) {
        'waiting_payment' => [
            'text' => 'بانتظار الدفع',
            'class' => 'bg-orange-500/10 text-orange-300',
        ],

        'pending' => [
            'text' => 'قيد الانتظار',
            'class' => 'bg-yellow-500/10 text-yellow-300',
        ],

        'in_progress' => [
            'text' => 'قيد التنفيذ',
            'class' => 'bg-blue-500/10 text-blue-300',
        ],

        'completed' => [
            'text' => 'مكتملة',
            'class' => 'bg-green-500/10 text-green-300',
        ],

        'cancelled' => [
            'text' => 'ملغاة',
            'class' => 'bg-red-500/10 text-red-300',
        ],

        default => [
            'text' => $status,
            'class' => 'bg-slate-500/10 text-slate-300',
        ],
    };
@endphp

<span
    class="inline-flex px-3 py-1 text-xs font-bold rounded-full {{ $statusData['class'] }}"
>
    {{ $statusData['text'] }}
</span>
