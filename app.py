from flask import Flask, render_template, request, jsonify
from datetime import datetime

app = Flask(__name__)

# --- DATA GLOBAL ---
cities = [
    'Jakarta', 'Tangerang', 'Bogor', 'Bandung', 'Cianjur', 'Serang', 'Cirebon', 
    'Purwakarta', 'Tasikmalaya', 'Purwokerto', 'Tegal', 'Pekalongan', 'Semarang', 
    'Kendal', 'Purbalingga', 'Temanggung', 'Wonosobo', 'Banjarnegara', 'Banyumas',
    'Magelang', 'Boyolali', 'Salatiga', 'Solo', 'Klaten', 'Sragen', 'Yogyakarta',
    'Demak', 'Kudus', 'Jepara', 'Pati', 'Ngawi', 'Madiun', 'Surabaya', 'Kediri',
    'Jombang', 'Mojokerto', 'Sidoarjo', 'Gresik', 'Lamongan', 'Tuban', 'Malang',
    'Tulungagung', 'Trenggalek', 'Pacitan', 'Blitar', 'Batu', 'Probolinggo',
    'Lumajang', 'Jember', 'Situbondo', 'Bondowoso', 'Banyuwangi'
]

# --- Matriks Jarak ---
city_coords = {
        # Jawa Barat
        'Jakarta': (0, 0),
        'Tangerang': (-10, 10),
        'Serang': (-80, 5),
        'Bogor': (20, -50),
        'Cianjur': (60, -50),
        'Purwakarta': (70, -20),
        'Bandung': (100, -50),
        'Cirebon': (200, 10),
        'Tasikmalaya': (150, -80),
        
        # Jawa Tengah (Barat/Tengah)
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

        # Jawa Tengah (Pantura Timur)
        'Demak': (370, 30),
        'Kudus': (390, 35),
        'Jepara': (395, 50),
        'Pati': (410, 35),
        
        # Jawa Timur (Barat/Tengah)
        'Ngawi': (510, 10),
        'Madiun': (530, -10),
        'Pacitan': (540, -90),
        'Trenggalek': (580, -80),
        'Tulungagung': (600, -70),
        'Kediri': (590, -50),
        'Blitar': (610, -75),
        
        # Jawa Timur (Area Surabaya)
        'Tuban': (580, 40),
        'Lamongan': (600, 45),
        'Gresik': (610, 55),
        'Surabaya': (600, 50),
        'Sidoarjo': (605, 40),
        'Mojokerto': (580, 30),
        'Jombang': (570, 25),
        
        # Jawa Timur (Area Malang)
        'Malang': (650, 0),
        'Batu': (645, 5),
        
        # Jawa Timur (Timur/Tapal Kuda)
        'Probolinggo': (710, 20),
        'Lumajang': (720, -10),
        'Jember': (770, -10),
        'Bondowoso': (800, 10),
        'Situbondo': (820, 25),
        'Banyuwangi': (870, 0)
    }

# --- Algoritma Nearest Neighbor ---
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
    route.append(start)
    return route, total_dist

# --- ROUTES ---
@app.route('/health', methods=['GET'])
def health_check():
    return jsonify({
        "nama": "Khairan Cherokee Musthfoa",
        "nrp": "5025241215",
        "status": "UP",
        "timestamp": datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    }), 200

@app.route('/')
def index():
    return render_template('index.html', cities=cities, city_coords=city_coords)

@app.route('/calculate', methods=['POST'])
def calculate():
    try:
        data = request.get_json()
        start = data.get('start_city')
        end = data.get('end_city')

        if not start or not end:
            return jsonify({'error': 'Titik awal atau akhir tidak boleh kosong'}), 400

        selected_cities = [end] if start != end else []
        route, dist = nearest_neighbor(start, selected_cities)
        return jsonify({'route': route, 'distance': dist})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)  