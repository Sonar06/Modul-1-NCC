# Modul 1 NCC
- nama: `Khairan Cherokee Musthofa`
- NRP: `5025241215`
  
---

Tugas Modul 1 disini diminta untuk membuat sebuah endpoint health menggunakan docker yang dimana nantinya akan di deploy ke VPS.

1. membuat endpoint health

   disini saya menggunakan bahasa pemrograman `python`

   ```python
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
        app.run(host='0.0.0.0', port=5000)

   ```

   dan juga buat `requirements.txt` untuk requirements yang diperlukan yang isinya `flask`

   2. buat docker file
      isi dari dockerfile 

   
