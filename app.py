from flask import Flask, render_template, request, jsonify
from datetime import datetime

app = Flask(__name__)

# --- DATA GLOBAL ---
cities = [
    'Jakarta', 'Tangerang', 'Bogor', 'Bandung', 'Cianjur', 'Serang', 'Cirebon', 
    'Purwakarta', 'Tasikmalaya', 'Purwokerto', 'Tegal', 'Pekalongan', 'Semarang',
    'Solo', 'Yogyakarta', 'Surabaya', 'Malang', 'Banyuwangi'
]

# Matriks Jarak (tetap sama dengan yang lama)
distance_matrix = {
    'Jakarta': {'Jakarta': 0, 'Tangerang': 25, 'Bogor': 60, 'Bandung': 150, 'Cianjur': 120, 'Serang': 90, 'Cirebon': 245, 'Purwakarta': 100, 'Tasikmalaya': 270, 'Purwokerto': 395, 'Tegal': 310, 'Pekalongan': 360, 'Semarang': 445, 'Solo': 550, 'Yogyakarta': 560, 'Surabaya': 785, 'Malang': 850, 'Banyuwangi': 1080},
    'Tangerang': {'Jakarta': 25, 'Tangerang': 0, 'Bogor': 70, 'Bandung': 160, 'Cianjur': 140, 'Serang': 70, 'Cirebon': 260, 'Purwakarta': 120, 'Tasikmalaya': 285, 'Purwokerto': 410, 'Tegal': 325, 'Pekalongan': 375, 'Semarang': 460, 'Solo': 565, 'Yogyakarta': 575, 'Surabaya': 800, 'Malang': 865, 'Banyuwangi': 1095},
    'Bogor': {'Jakarta': 60, 'Tangerang': 70, 'Bogor': 0, 'Bandung': 130, 'Cianjur': 80, 'Serang': 120, 'Cirebon': 270, 'Purwakarta': 110, 'Tasikmalaya': 240, 'Purwokerto': 420, 'Tegal': 340, 'Pekalongan': 390, 'Semarang': 475, 'Solo': 580, 'Yogyakarta': 590, 'Surabaya': 815, 'Malang': 880, 'Banyuwangi': 1110},
    'Bandung': {'Jakarta': 150, 'Tangerang': 160, 'Bogor': 130, 'Bandung': 0, 'Cianjur': 60, 'Serang': 200, 'Cirebon': 150, 'Purwakarta': 65, 'Tasikmalaya': 120, 'Purwokerto': 340, 'Tegal': 260, 'Pekalongan': 310, 'Semarang': 375, 'Solo': 520, 'Yogyakarta': 470, 'Surabaya': 700, 'Malang': 800, 'Banyuwangi': 1030},
    'Cianjur': {'Jakarta': 120, 'Tangerang': 140, 'Bogor': 80, 'Bandung': 60, 'Cianjur': 0, 'Serang': 180, 'Cirebon': 200, 'Purwakarta': 100, 'Tasikmalaya': 150, 'Purwokerto': 380, 'Tegal': 300, 'Pekalongan': 350, 'Semarang': 460, 'Solo': 560, 'Yogyakarta': 510, 'Surabaya': 780, 'Malang': 840, 'Banyuwangi': 1070},
    'Serang': {'Jakarta': 90, 'Tangerang': 70, 'Bogor': 120, 'Bandung': 200, 'Cianjur': 180, 'Serang': 0, 'Cirebon': 310, 'Purwakarta': 170, 'Tasikmalaya': 330, 'Purwokerto': 460, 'Tegal': 375, 'Pekalongan': 425, 'Semarang': 510, 'Solo': 615, 'Yogyakarta': 625, 'Surabaya': 850, 'Malang': 915, 'Banyuwangi': 1145},
    'Cirebon': {'Jakarta': 245, 'Tangerang': 260, 'Bogor': 270, 'Bandung': 150, 'Cianjur': 200, 'Serang': 310, 'Cirebon': 0, 'Purwakarta': 100, 'Tasikmalaya': 190, 'Purwokerto': 180, 'Tegal': 100, 'Pekalongan': 150, 'Semarang': 235, 'Solo': 335, 'Yogyakarta': 285, 'Surabaya': 555, 'Malang': 615, 'Banyuwangi': 845},
    'Purwakarta': {'Jakarta': 100, 'Tangerang': 120, 'Bogor': 110, 'Bandung': 65, 'Cianjur': 100, 'Serang': 170, 'Cirebon': 100, 'Purwakarta': 0, 'Tasikmalaya': 170, 'Purwokerto': 310, 'Tegal': 230, 'Pekalongan': 280, 'Semarang': 390, 'Solo': 490, 'Yogyakarta': 440, 'Surabaya': 710, 'Malang': 770, 'Banyuwangi': 1000},
    'Tasikmalaya': {'Jakarta': 270, 'Tangerang': 285, 'Bogor': 240, 'Bandung': 120, 'Cianjur': 150, 'Serang': 330, 'Cirebon': 190, 'Purwakarta': 170, 'Tasikmalaya': 0, 'Purwokerto': 220, 'Tegal': 250, 'Pekalongan': 300, 'Semarang': 385, 'Solo': 485, 'Yogyakarta': 350, 'Surabaya': 705, 'Malang': 765, 'Banyuwangi': 995},
    'Purwokerto': {'Jakarta': 395, 'Tangerang': 410, 'Bogor': 420, 'Bandung': 340, 'Cianjur': 380, 'Serang': 460, 'Cirebon': 180, 'Purwakarta': 310, 'Tasikmalaya': 220, 'Purwokerto': 0, 'Tegal': 95, 'Pekalongan': 145, 'Semarang': 230, 'Solo': 265, 'Yogyakarta': 215, 'Surabaya': 485, 'Malang': 545, 'Banyuwangi': 775},
    'Tegal': {'Jakarta': 310, 'Tangerang': 325, 'Bogor': 340, 'Bandung': 260, 'Cianjur': 300, 'Serang': 375, 'Cirebon': 100, 'Purwakarta': 230, 'Tasikmalaya': 250, 'Purwokerto': 95, 'Tegal': 0, 'Pekalongan': 50, 'Semarang': 135, 'Solo': 240, 'Yogyakarta': 190, 'Surabaya': 460, 'Malang': 520, 'Banyuwangi': 750},
    'Pekalongan': {'Jakarta': 360, 'Tangerang': 375, 'Bogor': 390, 'Bandung': 310, 'Cianjur': 350, 'Serang': 425, 'Cirebon': 150, 'Purwakarta': 280, 'Tasikmalaya': 300, 'Purwokerto': 145, 'Tegal': 50, 'Pekalongan': 0, 'Semarang': 85, 'Solo': 190, 'Yogyakarta': 140, 'Surabaya': 410, 'Malang': 470, 'Banyuwangi': 700},
    'Semarang': {'Jakarta': 445, 'Tangerang': 460, 'Bogor': 475, 'Bandung': 375, 'Cianjur': 460, 'Serang': 510, 'Cirebon': 235, 'Purwakarta': 390, 'Tasikmalaya': 385, 'Purwokerto': 230, 'Tegal': 135, 'Pekalongan': 85, 'Semarang': 0, 'Solo': 100, 'Yogyakarta': 120, 'Surabaya': 335, 'Malang': 390, 'Banyuwangi': 620},
    'Solo': {'Jakarta': 550, 'Tangerang': 565, 'Bogor': 580, 'Bandung': 520, 'Cianjur': 560, 'Serang': 615, 'Cirebon': 335, 'Purwakarta': 490, 'Tasikmalaya': 485, 'Purwokerto': 265, 'Tegal': 240, 'Pekalongan': 190, 'Semarang': 100, 'Solo': 0, 'Yogyakarta': 65, 'Surabaya': 220, 'Malang': 290, 'Banyuwangi': 520},
    'Yogyakarta': {'Jakarta': 560, 'Tangerang': 575, 'Bogor': 590, 'Bandung': 470, 'Cianjur': 510, 'Serang': 625, 'Cirebon': 285, 'Purwakarta': 440, 'Tasikmalaya': 350, 'Purwokerto': 215, 'Tegal': 190, 'Pekalongan': 140, 'Semarang': 120, 'Solo': 65, 'Yogyakarta': 0, 'Surabaya': 325, 'Malang': 330, 'Banyuwangi': 560},
    'Surabaya': {'Jakarta': 785, 'Tangerang': 800, 'Bogor': 815, 'Bandung': 700, 'Cianjur': 780, 'Serang': 850, 'Cirebon': 555, 'Purwakarta': 710, 'Tasikmalaya': 705, 'Purwokerto': 485, 'Tegal': 460, 'Pekalongan': 410, 'Semarang': 335, 'Solo': 220, 'Yogyakarta': 325, 'Surabaya': 0, 'Malang': 90, 'Banyuwangi': 295},
    'Malang': {'Jakarta': 850, 'Tangerang': 865, 'Bogor': 880, 'Bandung': 800, 'Cianjur': 840, 'Serang': 915, 'Cirebon': 615, 'Purwakarta': 770, 'Tasikmalaya': 765, 'Purwokerto': 545, 'Tegal': 520, 'Pekalongan': 470, 'Semarang': 390, 'Solo': 290, 'Yogyakarta': 330, 'Surabaya': 90, 'Malang': 0, 'Banyuwangi': 235},
    'Banyuwangi': {'Jakarta': 1080, 'Tangerang': 1095, 'Bogor': 1110, 'Bandung': 1030, 'Cianjur': 1070, 'Serang': 1145, 'Cirebon': 845, 'Purwakarta': 1000, 'Tasikmalaya': 995, 'Purwokerto': 775, 'Tegal': 750, 'Pekalongan': 700, 'Semarang': 620, 'Solo': 520, 'Yogyakarta': 560, 'Surabaya': 295, 'Malang': 235, 'Banyuwangi': 0}
}

