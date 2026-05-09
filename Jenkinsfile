pipeline {
    agent any

    environment {
        // Poin Plus: Credential & Environment Management
        SONAR_TOKEN        = credentials('Sonarqube')
        SONAR_PROJECT_KEY  = 'iniberita'
        SONAR_PROJECT_NAME = 'iniberita'
        SCANNER_HOME       = tool 'Sonarqube' 
    }

    stages {
        stage('Checkout') {
            steps {
                echo '=== Stage 1: Ambil kode dari repository ==='
                checkout scm
            }
        }

        stage('Build') {
            steps {
                echo '=== Stage 2: Kompilasi / build Docker Image ==='
                // Menggunakan Dockerfile multi-stage yang kamu buat sebelumnya
                sh 'docker build -t iniberita .'
            }
        }

        stage('Test') {
            steps {
                echo '=== Stage 3: Jalankan pengujian (Syntax Check) ==='
                // Menjalankan test menggunakan container agar tidak butuh install PHP di Jenkins
                sh 'docker run --rm -v $(pwd):/app -w /app php:8.2-cli php -l index.php'
            }
        }

        stage('Analyze') {
            steps {
                echo '=== Stage 4: Analisis SonarQube ==='
                script {
                    withSonarQubeEnv('Sonarqube_server') {
                        sh """
                        ${SCANNER_HOME}/bin/sonar-scanner \
                          -Dsonar.projectKey=${SONAR_PROJECT_KEY} \
                          -Dsonar.projectName=${SONAR_PROJECT_NAME} \
                          -Dsonar.sources=. \
                          -Dsonar.host.url=http://70.153.136.203:9000 \
                          -Dsonar.token=${SONAR_TOKEN}
                        """
                    }
                }
            }
        }

        stage('Quality Gate') {
            steps {
                echo '=== Menunggu Standar Kualitas SonarQube ==='
                // Poin Plus: Menggagalkan pipeline jika standar tidak terpenuhi
                timeout(time: 5, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }

        stage('Deploy') {
            steps {
                echo '=== Stage 5: Deployment ke Container Running ==='
                sh '''
                # Hentikan container lama jika ada
                docker stop iniberita || true
                docker rm iniberita || true
                
                # Jalankan container baru di port 80
                docker run -d --name iniberita -p 80:80 iniberita
                '''
                echo 'Deploy Berhasil! Silakan akses IP VPS kamu.'
            }
        }
    }

    post {
        success {
            echo 'Pipeline SUCCESS: Semua tahap dari Checkout hingga Deploy berhasil!'
        }
        failure {
            echo 'Pipeline FAILED: Terjadi kesalahan pada salah satu stage.'
        }
        always {
            echo 'Pembersihan Workspace...'
            cleanWs()
        }
    }
}