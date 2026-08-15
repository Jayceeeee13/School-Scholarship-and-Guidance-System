<div class="info-section">
    <div class="info-row">
      <span class="info-label">Name &nbsp;:</span>
      <div class="info-value"><?php echo e($record->user->name ?? 'Unknown Examinee'); ?></div>
    </div>
    <div class="info-row">
      <span class="info-label">Contact No.:</span>
      <div class="info-value"><?php echo e($record->user->contact_no ?? '—'); ?></div>
    </div>
    <div class="info-row">
      <span class="info-label">Date of Examination:</span>
      <div class="info-value"><?php echo e($examDate); ?></div>
    </div>
  </div>

  <div class="body-grid">

    
    <div>
      <table class="score-table">
        <thead>
          <tr><th colspan="2">SCORE GUIDELINES</th></tr>
        </thead>
        <tbody>
          <tr><td>95 – 100</td><td>100% Tuition Fee &amp; Misc. Discount</td></tr>
          <tr><td>85 – 94</td><td>100% Tuition Fee Discount</td></tr>
          <tr><td>75 – 84</td><td>75% Tuition Fee Discount</td></tr>
          <tr><td>65 – 74</td><td>50% Tuition Fee Discount</td></tr>
          <tr><td>60 – 65</td><td>25% Tuition Fee Discount</td></tr>
          <tr><td>50 – 59</td><td>10% Tuition Fee Discount</td></tr>
        </tbody>
      </table>
    </div>

    
    <div>
      <table class="areas-table">
        <thead>
          <tr><th>KEY AREAS</th><th>SCORE</th></tr>
        </thead>
        <tbody>
          <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categoryScores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $scores): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td><?php echo e($category); ?></td>
              <td><?php echo e($scores['correct']); ?>/<?php echo e($scores['total']); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
          <tr class="total-row">
            <td>TOTAL</td>
            <td><?php echo e($percentage); ?></td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>

  
  <div class="discount-result">
    Scholarship Result:&nbsp;
    <strong><?php echo e($discount ?: 'No discount awarded'); ?></strong>
  </div>

  <hr class="thin">

  <div class="signature-section">
    <div class="sig-row">
      <span class="sig-label">Evaluator:</span>
      <div class="sig-underline"></div>
    </div>
    <div class="sig-row">
      <span class="sig-label">Date Released:</span>
      <div class="sig-underline"></div>
    </div>
  </div>

  <div class="footer">FM-AAD-064 &nbsp;|&nbsp; Dated 14 April 2025</div><?php /**PATH C:\xampp\htdocs\sample\resources\views/filament/pages/partials/result-slip-body.blade.php ENDPATH**/ ?>