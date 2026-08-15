<div class="space-y-3">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $record->activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3 text-sm">
            <div class="flex items-center justify-between gap-3">
                <span class="font-semibold"><?php echo e($activity->activity_date?->format('M d, Y') ?? '—'); ?></span>
                <span class="text-gray-500 dark:text-gray-400"><?php echo e($activity->venue ?? '—'); ?></span>
            </div>
            <p class="mt-1 text-gray-700 dark:text-gray-300"><?php echo e($activity->activity ?? '—'); ?></p>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-gray-500 dark:text-gray-400">No activities recorded.</p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($record->status !== 'pending' && $record->remarks): ?>
        <div class="rounded-lg bg-gray-50 dark:bg-gray-800 p-3 text-sm">
            <span class="font-semibold">Office remarks:</span> <?php echo e($record->remarks); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\xampp\htdocs\sample\resources\views/filament/accomplishment-report-activities-modal.blade.php ENDPATH**/ ?>