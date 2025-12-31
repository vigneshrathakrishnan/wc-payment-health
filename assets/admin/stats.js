(function () {
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') return;
        if (typeof WCPH_STATS === 'undefined') return;

        const canvas = document.getElementById('wcphChart');
        if (!canvas) return;

        new Chart(canvas, {
            type: 'pie',
            data: {
                labels: ['Success', 'Failure'],
                datasets: [{
                    data: [WCPH_STATS.success, WCPH_STATS.failure],
                    backgroundColor: ['#46b450', '#dc3232']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });
})();
