let selectedDestinations = [];
const graph = document.getElementById('routeGraph');
const listContainer = document.getElementById('selectedCitiesList');

// Fungsi untuk menambah kota ke daftar
document.getElementById('addCityBtn').addEventListener('click', () => {
    const city = document.getElementById('cityPicker').value;
    if (city && !selectedDestinations.includes(city)) {
        selectedDestinations.push(city);
        renderList();
    }
});

// Fungsi untuk merender tampilan daftar kota
function renderList() {
    if (selectedDestinations.length === 0) {
        listContainer.innerHTML = '<p class="text-muted small mb-0">Belum ada kota dipilih...</p>';
        return;
    }
    listContainer.innerHTML = selectedDestinations.map((city, index) => `
        <span class="badge bg-secondary m-1 p-2">
            ${city} 
            <button type="button" class="btn-close btn-close-white" style="font-size: 0.5rem" onclick="removeCity(${index})"></button>
        </span>
    `).join('');
}

// Fungsi hapus satu kota
window.removeCity = (index) => {
    selectedDestinations.splice(index, 1);
    renderList();
};

// Reset semua
document.getElementById('clearBtn').addEventListener('click', () => {
    selectedDestinations = [];
    renderList();
    graph.innerHTML = '';
});

// Kirim ke Backend
document.getElementById('calculateBtn').addEventListener('click', async () => {
    const start = document.getElementById('startCity').value;

    if (!start || selectedDestinations.length === 0) {
        alert("Pilih titik awal dan minimal satu tujuan!");
        return;
    }

    const response = await fetch('/calculate', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ start_city: start, destinations: selectedDestinations })
    });

    const result = await response.json();

    if (result.error) {
        alert(result.error);
    } else {
        document.getElementById('distDisplay').innerText = result.distance;
        document.getElementById('routeDisplay').innerHTML = result.route.join(' ➡️ ');
        drawGraph(result.route);
    }
});

// Fungsi drawGraph tetap sama menggunakan cityCoords...

// Fungsi drawGraph tetap sama seperti sebelumnya
function drawGraph(route) {
    graph.innerHTML = '';
    const offset = 50; 
    const multiplier = 0.8; // Perkecil skala jika koordinat terlalu lebar

    for (let i = 0; i < route.length - 1; i++) {
        const c1 = route[i];
        const c2 = route[i+1];
        if (!cityCoords[c1] || !cityCoords[c2]) continue;

        const x1 = (cityCoords[c1][0] * multiplier) + offset;
        const y1 = (Math.abs(cityCoords[c1][1]) * multiplier) + offset;
        const x2 = (cityCoords[c2][0] * multiplier) + offset;
        const y2 = (Math.abs(cityCoords[c2][1]) * multiplier) + offset;

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
        node.style.left = (cityCoords[city][0] * multiplier + offset) + 'px';
        node.style.top = (Math.abs(cityCoords[city][1]) * multiplier + offset) + 'px';
        node.title = city;
        graph.appendChild(node);
    });
}