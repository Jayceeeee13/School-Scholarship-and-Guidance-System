
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Result Slip – <?php echo e($record->user->name ?? 'Unknown Examinee'); ?></title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: 'Times New Roman', Times, serif;
  font-size: 10pt;
  color: #000;
  background: #f0f0f0;
  padding: 16px;
}

.print-btn-wrap {
  text-align: center;
  margin: 0 0 16px;
}
.print-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 28px;
  background: #1a56db;
  color: #fff;
  border: none;
  border-radius: 6px;
  font-size: 12pt;
  font-family: system-ui, sans-serif;
  cursor: pointer;
}
.print-btn:hover { background: #1e429f; }

/* ── Outer wrapper: two slips side by side ── */
.sheet {
  width: 297mm;
  margin: 0 auto;
  background: #fff;
  display: flex;
  align-items: stretch;
  position: relative;
}

/* ── Cut line between the two slips ── */
.cut-line {
  width: 40px;
  flex-shrink: 0;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.cut-line::after {
  content: '';
  position: absolute;
  top: 0;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 2px;
  background: repeating-linear-gradient(
    to bottom,
    #000 0px, #000 6px,
    transparent 6px, transparent 12px
  );
}

.cut-icon {
  background: #fff;
  padding: 2px;
  font-size: 14px;
  line-height: 1;
  z-index: 2;
  position: relative;
  user-select: none;
}

/* ── Each individual slip ── */
.slip {
  flex: 1;
  display: flex;
  flex-direction: column;
}

/* -----------------------------------------
   HEADER (small top block)
   +--------+-----------------------------+
   |        | School Name + Address (r1)  |
   |  Logo  +-----------------------------|
   |        | Document Title       (r2)   |
   +--------+-----------------------------+
----------------------------------------- */
.slip-header {
  width: 100%;
  border-collapse: collapse;
  border: 1.5px solid #000;
  margin-bottom: 0;
}

.slip-header td {
  border: 1px solid #000;
  vertical-align: middle;
}

.slip-header .logo-cell {
  width: 70px;
  text-align: center;
  padding: 6px 8px;
  vertical-align: middle;
}

.slip-header .logo-cell img {
  width: 54px;
  height: 54px;
  object-fit: contain;
}

.slip-header .name-cell {
  padding: 6px 10px 4px;
  text-align: center;
  border-bottom: 1px solid #000;
  vertical-align: middle;
}

.slip-header .name-cell .school-name {
  font-size: 9.5pt;
  font-weight: bold;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  display: block;
}

.slip-header .name-cell .school-address {
  font-size: 7pt;
  display: block;
  margin-top: 1px;
}

.slip-header .title-cell {
  padding: 6px 10px;
  text-align: center;
  vertical-align: middle;
}

.slip-header .title-cell .doc-title {
  font-size: 11pt;
  font-weight: bold;
  text-transform: uppercase;
  letter-spacing: 1px;
}

/* -----------------------------------------
   CONTENT BOX (large bordered area below header)
----------------------------------------- */
.content-box {
  border: 1.5px solid #000;
  border-top: none;
  padding: 8mm 10mm 9mm;
  flex: 1;
  display: flex;
  flex-direction: column;
}

/* ── Inner content styles ── */
hr.thick { border: none; border-top: 2px solid #000; margin: 5px 0; }
hr.thin  { border: none; border-top: 1px solid #000; margin: 4px 0; }

/* Remove stray leading hr at the very top of the content box */
.content-box > hr:first-child,
.content-box > hr.thick:first-child,
.content-box > hr.thin:first-child {
  display: none;
}

.info-section { margin: 6px 0 4px; }
.info-row {
  display: flex;
  align-items: flex-end;
  gap: 5px;
  margin-bottom: 5px;
}
.info-label { font-size: 9.5pt; white-space: nowrap; }
.info-value {
  flex: 1;
  border-bottom: 1px solid #000;
  min-height: 14px;
  font-size: 9.5pt;
  padding-left: 3px;
  padding-bottom: 1px;
}

.body-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0 12px;
  margin: 8px 0 6px;
  align-items: start;
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 8.5pt;
}
th, td {
  border: 1px solid #000;
  padding: 2px 5px;
}
th { font-weight: bold; text-align: center; }

.score-table td:first-child {
  text-align: center;
  white-space: nowrap;
  width: 52px;
}
.score-table td:last-child { font-weight: bold; font-size: 8pt; }

.areas-table th:last-child,
.areas-table td:last-child {
  text-align: center;
  width: 55px;
}
.areas-table tr.total-row td {
  font-weight: bold;
  border-top: 2px solid #000;
}

.discount-result {
  font-size: 9pt;
  text-align: center;
  margin: 4px 0 6px;
  line-height: 1.5;
  padding: 4px 6px;
  border: 1px solid #000;
}
.discount-result strong { font-size: 10pt; }

.signature-section { margin-top: 10px; }
.sig-row {
  display: flex;
  align-items: flex-end;
  gap: 6px;
  margin-bottom: 12px;
}
.sig-label { font-size: 9.5pt; white-space: nowrap; }
.sig-underline { flex: 0 0 180px; border-bottom: 1px solid #000; min-height: 13px; }

.footer { text-align: right; font-size: 7.5pt; margin-top: 10px; }

@media print {
  .print-btn-wrap { display: none; }
  body { background: #fff; padding: 0; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
  @page { size: A4 landscape; margin: 5mm; }
}
</style>
</head>
<body>

<div class="print-btn-wrap">
  <button class="print-btn" onclick="window.print()">
    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round"
            d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1
               2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2m-6
               0h4v4H10v-4z"/>
    </svg>
    Print Result Slip
  </button>
</div>

<?php
  $examDate     = $record->created_at?->format('F d, Y') ?? '—';
  $totalCorrect = $categoryScores->sum('correct');
  $totalItems   = $categoryScores->sum('total');

  $slipData = [
    'logoSrc'        => asset('images/gvcf-logo.png'),
    'record'         => $record,
    'examDate'       => $examDate,
    'categoryScores' => $categoryScores,
    'percentage'     => $percentage,
    'discount'       => $discount,
    'totalCorrect'   => $totalCorrect,
    'totalItems'     => $totalItems,
  ];
?>

<div class="sheet">

  
  <div class="slip">

    
    <table class="slip-header">
      <tr>
        <td class="logo-cell" rowspan="2">
          <img src="<?php echo e(asset('images/logo.png')); ?>" alt="GVCF Logo">
        </td>
        <td class="name-cell">
          <span class="school-name">Green Valley College Foundation, Inc.</span>
          <span class="school-address">Km. 2, Bo.2, Gensan Dr., Koronadal City, South Cotabato</span>
        </td>
      </tr>
      <tr>
        <td class="title-cell">
          <span class="doc-title">ADMISSION AND SCHOLARSHIP TEST RESULT</span>
        </td>
      </tr>
    </table>

    
    <div class="content-box">
      <?php echo $__env->make('filament.pages.partials.result-slip-body', $slipData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

  </div>

  
  <div class="cut-line">
    <span class="cut-icon">✂</span>
  </div>

  
  <div class="slip">

    
    <table class="slip-header">
      <tr>
        <td class="logo-cell" rowspan="2">
          <img src="<?php echo e(asset('images/logo.png')); ?>" alt="GVCF Logo">
        </td>
        <td class="name-cell">
          <span class="school-name">Green Valley College Foundation, Inc.</span>
          <span class="school-address">Km. 2, Bo.2, Gensan Dr., Koronadal City, South Cotabato</span>
        </td>
      </tr>
      <tr>
        <td class="title-cell">
          <span class="doc-title">ADMISSION AND SCHOLARSHIP TEST RESULT</span>
        </td>
      </tr>
    </table>

    
    <div class="content-box">
      <?php echo $__env->make('filament.pages.partials.result-slip-body', $slipData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

  </div>

</div>

</body>
</html><?php /**PATH C:\xampp\htdocs\sample\resources\views/filament/pages/exam-result-slip.blade.php ENDPATH**/ ?>