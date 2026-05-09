import pytest
from app import nearest_neighbor, app
import json

with open('distance_matrix.json') as f:
    distance_matrix = json.load(f)

def test_nearest_neighbor_basic():
    start = 'Jakarta'
    destinations = ['Bandung', 'Bogor']
    route, dist = nearest_neighbor(start, destinations)
    assert route[0] == start
    assert route[-1] == start
    assert set(route[1:-1]) == set(destinations)
    assert dist > 0

def test_health_check():
    client = app.test_client()
    resp = client.get('/health')
    data = resp.get_json()
    assert resp.status_code == 200
    assert data['status'] == 'UP'

def test_calculate_endpoint():
    client = app.test_client()
    resp = client.post('/calculate', json={'start_city': 'Jakarta', 'end_city': 'Bandung'})
    data = resp.get_json()
    assert 'route' in data
    assert 'distance' in data

def test_calculate_missing_city():
    client = app.test_client()
    resp = client.post('/calculate', json={'start_city': '', 'end_city': ''})
    data = resp.get_json()
    assert resp.status_code == 400
    assert 'error' in data