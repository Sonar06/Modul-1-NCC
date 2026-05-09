from flask import Flask, render_template, request, jsonify
# Impor semua fungsi algoritma (Nearest Neighbor, A*, dll) yang sudah kamu buat

app = Flask(__name__)

@app.route('/')
def index():
    return render_template('index.html', cities=cities)

@app.route('/calculate', methods=['POST'])
def calculate():
    data = request.json
    start = data['start_city']
    destinations = data['selected_cities']
    algo = data['algorithm']
    
    # Panggil fungsi algoritma berdasarkan input
    if algo == "Nearest Neighbor":
        route, distance = nearest_neighbor(start, destinations)
    # ... tambahkan logika algoritma lainnya
    
    return jsonify({
        'route': route,
        'distance': round(distance, 2)
    })

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)