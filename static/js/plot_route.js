async function calculate() {
    const start = document.getElementById('startCity').value;
    const end = document.getElementById('endCity').value;

    if (!start || !end) {
        alert("Pilih titik awal dan akhir!");
        return;
    }

    const response = await fetch('/calculate', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({start_city: start, end_city: end})
    });

    const result = await response.json();

    if (result.error) {
        alert("Error: " + result.error);
        return;
    }

    document.getElementById('distDisplay').innerText = result.distance;

    // Plot route pada canvas
    const canvas = document.getElementById('routeCanvas');
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Koordinat kota diambil dari data server
    const coords = {{ coords | tojson }};

    // Gambarkan node
    for (const city in coords) {
        const [x, y] = coords[city];
        ctx.beginPath();
        ctx.arc(x + 50, 500 - (y + 50), 5, 0, 2 * Math.PI);
        ctx.fillStyle = "blue";
        ctx.fill();
        ctx.fillText(city, x + 55, 500 - (y + 55));
    }

    // Gambarkan route
    ctx.strokeStyle = "red";
    ctx.lineWidth = 2;
    ctx.beginPath();
    const route = result.route;
    for (let i = 0; i < route.length; i++) {
        const [x, y] = coords[route[i]];
        if (i === 0) ctx.moveTo(x + 50, 500 - (y + 50));
        else ctx.lineTo(x + 50, 500 - (y + 50));
    }
    ctx.stroke();
}