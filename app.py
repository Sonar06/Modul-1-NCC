import os
from flask import Flask, jsonify
from datetime import datetime

app = Flask(__name__)

@app.route('/health', methods=['GET'])
def health_check():
    return jsonify({
	"nama": "Khairan Cherokee Musthfoa",
        "nrp": "5025241215",
        "status": "UP",
        "timestamp": datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    }), 200

if __name__ == '__main__':
    port_env = int(os.environ.get("PORT", 5000))
    app.run(host='0.0.0.0', port=port_env)
