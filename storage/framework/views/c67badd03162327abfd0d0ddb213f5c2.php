<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="space-y-6">

        
        <div style="border-radius:16px;border:1px solid #e5e7eb;padding:24px;background:white;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                <div style="width:36px;height:36px;border-radius:10px;background:#fef2f2;display:flex;align-items:center;justify-content:center;">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-calendar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['style' => 'width:20px;height:20px;color:#dc2626;']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <h3 style="font-weight:600;color:#111827;font-size:1rem;margin:0;">Events on this date</h3>
                    <span style="background:#fee2e2;color:#dc2626;font-size:12px;font-weight:600;padding:2px 10px;border-radius:20px;">
                        <?php echo e(count($events)); ?>

                    </span>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($events) > 0): ?>
                <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:20px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;padding:16px;border-radius:12px;border:1.5px solid #fed7aa;background:linear-gradient(135deg,#fff7ed,#ffedd5);">
                            <div style="display:flex;align-items:flex-start;gap:12px;">
                                <div style="width:36px;height:36px;border-radius:50%;background:#ffedd5;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-x-circle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['style' => 'width:20px;height:20px;color:#ea580c;']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                </div>
                                <div>
                                    <h4 style="font-weight:600;color:#9a3412;font-size:0.9375rem;margin:0 0 4px 0;"><?php echo e($event->title); ?></h4>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($event->reason): ?>
                                        <p style="font-size:0.8125rem;color:#c2410c;margin:0;"><?php echo e($event->reason); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <button
                                wire:click="deleteEvent(<?php echo e($event->id); ?>)"
                                wire:confirm="Remove this event?"
                                style="background:#fee2e2;color:#dc2626;padding:6px 12px;border-radius:8px;font-size:12px;font-weight:600;border:none;cursor:pointer;flex-shrink:0;"
                                onmouseover="this.style.background='#fecaca'"
                                onmouseout="this.style.background='#fee2e2'">
                                Remove
                            </button>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php else: ?>
                <div style="text-align:center;padding:24px 0;color:#9ca3af;margin-bottom:20px;">
                    <p style="font-size:0.875rem;margin:0;font-weight:500;">No events for this date yet</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div style="border-top:1px solid #e5e7eb;padding-top:20px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;">
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-plus-circle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['style' => 'width:20px;height:20px;color:#16a34a;']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newTitle'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p style="color:#dc2626;font-size:12px;margin:6px 0 0 0;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

        
        <div style="border-radius:16px;border:1px solid #e5e7eb;padding:24px;background:white;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                <div style="width:36px;height:36px;border-radius:10px;background:#eff6ff;display:flex;align-items:center;justify-content:center;">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-calendar-days'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['style' => 'width:20px;height:20px;color:#2563eb;']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <h3 style="font-weight:600;color:#111827;font-size:1rem;margin:0;">Appointments on this date</h3>
                    <span style="background:#dbeafe;color:#1d4ed8;font-size:12px;font-weight:600;padding:2px 10px;border-radius:20px;">
                        <?php echo e(count($appointments)); ?>

                    </span>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($appointments) > 0): ?>
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('filament.admin.resources.counseling-appointments.edit', $appointment->id)); ?>"
                           style="display:block;padding:16px;border-radius:12px;border:1.5px solid;text-decoration:none;transition:box-shadow 0.2s;
                               <?php echo e($appointment->status === 'pending' ? 'border-color:#fed7aa;background:#fff7ed;' : ''); ?>

                               <?php echo e($appointment->status === 'approved' ? 'border-color:#bbf7d0;background:#f0fdf4;' : ''); ?>

                               <?php echo e($appointment->status === 'rejected' ? 'border-color:#fecaca;background:#fef2f2;' : ''); ?>"
                           onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'"
                           onmouseout="this.style.boxShadow='none'">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                                <div>
                                    <h4 style="font-weight:600;color:#111827;font-size:1rem;margin:0 0 6px 0;">
                                        <?php echo e($appointment->first_name); ?> <?php echo e($appointment->last_name); ?>

                                    </h4>
                                    <div style="display:flex;gap:16px;font-size:13px;color:#6b7280;">
                                        <span>🕐 <?php echo e($appointment->timeSlot?->name ?? 'No time'); ?></span>
                                        <span>💻 <?php echo e($appointment->modeOfCounseling?->name ?? 'N/A'); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($appointment->supportNeeded): ?>
                                            <span>💙 <?php echo e($appointment->supportNeeded->name); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <span style="flex-shrink:0;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;
                                    <?php echo e($appointment->status === 'pending' ? 'background:#fed7aa;color:#c2410c;' : ''); ?>

                                    <?php echo e($appointment->status === 'approved' ? 'background:#bbf7d0;color:#15803d;' : ''); ?>

                                    <?php echo e($appointment->status === 'rejected' ? 'background:#fecaca;color:#dc2626;' : ''); ?>">
                                    <?php echo e(ucfirst($appointment->status)); ?>

                                </span>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php else: ?>
                <div style="text-align:center;padding:40px 0;color:#9ca3af;">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-calendar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['style' => 'width:48px;height:48px;margin:0 auto 12px;color:#d1d5db;']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                    <p style="font-size:0.875rem;margin:0;font-weight:500;">No appointments on this date</p>
                    <p style="font-size:0.8125rem;margin:4px 0 0 0;color:#d1d5db;">Click a date with appointments to view them here</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\sample\resources\views/filament/resources/calendar-resource/pages/manage-date-page.blade.php ENDPATH**/ ?>