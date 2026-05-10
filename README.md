# Modul 1 NCC

- nama: `Khairan Cherokee Musthofa`
- NRP: `5025241215`
  
---


## Deskripsi Pipeline

Pipeline ini adalah sistem otomatisasi CI/CD yang dirancang untuk mengelola siklus hidup aplikasi atau website. dengan tahapan stage yang biasa ada dan yang saya pakai yaitu:

- Checkout: Mengambil kode sumber terbaru dari repositori GitHub untuk memastikan Jenkins memproses revisi kode terakhir.
- Build: Tahap persiapan di mana sistem mengunduh dependencies Python, menginstal tools pengujian, dan menyiapkan aplikasi Sonar Scanner CLI
- Test: Menjalankan pengujian logika kode dan menghitung berapa persen baris kode yang sudah teruji.
- Analyze (SonarQube Analysis): Mengirimkan seluruh kode dan hasil laporan pengujian ke server SonarQube untuk dianalisis secara mendalam (mencari celah keamanan, bug, atau kode yang tidak efisien).
- Quality Gate: Tahap kontrol kualitas di mana pipeline akan berhenti otomatis atau bisa disebut gagal jika hasil analisis SonarQube tidak memenuhi standar minimal yang ditentukan.

## integrasi Jenkins dengan SonarQube
jika sudah berhasil menginstall sonarqube dan juga jenkins di server. maka untuk konfigurasi untuk mengintegrasi yang saya lakukan adalah sebagai berikut:

1. Install Plugin SonarQube Scanner di Jenkins
jenkins manage -> plugins -> available plugins, setelah itu cari SonarQube Scanner kemudian install di jenkins

![alt text](<media/image.png>)

karena saya sudah menginstall maka akan terlihat di installed plugins.

2. Menyiapkan Kredensial (Token)
agar jenkins punya izin akses ke server SonarQube. pertama buka sonarqube yang jalan di port 9000, setelah itu My Account -> Security -> Generate

jika sudah akan terlihat seperti ini:

![alt text](<media/Screenshot 2026-05-10 094531.png>)

3. konfigurasi SonarQube Scanner installations
buka jenkins, lalu ke manage -> tools. lu cari SonarQube Scanner installations, jika belum ada maka install terlebih dahulu.

![alt text](<media/Screenshot 2026-05-10 095229.png>)

4. Mengonfigurasi koneksi SonarQube di Jenkins
buka jenkins, lalu ke manage -> system. Cari tools yang namanya SonarQube server 

![alt text](<media/Screenshot 2026-05-10 095444.png>)
![alt text](<media/Screenshot 2026-05-10 095458.png>)

untuk Server authentication token itu isi dengan token yang sudah di dapat dari generate toke sonarqube. dengan cara add -> secret file, lalu untuk kolon secret isi dengan token sonarqube yang sudah di dapatkan. juga untuk Server URL itu isi dengan http://IP-VPS:9000.

5. Konfigurasi Webhook di SonarQube
buka sonarqube, Administration -> Configuration -> Webhooks lalu pilih create dan isi kolom yang ada. Untuk URL isi dengan http://IP-VPS:8080/sonarqube-webhook/

![alt text](<media/Screenshot 2026-05-10 100028.png>)

6. konfigurasi webhook di Github
pertama generate token github bisa diakses melalui <a href="https://github.com/settings/tokens" target="_blank">Link ini</a>. setelah itu konfigurasi GitHub di Jenkins 

![alt text](<media/Screenshot 2026-05-10 103750.png>)

untuk URL isi dengan link repo yang ingin dijalankan. dan untuk credential sama seperti konfigurasi sonarqube di jenkins namun secret diambil dari token github.

lalu buka settings repo -> webhook  -> add webhook

![alt text](<media/Screenshot 2026-05-10 104052.png>)

untuk payload URL menggunakan URL dari jenkins di port 8080 `IP-VPS:8080`





## membuat job di jenkins dengan pipeline
untuk ini isi sesuai kebutuhan seperti general ada github project isi denga repo yang ingin di lakukan webhook dengan pipeline. lalu trigers centang bagian github hook triger for GITScm polling. jika merasa sudha maka lanjut ke pipeline nya, untuk definisi gunakan Pipeline script from SCM. lalu SCM melalui git dan masukan semua kebutuhan yang diperlukan seperti URL repo juga credential.

![alt text](<media/Screenshot 2026-05-10 104611.png>)

