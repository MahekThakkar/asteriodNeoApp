<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neo Stats Results</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
    <div class="container">
        <h1 class="mb-4">Neo Stats Results</h1>
        <div class="mb-3">
            <h3>Fastest Asteroid</h3>
            <p>ID: {{ $fastestAsteroid['id'] }}</p>
            <p>Speed: {{ $fastestAsteroid['speed'] }} km/h</p>
        </div>
        <div class="mb-3">
            <h3>Closest Asteroid</h3>
            <p>ID: {{ $closestAsteroid['id'] }}</p>
            <p>Distance: {{ $closestAsteroid['distance'] }} km</p>
        </div>
        <div class="mb-3">
            <h3>Average Size of Asteroids</h3>
            <p>{{ $averageSize }} km</p>
        </div>
        <div class="mb-3">
            <h3>Asteroids Per Day</h3>
            <canvas id="asteroidsChart"></canvas>
        </div>
    </div>
    <script>
        const data = @json($totalAsteroidsPerDay);
        const labels = Object.keys(data);
        const values = Object.values(data);

        const ctx = document.getElementById('asteroidsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Number of Asteroids',
                    data: values,
                    borderColor: 'blue',
                    fill: false,
                }]
            },
        });
    </script>
</body>
</html>
