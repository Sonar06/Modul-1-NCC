const positions = {}; // Simulasi posisi node (bisa diatur manual)
const graph = document.getElementById('routeGraph');

document.getElementById('calculateBtn').addEventListener('click', async () => {
    const start = document.getElementById('startCity').value;
    const end = document.getElementById('endCity').value;

    if (!start || !end) {
        alert("Pilih titik awal dan akhir!");
        return;
    }

    const response = await fetch('/calculate', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ start_city: start, end_city: end })
    });

    const result = await response.json();

    if (result.error) {
        alert("Error: " + result.error);
    } else {
        document.getElementById('distDisplay').innerText = result.distance;
        document.getElementById('routeDisplay').innerHTML = 
            result.route.map((city, i) => `<span>${i+1}. ${city}</span>`).join('<br> ➡️ ');

        drawGraph(result.route);
    }
});

function drawGraph(route) {
    graph.innerHTML = '';

    // Draw lines
    for (let i = 0; i < route.length - 1; i++) {
        const from = {x: cityCoords[route[i]][0], y: cityCoords[route[i]][1]};
        const to = {x: cityCoords[route[i+1]][0], y: cityCoords[route[i+1]][1]};

        const dx = to.x - from.x;
        const dy = to.y - from.y;
        const length = Math.sqrt(dx*dx + dy*dy);
        const angle = Math.atan2(dy, dx) * 180 / Math.PI;

        const line = document.createElement('div');
        line.className = 'line';
        line.style.width = length + 'px';
        line.style.transform = `rotate(${angle}deg)`;
        line.style.left = from.x + 'px';
        line.style.top = from.y + 'px';
        graph.appendChild(line);
    }

    // Draw nodes
    route.forEach(city => {
        const node = document.createElement('div');
        node.className = 'node';
        node.style.left = cityCoords[city][0] + 'px';
        node.style.top = cityCoords[city][1] + 'px';
        node.innerText = city[0]; // inisial kota
        graph.appendChild(node);
    });
}