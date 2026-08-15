<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-filament::icon
                        icon="heroicon-o-calendar-days"
                        class="h-5 w-5"
                    />
                    <span>This Week's Appointments</span>
                </div>
                <x-filament::button
                    tag="a"
                    href="{{ \App\Filament\Resources\CounselingAppointmentsResource::getUrl('calendar') }}"
                    size="sm"
                    color="gray"
                    outlined
                >
                    View Full Calendar
                </x-filament::button>
            </div>
        </x-slot>

        {{-- Today's Appointments --}}
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                📅 Today ({{ now()->format('l, F j, Y') }})
            </h3>
            
            @php $todayAppointments = $this->getTodayAppointments(); @endphp
            
            @if($todayAppointments->count() > 0)
                <div class="space-y-2">
                    @foreach($todayAppointments as $appointment)
                        <a href="{{ \App\Filament\Resources\CounselingAppointmentsResource::getUrl('edit', ['record' => $appointment->id]) }}"
                           class="block p-3 rounded-lg border transition hover:shadow-md
                                  @if($appointment->status === 'pending') border-orange-200 bg-orange-50 dark:border-orange-800 dark:bg-orange-950
                                  @elseif($appointment->status === 'approved') border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950
                                  @else border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950
                                  @endif">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        <x-filament::icon
                                            icon="heroicon-o-clock"
                                            class="h-5 w-5 text-gray-500"
                                        />
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">
                                            {{ $appointment->first_name }} {{ $appointment->last_name }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $appointment->timeSlot?->name ?? 'No time set' }}
                                        </p>
                                    </div>
                                </div>
                                <x-filament::badge
                                    :color="match($appointment->status) {
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        default => 'gray'
                                    }"
                                >
                                    {{ ucfirst($appointment->status) }}
                                </x-filament::badge>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                    <x-filament::icon
                        icon="heroicon-o-calendar-days"
                        class="h-12 w-12 mx-auto mb-2 opacity-50"
                    />
                    <p>No appointments scheduled for today</p>
                </div>
            @endif
        </div>

        {{-- This Week Overview --}}
        <div>
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                📆 This Week Overview
            </h3>
            
            @php 
                $appointments = $this->getAppointments();
                $groupedByDate = $appointments->groupBy(function($item) {
                    return $item->counseling_date->format('Y-m-d');
                });
            @endphp
            
            @if($groupedByDate->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($groupedByDate as $date => $dayAppointments)
                        <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            <p class="font-semibold text-sm text-gray-700 dark:text-gray-300 mb-2">
                                {{ \Carbon\Carbon::parse($date)->format('D, M j') }}
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                {{ $dayAppointments->count() }} {{ $dayAppointments->count() === 1 ? 'appointment' : 'appointments' }}
                            </p>
                            <div class="mt-2 flex gap-1">
                                @foreach($dayAppointments->take(3) as $apt)
                                    <div class="w-2 h-2 rounded-full
                                        @if($apt->status === 'pending') bg-orange-400
                                        @elseif($apt->status === 'approved') bg-green-400
                                        @else bg-red-400
                                        @endif">
                                    </div>
                                @endforeach
                                @if($dayAppointments->count() > 3)
                                    <span class="text-xs text-gray-500">+{{ $dayAppointments->count() - 3 }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                    <p>No appointments this week</p>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>