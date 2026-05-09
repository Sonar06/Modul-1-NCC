pipeline {
    agent any
    
    environment {
        // Mengambil kredensial 'Sonarqube' dari Jenkins Credentials
        SONAR_TOKEN = credentials('Sonarqube')
    }

    stages {
        stage('Checkout') {
            steps {
                echo '=== Stage 1: Checkout Source Code ==='
                checkout scm
            }
        }

        stage('Build') {
            steps {
                echo '=== Stage 2: Build Docker Image ==='
                sh 'docker build -t route-app-image .'
            }
        }

        stage('Python Lint/Test') {
            steps {
                echo '=== Stage 3: Syntax Check ==='
                sh 'docker run --rm route-app-image python -m py_compile app.py'
            }
        }

        stage('SonarQube Analysis') {
            steps {
                echo '=== Stage 4: SonarQube Analysis ==='
                script {
                    // Mencari path instalasi scanner secara dinamis
                    def scannerHome = tool 'Sonarqube'
                    
                    withSonarQubeEnv('Sonarqube_server') {
                        sh """
                        ${scannerHome}/bin/sonar-scanner \
                        -Dsonar.projectKey=route-optimizer \
                        -Dsonar.projectName="Optimasi Rute Kurir" \
                        -Dsonar.sources=. \
                        -Dsonar.language=py \
                        -Dsonar.python.version=3
                        """
                    }
                }
            }
        }

        stage('Quality Gate') {
            steps {
                echo '=== Stage 5: Quality Gate ==='
                timeout(time: 3, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }

        stage('Deploy') {
            steps {
                echo '=== Stage 6: Deploy with Docker Compose ==='
                sh '''
                # Hentikan container lama jika ada
                docker stop route-app || true
                docker rm route-app || true
                
                # Jalankan compose dengan flag --remove-orphans untuk membersihkan container lain
                docker compose up -d --build --remove-orphans
                ''' 
            }
        }
    }

    post {
        always {
            echo 'Pipeline selesai, membersihkan workspace...'
            cleanWs()
        }
        success {
            echo 'Pipeline BERHASIL! Aplikasi sudah running di port 80.'
        }
        failure {
            echo 'Pipeline GAGAL! Periksa log pada stage yang merah.'
        }
    }
}