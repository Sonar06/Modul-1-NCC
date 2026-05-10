from  flask import Flask, request, jsonify, render_template
import json, os
from datetime import datetime

app = Flask(__name__)

# Load distance matrix dari file JSON
with open('distance_matrix.json') as f:
    distance_matrix = json.load(f)

# Koordinat kota untuk node plotting
city_coords = {
    'Jakarta': (0, 0),
    'Tangerang': (-10, 10),
    'Serang': (-80, 5),
    'Bogor': (20, -50),
    'Cianjur': (60, -50),
    'Purwakarta': (70, -20),
    'Bandung': (100, -50),
    'Cirebon': (200, 10),
    'Tasikmalaya': (150, -80),
    'Tegal': (280, 20),
    'Pekalongan': (320, 20),
    'Purwokerto': (300, -20),
    'Banyumas': (305, -25),
    'Purbalingga': (310, -30),
    'Banjarnegara': (330, -35),
    'Wonosobo': (360, -30),
    'Temanggung': (370, -20),
    'Kendal': (330, 20),
    'Semarang': (350, 20),
    'Magelang': (380, -25),
    'Yogyakarta': (400, -30),
    'Salatiga': (380, 0),
    'Boyolali': (430, -10),
    'Solo': (450, 10),
    'Klaten': (420, -20),
    'Sragen': (480, 15),
    'Demak': (370, 30),
    'Kudus': (390, 35),
    'Jepara': (395, 50),
    'Pati': (410, 35),
    'Ngawi': (510, 10),
    'Madiun': (530, -10),
    'Pacitan': (540, -90),
    'Trenggalek': (580, -80),
    'Tulungagung': (600, -70),
    'Kediri': (590, -50),
    'Blitar': (610, -75),
    'Tuban': (580, 40),
    'Lamongan': (600, 45),
    'Gresik': (610, 55),
    'Surabaya': (600, 50),
    'Sidoarjo': (605, 40),
    'Mojokerto': (580, 30),
    'Jombang': (570, 25),
    'Malang': (650, 0),
    'Batu': (645, 5),
    'Probolinggo': (710, 20),
    'Lumajang': (720, -10),
    'Jember': (770, -10),
    'Bondowoso': (800, 10),
    'Situbondo': (820, 25),
    'Banyuwangi': (870, 0)
}

# Algoritma Nearest Neighbor
def nearest_neighbor(start, destinations):
    route = [start]
    total_distance = 0
    remaining = set(destinations)
    current = start

    while remaining:
        next_city = min(remaining, key=lambda x, c=current: distance_matrix[c][x])
        total_distance += distance_matrix[current][next_city]
        route.append(next_city)
        current = next_city
        remaining.remove(next_city)

    # kembali ke start
    total_distance += distance_matrix[current][start]
    route.append(start)
    return route, total_distance

# --- Routes ---
@app.route('/', methods=['GET'])
def index():
    cities = list(distance_matrix.keys())
    return render_template('index.html', cities=cities, coords=city_coords)

@app.route('/health', methods=['GET'])
def health_check():
    return jsonify({
	"nama": "Khairan Cherokee Musthfoa",
        "nrp": "5025241215",
        "status": "UP",
        "timestamp": datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    }), 200

@app.route('/calculate', methods=['POST'])
def calculate():
    data = request.get_json()
    start = data.get('start_city')
    end = data.get('end_city')
    if not start or not end:
        return jsonify({'error': 'Missing city'}), 400

    route, dist = nearest_neighbor(start, [end])
    return jsonify({'route': route, 'distance': dist})

if __name__ == '__main__':
    # Ambil host dari env, kalau tidak ada default ke 127.0.0.1 (aman menurut Sonar)
    host = os.getenv('FLASK_RUN_HOST', '127.0.0.1')
    port = int(os.getenv('FLASK_RUN_PORT', 5000))
    app.run(host=host, port=port)
