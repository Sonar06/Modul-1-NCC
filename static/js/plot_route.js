const graph = document.getElementById('routeGraph');

document.getElementById('calculateBtn').addEventListener('click', async () => {
    const start = document.getElementById('startCity').value;
    const end = document.getElementById('endCity').value;

    const response = await fetch('/calculate', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ start_city: start, end_city: end })
    });

    const result = await response.json();

    if (result.error) {
        alert(result.error);
    } else {
        document.getElementById('distDisplay').innerText = result.distance;
        document.getElementById('routeDisplay').innerHTML = 
            result.route.join(' ➡️ ');
        drawGraph(result.route);
    }
});

function drawGraph(route) {
    graph.innerHTML = '';
    const scale = 0.5; // Sesuaikan skala jika koordinat terlalu besar
    const offset = 100;

    for (let i = 0; i < route.length - 1; i++) {
        const c1 = route[i];
        const c2 = route[i+1];
        
        if (!cityCoords[c1] || !cityCoords[c2]) continue;

        const x1 = cityCoords[c1][0] + offset;
        const y1 = Math.abs(cityCoords[c1][1]) + offset;
        const x2 = cityCoords[c2][0] + offset;
        const y2 = Math.abs(cityCoords[c2][1]) + offset;

        // Gambar Garis
        const dist = Math.hypot(x2 - x1, y2 - y1);
        const ang = Math.atan2(y2 - y1, x2 - x1) * 180 / Math.PI;
        const line = document.createElement('div');
        line.className = 'line';
        line.style.width = dist + 'px';
        line.style.left = x1 + 'px';
        line.style.top = y1 + 'px';
        line.style.transform = `rotate(${ang}deg)`;
        graph.appendChild(line);
    }

    route.forEach(city => {
        if (!cityCoords[city]) return;
        const node = document.createElement('div');
        node.className = 'node';
        node.style.left = (cityCoords[city][0] + offset) + 'px';
        node.style.top = (Math.abs(cityCoords[city][1]) + offset) + 'px';
        node.title = city;
        graph.appendChild(node);
    });
}