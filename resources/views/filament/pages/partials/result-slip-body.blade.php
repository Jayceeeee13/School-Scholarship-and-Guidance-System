<div class="info-section">
    <div class="info-row">
      <span class="info-label">Name &nbsp;:</span>
      <div class="info-value">{{ $record->user->name ?? 'Unknown Examinee' }}</div>
    </div>
    <div class="info-row">
      <span class="info-label">Contact No.:</span>
      <div class="info-value">{{ $record->user->contact_no ?? '—' }}</div>
    </div>
    <div class="info-row">
      <span class="info-label">Date of Examination:</span>
      <div class="info-value">{{ $examDate }}</div>
    </div>
  </div>

  <div class="body-grid">

    {{-- Left: Score Guidelines --}}
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

    {{-- Right: Key Areas with actual scores --}}
    <div>
      <table class="areas-table">
        <thead>
          <tr><th>KEY AREAS</th><th>SCORE</th></tr>
        </thead>
        <tbody>
          @foreach ($categoryScores as $category => $scores)
            <tr>
              <td>{{ $category }}</td>
              <td>{{ $scores['correct'] }}/{{ $scores['total'] }}</td>
            </tr>
          @endforeach
          <tr class="total-row">
            <td>TOTAL</td>
            <td>{{ $percentage }}</td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>

  {{-- Discount result --}}
  <div class="discount-result">
    Scholarship Result:&nbsp;
    <strong>{{ $discount ?: 'No discount awarded' }}</strong>
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

  <div class="footer">FM-AAD-064 &nbsp;|&nbsp; Dated 14 April 2025</div>