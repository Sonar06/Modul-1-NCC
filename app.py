from flask import Flask, request, jsonify
import json

app = Flask(__name__)

# Load distance matrix dari file JSON
with open('distance_matrix.json') as f:
    distance_matrix = json.load(f)

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