karena di repo ini saya jalankan di branc Modul-2 maka saya isi Branches to build dengan `*/Modul-2`

jika sudah buat jenkinsfile dan saya isi dengan:

```Jenkinsfile
pipeline {
    agent {
        docker {
            image 'python:3.11-slim'
            // KUNCI UTAMA: Mount docker.sock agar kontainer Python bisa perintahin Docker Host buat Deploy
            args '-u root:root -v /var/run/docker.sock:/var/run/docker.sock'
        }
    }

    environment {
        SONAR_TOKEN = credentials('Sonarqube')
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Build Environment') {
            steps {
                sh '''
                apt-get update && apt-get install -y wget unzip
                pip install --upgrade pip
                pip install -r requirements.txt pytest pytest-cov flake8

                if [ ! -d "/opt/sonar-scanner" ]; then
                    wget https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-5.0.1.3006-linux.zip
                    unzip -o sonar-scanner-cli-5.0.1.3006-linux.zip
                    mv sonar-scanner-5.0.1.3006-linux /opt/sonar-scanner
                fi
                '''
            }
        }

        stage('Test') {
            parallel {
                stage('Unit Tests & Coverage') {
                    steps {
                        sh 'pytest --cov=. --cov-report=xml:reports/coverage.xml'
                    }
                }
                stage('Code Linting') {
                    steps {
                        sh 'flake8 . --exclude=venv --exit-zero'
                    }
                }
            }
        }

        stage('SonarQube Analysis') {
            steps {
                withSonarQubeEnv('Sonarqube_server') {
                    sh '''
                    /opt/sonar-scanner/bin/sonar-scanner \
                      -Dsonar.projectKey=route-optimizer \
                      -Dsonar.sources=. \
                      -Dsonar.exclusions=venv/** \
                      -Dsonar.python.coverage.reportPaths=reports/coverage.xml
                    '''
                }
            }
        }

        stage('Quality Gate') {
            steps {
                timeout(time: 5, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }

        stage('Deploy') {
            steps {
                echo 'Mendeploy aplikasi menggunakan Docker Compose...'
                sh '''
                # Kita install docker-compose (versi standalone) yang lebih stabil di kontainer
                if ! command -v docker-compose &> /dev/null; then
                    apt-get update && apt-get install -y docker-compose
                fi
                
                # Gunakan docker-compose (dengan tanda strip)
                docker-compose down || true
                docker-compose up -d --build
                '''
            }
        }
    }

    post {
        always { 
            echo 'Membersihkan workspace...'
            cleanWs() 
        }
        success {
            echo 'Pipeline sukses! Aplikasi sudah terdeploy di Port 80/3000.' 
        }
        failure { 
            echo 'Pipeline gagal! Cek stage yang merah.' 
        }
    }
}
```

keterangan:
1. Konfigurasi Environment
- Agent Docker: Seluruh instruksi dijalankan di dalam kontainer python:3.11-slim. Ini memastikan lingkungan yang bersih dan konsisten tanpa perlu menginstal Python di OS VPS.
- Environment: Mengambil token keamanan SonarQube dari kredensial Jenkins agar proses login ke server analisis aman (tidak tertulis langsung).

2. Alur Kerja (Stages)
-Stage Checkout: Mengambil kode sumber terbaru dari repositori Git.
- Stage Build: Melakukan instalasi pustaka Python (pip install) dan perangkat lunak pendukung (wget, unzip), serta menyiapkan aplikasi Sonar Scanner CLI di dalam kontainer.
- Stage Test (Parallel): Menjalankan dua pengujian sekaligus untuk efisiensi:
    - Unit Tests: Menguji fungsi logika dan membuat laporan cakupan kode (coverage).
    - Code Linting: Memeriksa kerapian dan standar penulisan kode menggunakan flake8.
- Stage SonarQube Analysis: Melakukan pemindaian statis terhadap kode untuk mendeteksi celah keamanan, bug, dan kerentanan.
- Stage Quality Gate: Menunggu hasil penilaian dari server SonarQube; jika kualitas kode di bawah standar, pipeline otomatis dihentikan (abort).

3. Post-Actions
Always: Membersihkan folder kerja (workspace) setelah selesai agar tidak memenuhi penyimpanan.

- Success/Failure: Memberikan notifikasi status akhir pipeline melalui log Jenkins.


## Alur Pipeline (Flow)

Checkout -> Setup -> Backend Build & Test (Parallel) -> SonarQube Analysis -> Quality Gate -> Post Actions

