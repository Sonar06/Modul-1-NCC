from flask import Flask, request, jsonify
import json

app = Flask(__name__)

# Load distance matrix dari file JSON
with open('distance_matrix.json') as f:
    distance_matrix = json.load(f)
    
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

def nearest_neighbor(start, destinations):
    route = [start]
    total_distance = 0
    remaining = set(destinations)
    current = start

    while remaining:
        next_city = min(remaining, key=lambda x: distance_matrix[current][x])
        total_distance += distance_matrix[current][next_city]
        route.append(next_city)
        current = next_city
        remaining.remove(next_city)

    # kembali ke start
    total_distance += distance_matrix[current][start]
    route.append(start)
    return route, total_distance

@app.route('/health')
def health():
    return jsonify({'status': 'UP'})

@app.route('/calculate', methods=['POST'])
def calculate():
    data = request.get_json()
    start = data.get('start_city')
    end = data.get('end_city')
    if not start or not end:
        return jsonify({'error': 'Missing city'}), 400

    route, dist = nearest_neighbor(start, [end])
    return jsonify({'route': route, 'distance': dist})

if __name__ == "__main__":
    app.run(debug=True)