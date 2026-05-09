@props([
    'id' => 'chart-' . uniqid(),
    'type' => 'line', // line, donut, bar, area
    'height' => 350,
    'series' => [],
    'labels' => [],
])

<div x-data="chartComponent_{{ $id }}()" x-init="initChart()" class="w-full relative">
    <div id="{{ $id }}" style="min-height: {{ $height }}px;"></div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chartComponent_{{ $id }}', () => ({
            chart: null,
            initChart() {
                const options = {
                    series: @json($series),
                    chart: {
                        type: '{{ $type }}',
                        height: {{ $height }},
                        background: 'transparent',
                        toolbar: { show: false },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800,
                            dynamicAnimation: { enabled: true, speed: 350 }
                        }
                    },
                    colors: ['#00ADB5', '#22C55E', '#F59E0B', '#EF4444'],
                    fill: {
                        type: this.getFillType(),
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.45,
                            opacityTo: 0.05,
                            stops: [0, 90, 100]
                        }
                    },
                    theme: {
                        mode: 'dark'
                    },
                    dataLabels: { enabled: false },
                    stroke: {
                        curve: 'smooth',
                        width: {{ $type === 'donut' || $type === 'bar' ? 0 : 3 }}
                    },
                    tooltip: {
                        theme: 'dark',
                        style: { fontSize: '12px' },
                        background: '#393E46',
                        cssClass: 'rounded-xl shadow-lg border border-gray-700'
                    },
                    grid: {
                        borderColor: 'rgba(238, 238, 238, 0.1)',
                        strokeDashArray: 4,
                        xaxis: { lines: { show: false } },
                        yaxis: { lines: { show: true } }
                    },
                    xaxis: {
                        categories: @json($labels),
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: { style: { colors: '#9ca3af', fontFamily: 'Inter, sans-serif' } }
                    },
                    yaxis: {
                        labels: { style: { colors: '#9ca3af', fontFamily: 'Inter, sans-serif' } }
                    }
                };

                // Remove grid/axes for Donut
                if('{{ $type }}' === 'donut') {
                    delete options.xaxis;
                    delete options.yaxis;
                    delete options.grid;
                    delete options.stroke;
                    options.labels = @json($labels);
                }

                this.chart = new window.ApexCharts(document.querySelector("#{{ $id }}"), options);
                this.chart.render();
            },
            getFillType() {
                return '{{ $type }}' === 'area' ? 'gradient' : 'solid';
            }
        }));
    });
</script>
@endpush
