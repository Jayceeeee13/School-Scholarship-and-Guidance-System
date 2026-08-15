<div
    <?php echo e($attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)); ?>

>
    <?php echo e($getChildComponentContainer()); ?>

</div>
<?php /**PATH /home/u476045238/domains/gvcfiguidancesc.com/public_html/vendor/filament/forms/resources/views/components/grid.blade.php ENDPATH**/ ?>