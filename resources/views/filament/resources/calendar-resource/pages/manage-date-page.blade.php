<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Events Card --}}
        <div style="border-radius:16px;border:1px solid #e5e7eb;padding:24px;background:white;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                <div style="width:36px;height:36px;border-radius:10px;background:#fef2f2;display:flex;align-items:center;justify-content:center;">
                    <x-heroicon-o-calendar style="width:20px;height:20px;color:#dc2626;"/>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <h3 style="font-weight:600;color:#111827;font-size:1rem;margin:0;">Events on this date</h3>
                    <span style="background:#fee2e2;color:#dc2626;font-size:12px;font-weight:600;padding:2px 10px;border-radius:20px;">
                        {{ count($events) }}
                    </span>
                </div>
            </div>

            @if(count($events) > 0)
                <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:20px;">
                    @foreach($events as $event)
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;padding:16px;border-radius:12px;border:1.5px solid #fed7aa;background:linear-gradient(135deg,#fff7ed,#ffedd5);">
                            <div style="display:flex;align-items:flex-start;gap:12px;">
                                <div style="width:36px;height:36px;border-radius:50%;background:#ffedd5;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <x-heroicon-o-x-circle style="width:20px;height:20px;color:#ea580c;"/>
                                </div>
                                <div>
                                    <h4 style="font-weight:600;color:#9a3412;font-size:0.9375rem;margin:0 0 4px 0;">{{ $event->title }}</h4>
                                    @if($event->reason)
                                        <p style="font-size:0.8125rem;color:#c2410c;margin:0;">{{ $event->reason }}</p>
                                    @endif
                                </div>
                            </div>
                            <button
                                wire:click="deleteEvent({{ $event->id }})"
                                wire:confirm="Remove this event?"
                                style="background:#fee2e2;color:#dc2626;padding:6px 12px;border-radius:8px;font-size:12px;font-weight:600;border:none;cursor:pointer;flex-shrink:0;"
                                onmouseover="this.style.background='#fecaca'"
                                onmouseout="this.style.background='#fee2e2'">
                                Remove
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align:center;padding:24px 0;color:#9ca3af;margin-bottom:20px;">
                    <p style="font-size:0.875rem;margin:0;font-weight:500;">No events for this date yet</p>
                </div>
            @endif

            {{-- Add Event Form --}}
            <div style="border-top:1px solid #e5e7eb;padding-top:20px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;">
                        <x-heroicon-o-plus-circle style="width:20px;height:20px;color:#16a34a;"/>
                    </div>
                    <h3 style="font-weight:600;color:#111827;font-size:1rem;margin:0;">Add Event</h3>
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:0.875rem;font-weight:500;color:#374151;margin-bottom:6px;">
                        Title <span style="color:#dc2626;">*</span>
                    </label>
                    <input
                        type="text"
                        wire:model="newTitle"
                        placeholder="e.g. Holiday, School event, Meeting..."
                        style="width:100%;border:1.5px solid #d1d5db;border-radius:10px;padding:10px 14px;font-size:14px;outline:none;transition:border 0.2s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#16a34a'"
                        onblur="this.style.borderColor='#d1d5db'"
                    />
                    @error('newTitle') <p style="color:#dc2626;font-size:12px;margin:6px 0 0 0;">{{ $message }}</p> @enderror
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:0.875rem;font-weight:500;color:#374151;margin-bottom:6px;">
                        Reason <span style="color:#9ca3af;font-weight:400;">(optional)</span>
                    </label>
                    <input
                        type="text"
                        wire:model="newReason"
                        placeholder="Additional details..."
                        style="width:100%;border:1.5px solid #d1d5db;border-radius:10px;padding:10px 14px;font-size:14px;outline:none;transition:border 0.2s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#16a34a'"
                        onblur="this.style.borderColor='#d1d5db'"
                    />
                </div>

                <div style="display:flex;justify-content:flex-end;">
                    <button
                        wire:click="addEvent"
                        style="background:linear-gradient(135deg,#059669,#047857);color:white;padding:10px 24px;border-radius:10px;font-size:14px;font-weight:600;border:none;cursor:pointer;display:flex;align-items:center;gap:8px;box-shadow:0 2px 4px rgba(5,150,105,0.3);"
                        onmouseover="this.style.background='linear-gradient(135deg,#047857,#065f46)'"
                        onmouseout="this.style.background='linear-gradient(135deg,#059669,#047857)'">
                        + Add Event
                    </button>
                </div>
            </div>
        </div>

        {{-- Appointments on this date --}}
        <div style="border-radius:16px;border:1px solid #e5e7eb;padding:24px;background:white;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                <div style="width:36px;height:36px;border-radius:10px;background:#eff6ff;display:flex;align-items:center;justify-content:center;">
                    <x-heroicon-o-calendar-days style="width:20px;height:20px;color:#2563eb;"/>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <h3 style="font-weight:600;color:#111827;font-size:1rem;margin:0;">Appointments on this date</h3>
                    <span style="background:#dbeafe;color:#1d4ed8;font-size:12px;font-weight:600;padding:2px 10px;border-radius:20px;">
                        {{ count($appointments) }}
                    </span>
                </div>
            </div>

            @if(count($appointments) > 0)
                <div style="display:flex;flex-direction:column;gap:12px;">
                    @foreach($appointments as $appointment)
                        <a href="{{ route('filament.admin.resources.counseling-appointments.edit', $appointment->id) }}"
                           style="display:block;padding:16px;border-radius:12px;border:1.5px solid;text-decoration:none;transition:box-shadow 0.2s;
                               {{ $appointment->status === 'pending' ? 'border-color:#fed7aa;background:#fff7ed;' : '' }}
                               {{ $appointment->status === 'approved' ? 'border-color:#bbf7d0;background:#f0fdf4;' : '' }}
                               {{ $appointment->status === 'rejected' ? 'border-color:#fecaca;background:#fef2f2;' : '' }}"
                           onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'"
                           onmouseout="this.style.boxShadow='none'">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                                <div>
                                    <h4 style="font-weight:600;color:#111827;font-size:1rem;margin:0 0 6px 0;">
                                        {{ $appointment->first_name }} {{ $appointment->last_name }}
                                    </h4>
                                    <div style="display:flex;gap:16px;font-size:13px;color:#6b7280;">
                                        <span>🕐 {{ $appointment->timeSlot?->name ?? 'No time' }}</span>
                                        <span>💻 {{ $appointment->modeOfCounseling?->name ?? 'N/A' }}</span>
                                        @if($appointment->supportNeeded)
                                            <span>💙 {{ $appointment->supportNeeded->name }}</span>
                                        @endif
                                    </div>
                                </div>
                                <span style="flex-shrink:0;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;
                                    {{ $appointment->status === 'pending' ? 'background:#fed7aa;color:#c2410c;' : '' }}
                                    {{ $appointment->status === 'approved' ? 'background:#bbf7d0;color:#15803d;' : '' }}
                                    {{ $appointment->status === 'rejected' ? 'background:#fecaca;color:#dc2626;' : '' }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div style="text-align:center;padding:40px 0;color:#9ca3af;">
                    <x-heroicon-o-calendar style="width:48px;height:48px;margin:0 auto 12px;color:#d1d5db;"/>
                    <p style="font-size:0.875rem;margin:0;font-weight:500;">No appointments on this date</p>
                    <p style="font-size:0.8125rem;margin:4px 0 0 0;color:#d1d5db;">Click a date with appointments to view them here</p>
                </div>
            @endif
        </div>

    </div>
</x-filament-panels::page>