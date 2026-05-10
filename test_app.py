import pytest
from app import app

def test_index_page():
    client = app.test_client()
    resp = client.get('/')
    assert resp.status_code == 200
    assert b"Kurir Route Optimizer" in resp.data

def test_health_endpoint():
    client = app.test_client()
    resp = client.get('/health')
    assert resp.status_code == 200
    data = resp.get_json()
    assert data['status'] == "UP"

def test_calculate_endpoint():
    client = app.test_client()
    # Perbaikan: Kirim 'destinations' sebagai LIST agar sesuai app.py
    resp = client.post('/calculate', json={
        'start_city': 'Jakarta',
        'destinations': ['Bandung', 'Cirebon']
    })
    data = resp.get_json()
    assert resp.status_code == 200
    assert 'route' in data
    assert 'distance' in data
    # Pastikan rute kembali ke Jakarta (siklus)
    assert data['route'][0] == 'Jakarta'
    assert data['route'][-1] == 'Jakarta'