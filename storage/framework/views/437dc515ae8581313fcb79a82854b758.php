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
    <style>
        .fc .fc-button-primary {
            background-color: #059669 !important;
            border-color: #059669 !important;
        }
        .fc .fc-button-primary:hover {
            background-color: #047857 !important;
            border-color: #047857 !important;
        }
        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background-color: #047857 !important;
            border-color: #047857 !important;
        }
        .fc-day-today {
            background-color: rgba(5, 150, 105, 0.1) !important;
        }
        .fc-bg-event {
            opacity: 0.4 !important;
        }
        .fc-daygrid-day {
            cursor: pointer !important;
        }
    </style>

    
    <div class="mb-4 flex gap-4 flex-wrap items-center">
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded" style="background-color: #f59e0b;"></div>
            <span class="text-sm text-gray-600 dark:text-gray-400">Pending</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded" style="background-color: #10b981;"></div>
            <span class="text-sm text-gray-600 dark:text-gray-400">Approved</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded" style="background-color: #ef4444;"></div>
            <span class="text-sm text-gray-600 dark:text-gray-400">Rejected</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded" style="background-color: #fecaca; border: 1px solid #ef4444;"></div>
            <span class="text-sm text-gray-600 dark:text-gray-400">Inactive / Unavailable</span>
        </div>
        <div class="ml-auto text-sm text-gray-500 dark:text-gray-400 italic">
            💡 Click any date to manage it
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
<?php endif; ?><?php /**PATH C:\xampp\htdocs\sample\resources\views/filament/resources/counseling-appointments-resource/pages/calendar-appointments.blade.php ENDPATH**/ ?>