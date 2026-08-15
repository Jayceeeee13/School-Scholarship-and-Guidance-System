<x-filament-widgets::widget>
    <x-filament::card>

        {{-- Stat cards --}}
        <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:1.5rem;">

            <div style="background:#EFF6FF;border-radius:8px;padding:1rem 1.25rem;display:flex;align-items:center;gap:12px;">
                <div style="width:36px;height:36px;border-radius:8px;background:#DBEAFE;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="#1D4ED8" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <p style="font-size:12px;color:#6b7280;margin:0 0 2px;">Total scholars</p>
                    <p style="font-size:22px;font-weight:500;margin:0;color:#111827;">{{ $total }}</p>
                </div>
            </div>

            <div style="background:#F0FDF4;border-radius:8px;padding:1rem 1.25rem;display:flex;align-items:center;gap:12px;">
                <div style="width:36px;height:36px;border-radius:8px;background:#DCFCE7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="#15803D" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div>
                    <p style="font-size:12px;color:#6b7280;margin:0 0 2px;">Active</p>
                    <p style="font-size:22px;font-weight:500;margin:0;color:#15803D;">{{ $active }}</p>
                </div>
            </div>

            <div style="background:#F9FAFB;border-radius:8px;padding:1rem 1.25rem;display:flex;align-items:center;gap:12px;">
                <div style="width:36px;height:36px;border-radius:8px;background:#F3F4F6;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="#6B7280" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
                <div>
                    <p style="font-size:12px;color:#6b7280;margin:0 0 2px;">Inactive</p>
                    <p style="font-size:22px;font-weight:500;margin:0;color:#6B7280;">{{ $inactive }}</p>
                </div>
            </div>

            <div style="background:#F5F3FF;border-radius:8px;padding:1rem 1.25rem;display:flex;align-items:center;gap:12px;">
                <div style="width:36px;height:36px;border-radius:8px;background:#EDE9FE;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="#7C3AED" stroke-width="2" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                </div>
                <div>
                    <p style="font-size:12px;color:#6b7280;margin:0 0 2px;">Scholarship types</p>
                    <p style="font-size:22px;font-weight:500;margin:0;color:#7C3AED;">{{ $typeCount }}</p>
                </div>
            </div>

        </div>

        {{-- Bar chart --}}
        <div
            x-data="{
                init() {
                    const draw = () => {
                        const canvas = this.$refs.bar;
                        if (!canvas || !window.Chart) return;
                        if (canvas._chart) canvas._chart.destroy();
                        const data = {{ $chartData }};
                        const total = data.counts.reduce((a,b)=>a+b,0);
                        canvas._chart = new window.Chart(canvas, {
                            type: 'bar',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    data: data.counts,
                                    backgroundColor: data.colors,
                                    borderRadius: 6,
                                    borderSkipped: false,
                                    barPercentage: 0.6,
                                    categoryPercentage: 0.7,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                layout: {
                                    padding: { bottom: 50 }
                                },
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        callbacks: {
                                            label: (ctx) => {
                                                const pct = total > 0 ? Math.round(ctx.parsed.y / total * 100) : 0;
                                                return ' ' + ctx.parsed.y + ' scholars (' + pct + '%)';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: { display: false },
                                        ticks: { display: false },
                                        border: { display: false }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: '#f3f4f6' },
                                        ticks: {
                                            font: { size: 11 },
                                            color: '#9ca3af',
                                            precision: 0,
                                        }
                                    }
                                }
                            },
                            plugins: [{
                                id: 'belowBarLabels',
                                afterDraw(chart) {
                                    const ctx = chart.ctx;
                                    const total = data.counts.reduce((a,b)=>a+b,0);
                                    chart.data.datasets.forEach((dataset, datasetIndex) => {
                                        const meta = chart.getDatasetMeta(datasetIndex);
                                        meta.data.forEach((bar, index) => {
                                            const count = dataset.data[index];
                                            const pct   = total > 0 ? Math.round(count / total * 100) : 0;
                                            const label = data.labels[index];
                                            const x     = bar.x;
                                            const bottom = chart.chartArea.bottom;

                                            ctx.save();
                                            ctx.textAlign = 'center';

                                            ctx.font = 'bold 11px sans-serif';
                                            ctx.fillStyle = '#111827';
                                            ctx.fillText(label, x, bottom + 16);

                                            ctx.font = '11px sans-serif';
                                            ctx.fillStyle = '#374151';
                                            ctx.fillText(count + ' grantees', x, bottom + 30);

                                            ctx.font = '10px sans-serif';
                                            ctx.fillStyle = '#9ca3af';
                                            ctx.fillText(pct + '%', x, bottom + 44);

                                            ctx.restore();
                                        });
                                    });
                                }
                            }]
                        });
                    };
                    if (window.Chart) {
                        draw();
                    } else {
                        const s = document.createElement('script');
                        s.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js';
                        s.onload = draw;
                        document.head.appendChild(s);
                    }
                }
            }"
        >
            <p style="font-size:12px;font-weight:500;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 12px;">Scholars by scholarship type</p>
            <div style="position:relative;height:320px;">
                <canvas x-ref="bar" style="width:100%;height:100%;"></canvas>
            </div>
        </div>

    </x-filament::card>
</x-filament-widgets::widget>s