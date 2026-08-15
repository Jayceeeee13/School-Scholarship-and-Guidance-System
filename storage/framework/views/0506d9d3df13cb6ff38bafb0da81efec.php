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

    
    <div class="mb-4">
        <?php echo e($this->form); ?>

    </div>

    <?php
        $data       = $this->getReportData();
        $term1      = $data['term1'];
        $term2      = $data['term2'];
        $categories = $data['categories'];
        $rows       = $data['rows'];
        $grandT1    = $data['grand_t1'];
        $grandT2    = $data['grand_t2'];

        $schoolYear = $this->school_year_filter ?? '—';

        $catStyles = [
            'TES'  => [
                'title'    => 'background:#1d4ed8;color:#ffffff;',
                'term'     => 'background:#1d4ed8;color:#ffffff;',
                'sub'      => 'background:#dbeafe;color:#1e40af;',
                'mf'       => 'background:#eff6ff;color:#1d4ed8;',
                'subtotal' => 'background:#bfdbfe;color:#1e3a8a;font-weight:600;',
                'totalbg'  => 'background:#1d4ed8;color:#ffffff;font-weight:700;',
                'totalrow' => 'background:#eff6ff;font-weight:700;',
            ],
            'TDP'  => [
                'title'    => 'background:#047857;color:#ffffff;',
                'term'     => 'background:#047857;color:#ffffff;',
                'sub'      => 'background:#d1fae5;color:#065f46;',
                'mf'       => 'background:#ecfdf5;color:#047857;',
                'subtotal' => 'background:#a7f3d0;color:#064e3b;font-weight:600;',
                'totalbg'  => 'background:#047857;color:#ffffff;font-weight:700;',
                'totalrow' => 'background:#ecfdf5;font-weight:700;',
            ],
            'CMSP' => [
                'title'    => 'background:#6d28d9;color:#ffffff;',
                'term'     => 'background:#6d28d9;color:#ffffff;',
                'sub'      => 'background:#ede9fe;color:#5b21b6;',
                'mf'       => 'background:#f5f3ff;color:#6d28d9;',
                'subtotal' => 'background:#ddd6fe;color:#4c1d95;font-weight:600;',
                'totalbg'  => 'background:#6d28d9;color:#ffffff;font-weight:700;',
                'totalrow' => 'background:#f5f3ff;font-weight:700;',
            ],
        ];

        $subGroups = ['No. OF GRANTEES', 'PWD', 'IP', 'NONE BOARD', 'WITH BOARD'];
    ?>

    
    <div class="flex justify-end mb-4 gap-2 print:hidden">
        <a
            href="<?php echo e(route('reports.print', ['school_year' => $school_year_filter])); ?>"
            target="_blank"
            style="display:inline-flex;align-items:center;gap:6px;background-color:#16a34a;color:#ffffff;padding:8px 16px;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,.2);"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ffffff" style="width:16px;height:16px;flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.056 48.056 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
            </svg>
            <span style="color:#ffffff;font-size:14px;font-weight:600;">Print / Save as PDF</span>
        </a>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $school_year_filter): ?>
        <div class="rounded-xl border border-amber-200 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-700 p-6 text-center text-amber-800 dark:text-amber-200 text-sm font-medium">
            Please select a school year to view the report.
        </div>
    <?php else: ?>

        
        <div class="mb-4 flex items-center gap-2 flex-wrap">
            <span class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">School Year:</span>
            <span style="background:#1f2937;color:#ffffff;border-radius:9999px;font-size:11px;font-weight:700;padding:3px 12px;letter-spacing:.05em;">
                <?php echo e($schoolYear); ?>

            </span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $term1): ?>
                <span class="text-xs text-amber-600 dark:text-amber-400">⚠ No 1st Semester record found for this year.</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $term2): ?>
                <span class="text-xs text-amber-600 dark:text-amber-400">⚠ No 2nd Semester record found for this year.</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $t1 = $rows[$cat]['term1'];
                $t2 = $rows[$cat]['term2'];
                $s  = $catStyles[$cat];
            ?>

            <div class="mb-8 overflow-x-auto rounded-xl shadow-sm" style="border:1px solid #e5e7eb;">
                <table class="w-full text-xs border-collapse font-sans" style="min-width:960px;">
                    <thead>

                        
                        <tr>
                            <th colspan="23" style="<?php echo e($s['title']); ?> text-align:center;font-weight:700;font-size:13px;padding:10px 12px;border:1px solid #d1d5db;letter-spacing:.08em;text-transform:uppercase;">
                                TOTAL <?php echo e($cat); ?> GRANTEES
                            </th>
                        </tr>

                        
                        <tr>
                            <th rowspan="3" style="border:1px solid #d1d5db;background:#f9fafb;color:#374151;text-align:center;font-weight:700;padding:4px 8px;font-size:11px;vertical-align:middle;width:80px;">
                                <?php echo e($cat); ?><br>CATEGORY
                            </th>

                            <th colspan="11" style="<?php echo e($s['term']); ?> border:1px solid #d1d5db;text-align:center;font-weight:700;padding:6px 4px;font-size:11px;letter-spacing:.05em;">
                                1ST TERM
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($term1): ?>
                                    <span style="font-weight:400;font-size:10px;margin-left:4px;opacity:.85;">(<?php echo e($term1->semester); ?>)</span>
                                <?php else: ?>
                                    <span style="font-weight:400;font-size:10px;margin-left:4px;opacity:.6;">— No data —</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </th>

                            <th colspan="11" style="<?php echo e($s['term']); ?> border:1px solid #d1d5db;text-align:center;font-weight:700;padding:6px 4px;font-size:11px;letter-spacing:.05em;">
                                2ND TERM
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($term2): ?>
                                    <span style="font-weight:400;font-size:10px;margin-left:4px;opacity:.85;">(<?php echo e($term2->semester); ?>)</span>
                                <?php else: ?>
                                    <span style="font-weight:400;font-size:10px;margin-left:4px;opacity:.6;">— No data —</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </th>
                        </tr>

                        
                        <tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th colspan="2" style="<?php echo e($s['sub']); ?> border:1px solid #d1d5db;text-align:center;font-weight:600;padding:4px 2px;font-size:10px;"><?php echo e($group); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <th style="<?php echo e($s['sub']); ?> border:1px solid #d1d5db;text-align:center;font-weight:600;padding:4px 2px;font-size:10px;">TOTAL</th>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th colspan="2" style="<?php echo e($s['sub']); ?> border:1px solid #d1d5db;text-align:center;font-weight:600;padding:4px 2px;font-size:10px;"><?php echo e($group); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <th style="<?php echo e($s['sub']); ?> border:1px solid #d1d5db;text-align:center;font-weight:600;padding:4px 2px;font-size:10px;">TOTAL</th>
                        </tr>

                        
                        <tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = range(0, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th style="<?php echo e($s['mf']); ?> border:1px solid #d1d5db;text-align:center;padding:4px 2px;font-size:10px;font-weight:600;">MALE</th>
                                <th style="<?php echo e($s['mf']); ?> border:1px solid #d1d5db;text-align:center;padding:4px 2px;font-size:10px;font-weight:600;">FEMALE</th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <th style="<?php echo e($s['sub']); ?> border:1px solid #d1d5db;text-align:center;padding:4px 2px;font-size:10px;font-weight:700;">TOTAL</th>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = range(0, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th style="<?php echo e($s['mf']); ?> border:1px solid #d1d5db;text-align:center;padding:4px 2px;font-size:10px;font-weight:600;">MALE</th>
                                <th style="<?php echo e($s['mf']); ?> border:1px solid #d1d5db;text-align:center;padding:4px 2px;font-size:10px;font-weight:600;">FEMALE</th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <th style="<?php echo e($s['sub']); ?> border:1px solid #d1d5db;text-align:center;padding:4px 2px;font-size:10px;font-weight:700;">TOTAL</th>
                        </tr>
                    </thead>

                    <tbody>
                        
                        <tr>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 6px;font-size:11px;font-weight:700;color:#374151;background:#f9fafb;">
                                <?php echo e($cat); ?>

                            </td>

                            
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t1['total_male'] ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t1['total_female'] ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t1['pwd_male'] ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t1['pwd_female'] ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t1['ip_male'] ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t1['ip_female'] ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t1['none_board_male'] ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t1['none_board_female'] ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t1['with_board_male'] ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t1['with_board_female'] ?: ''); ?></td>
                            <td style="<?php echo e($s['subtotal']); ?> border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t1['total'] ?: ''); ?></td>

                            
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t2['total_male'] ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t2['total_female'] ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t2['pwd_male'] ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t2['pwd_female'] ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t2['ip_male'] ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t2['ip_female'] ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t2['none_board_male'] ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t2['none_board_female'] ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t2['with_board_male'] ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t2['with_board_female'] ?: ''); ?></td>
                            <td style="<?php echo e($s['subtotal']); ?> border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t2['total'] ?: ''); ?></td>
                        </tr>

                        
                        <tr style="<?php echo e($s['totalrow']); ?>">
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 6px;font-size:11px;">TOTAL</td>

                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;" colspan="2"><?php echo e(($t1['total_male'] + $t1['total_female']) ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;" colspan="2"><?php echo e(($t1['pwd_male'] + $t1['pwd_female']) ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;" colspan="2"><?php echo e(($t1['ip_male'] + $t1['ip_female']) ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;" colspan="2"><?php echo e(($t1['none_board_male'] + $t1['none_board_female']) ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;" colspan="2"><?php echo e(($t1['with_board_male'] + $t1['with_board_female']) ?: ''); ?></td>
                            <td style="<?php echo e($s['totalbg']); ?> border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t1['total']); ?></td>

                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;" colspan="2"><?php echo e(($t2['total_male'] + $t2['total_female']) ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;" colspan="2"><?php echo e(($t2['pwd_male'] + $t2['pwd_female']) ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;" colspan="2"><?php echo e(($t2['ip_male'] + $t2['ip_female']) ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;" colspan="2"><?php echo e(($t2['none_board_male'] + $t2['none_board_female']) ?: ''); ?></td>
                            <td style="border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;" colspan="2"><?php echo e(($t2['with_board_male'] + $t2['with_board_female']) ?: ''); ?></td>
                            <td style="<?php echo e($s['totalbg']); ?> border:1px solid #d1d5db;text-align:center;padding:8px 4px;font-size:11px;"><?php echo e($t2['total']); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\sample\resources\views/filament/pages/reports-page.blade.php ENDPATH**/ ?>