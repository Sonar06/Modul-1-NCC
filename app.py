from flask import Flask, render_template, request, jsonify
import pandas as pd

app = Flask(__name__)

# --- DATA GLOBAL (Definisikan di sini agar tidak NameError) ---
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

# Contoh matriks jarak (Pastikan minimal ada data jarak untuk kota-kota di atas)
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
    total_distance = 0
    
    while remaining:
        # Cari kota terdekat dari lokasi sekarang
        nearest = min(remaining, key=lambda city: distance_matrix.get(current, {}).get(city, 9999))
        dist = distance_matrix.get(current, {}).get(nearest, 9999)
        
        total_distance += dist
        route.append(nearest)
        remaining.remove(nearest)
        current = nearest
    
    # Kembali ke depot
    total_distance += distance_matrix.get(current, {}).get(start, 9999)
    route.append(start)
    return route, total_distance

# --- ROUTES ---
@app.route('/')
def index():
    # Variabel 'cities' sekarang aman karena sudah didefinisikan di atas
    return render_template('index.html', cities=cities)

@app.route('/calculate', methods=['POST'])
def calculate():
    try:
        data = request.get_json()
        start_city = data.get('start_city')
        selected_cities = data.get('selected_cities', [])
        
        if not start_city or not selected_cities:
            return jsonify({'error': 'Data tidak lengkap'}), 400
            
        route, total_dist = nearest_neighbor(start_city, selected_cities)
        
        return jsonify({
            'route': route,
            'distance': total_dist
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    # Jalankan pada port 5000 untuk sinkronisasi dengan Docker Compose
    app.run(host='0.0.0.0', port=5000)