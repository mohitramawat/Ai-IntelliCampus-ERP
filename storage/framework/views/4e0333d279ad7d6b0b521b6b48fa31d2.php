<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'id' => 'chart-' . uniqid(),
    'type' => 'line', // line, donut, bar, area
    'height' => 350,
    'series' => [],
    'labels' => [],
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'id' => 'chart-' . uniqid(),
    'type' => 'line', // line, donut, bar, area
    'height' => 350,
    'series' => [],
    'labels' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div x-data="chartComponent_<?php echo e($id); ?>()" x-init="initChart()" class="w-full relative">
    <div id="<?php echo e($id); ?>" style="min-height: <?php echo e($height); ?>px;"></div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chartComponent_<?php echo e($id); ?>', () => ({
            chart: null,
            initChart() {
                const options = {
                    series: <?php echo json_encode($series, 15, 512) ?>,
                    chart: {
                        type: '<?php echo e($type); ?>',
                        height: <?php echo e($height); ?>,
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
                        width: <?php echo e($type === 'donut' || $type === 'bar' ? 0 : 3); ?>

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
                        categories: <?php echo json_encode($labels, 15, 512) ?>,
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: { style: { colors: '#9ca3af', fontFamily: 'Inter, sans-serif' } }
                    },
                    yaxis: {
                        labels: { style: { colors: '#9ca3af', fontFamily: 'Inter, sans-serif' } }
                    }
                };

                // Remove grid/axes for Donut
                if('<?php echo e($type); ?>' === 'donut') {
                    delete options.xaxis;
                    delete options.yaxis;
                    delete options.grid;
                    delete options.stroke;
                    options.labels = <?php echo json_encode($labels, 15, 512) ?>;
                }

                this.chart = new window.ApexCharts(document.querySelector("#<?php echo e($id); ?>"), options);
                this.chart.render();
            },
            getFillType() {
                return '<?php echo e($type); ?>' === 'area' ? 'gradient' : 'solid';
            }
        }));
    });
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views\components\ui\chart.blade.php ENDPATH**/ ?>