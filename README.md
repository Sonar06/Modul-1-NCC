<img width="1009" height="128" alt="image" src="https://github.com/user-attachments/assets/7cd99ad1-e6f4-4e31-99e0-4e1ae3e433ca" /># Modul 1 NCC
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

2. buat dockerfile isi dari dan juga docker compose agar bisa menjalankan kode yang dibuat
3. run docker compose
   ```bash
   docker-compose up -d -build
   ```

- bukti endpoint dapat diakses
  <img width="1009" height="128" alt="image" src="https://github.com/user-attachments/assets/b64f3ca6-0fb8-4a99-917a-9c21bbf1c962" />

  [Link](http://20.41.122.247/health)  **sudah dapat diakses publik**


  ### uploud ke VPS

  - masuk ke microsoft azure dan login
  - buat VM Ubuntu
  - Copy dari yang dilokal ke VM juga jalankan `sudo apt-get update` dan juga intall docker
  - jalankan docker-compose
  - setelah itu buka melalui `https://<publicIP>/health` jika saya http://20.41.122.247/health

    


   
