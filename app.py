from flask import Flask, render_template, request, jsonify

app = Flask(__name__)

# --- DATA GLOBAL (Global Scope) ---
cities = [
    'Jakarta', 'Tangerang', 'Bogor', 'Bandung', 'Cianjur', 'Serang', 'Cirebon', 
    'Purwakarta', 'Tasikmalaya', 'Purwokerto', 'Tegal', 'Pekalongan', 'Semarang',
    'Solo', 'Yogyakarta', 'Surabaya', 'Malang', 'Banyuwangi'
]

# Matriks Jarak (Contoh)
distance_matrix = {
    'Jakarta': {'Bandung': 150, 'Semarang': 445, 'Surabaya': 785, 'Yogyakarta': 560},
    'Bandung': {'Jakarta': 150, 'Semarang': 375, 'Surabaya': 700, 'Yogyakarta': 470},
    'Semarang': {'Jakarta': 445, 'Bandung': 375, 'Surabaya': 335, 'Yogyakarta': 120},
    'Surabaya': {'Jakarta': 785, 'Bandung': 700, 'Semarang': 335, 'Yogyakarta': 325},
    'Yogyakarta': {'Jakarta': 560, 'Bandung': 470, 'Semarang': 120, 'Surabaya': 325}
}

# --- LOGIKA ALGORITMA ---
def nearest_neighbor(start, destinations):
    route = [start]
    current = start
    remaining = list(destinations)
    total_dist = 0
    while remaining:
        next_city = min(remaining, key=lambda x: distance_matrix.get(current, {}).get(x, 9999))
        total_dist += distance_matrix.get(current, {}).get(next_city, 9999)
        route.append(next_city)
        remaining.remove(next_city)
        current = next_city
    route.append(start)  # Kembali ke awal
    return route, total_dist

# --- ROUTES ---
@app.route('/health')
def health():
    """Endpoint Health Check untuk akses via port 3000"""
    return jsonify({"status": "up", "service": "route-optimizer"}), 200

@app.route('/')
def index():
    """Halaman Utama untuk akses via port 80"""
    return render_template('index.html', cities=cities)

@app.route('/calculate', methods=['POST'])
def calculate():
    try:
        data = request.get_json()
        start = data.get('start_city')
        selected = data.get('selected_cities', [])
        route, dist = nearest_neighbor(start, selected)
        return jsonify({'route': route, 'distance': dist})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)