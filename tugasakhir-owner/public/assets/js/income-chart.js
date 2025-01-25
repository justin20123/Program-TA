function generateIncomeChart(data) {
    // Extract the month names and total income values from the data
    var labels = data.map(item => item.month);
    var values = data.map(item => item.total_income);

    // Create the chart
    var ctx = document.getElementById('incomeChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Income',
                data: values,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
}