# --- ALGORITMA NEAREST NEIGHBOR ---
def nearest_neighbor(start, destinations):
    if start not in cities:
        raise ValueError(f"Start city '{start}' tidak valid")
    for city in destinations:
        if city not in cities:
            raise ValueError(f"Selected city '{city}' tidak valid")
    
    route = [start]
    current = start
    remaining = list(destinations)
    total_dist = 0
    while remaining:
        next_city = min(remaining, key=lambda x: distance_matrix[current].get(x, float('inf')))
        total_dist += distance_matrix[current].get(next_city, float('inf'))
        route.append(next_city)
        remaining.remove(next_city)
        current = next_city
    route.append(start)  # kembali ke awal
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
    return render_template('index.html', cities=cities)

@app.route('/calculate', methods=['POST'])
def calculate():
    try:
        data = request.get_json()
        start = data.get('start_city')
        end = data.get('end_city')
        
        if start not in distance_matrix or end not in distance_matrix:
            return jsonify({'error': 'Kota tidak valid'}), 400

        # Hitung jarak langsung dari start ke end
        dist = distance_matrix[start].get(end, None)
        if dist is None:
            return jsonify({'error': f'Jarak dari {start} ke {end} tidak ditemukan'}), 400
        
        route = [start, end]

        return jsonify({'route': route, 'distance': dist})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)