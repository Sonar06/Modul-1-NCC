pipeline {
    agent any

    environment {
        // Poin Plus: Credential & Environment Management
        SONAR_TOKEN        = credentials('Sonarqube')
        SONAR_PROJECT_KEY  = 'iniberita'
        SONAR_PROJECT_NAME = 'iniberita'
        SCANNER_HOME       = tool 'Sonarqube' 
        // Mengarahkan docker ke socket lokal secara global
        DOCKER_OPTS        = '-H unix:///var/run/docker.sock'
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
                // Menggunakan socket host untuk build image
                sh "docker ${DOCKER_OPTS} build -t iniberita ."
            }
        }

        stage('Test') {
            steps {
                echo '=== Stage 3: Jalankan pengujian (Syntax Check) ==='
                // Menjalankan testing di dalam container PHP
                sh "docker ${DOCKER_OPTS} run --rm -v \$(pwd):/app -w /app php:8.2-cli php -l index.php"
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
                timeout(time: 5, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }

        stage('Deploy') {
            steps {
                echo '=== Stage 5: Deployment ke Container Running ==='
                sh """
                # Hentikan container lama jika ada
                docker ${DOCKER_OPTS} stop iniberita || true
                docker ${DOCKER_OPTS} rm iniberita || true
                
                # Jalankan container baru di port 80
                docker ${DOCKER_OPTS} run -d --name iniberita -p 80:80 iniberita
                """
                echo 'Deploy Berhasil! Silakan akses IP VPS kamu.'
            }
        }
    }

    post {
        success {
            echo 'Pipeline SUCCESS: Semua tahap dari Checkout hingga Deploy berhasil!'
        }
        failure {
            echo 'Pipeline FAILED: Terjadi kesalahan. Cek log pada stage yang merah.'
        }
        always {
            echo 'Pembersihan Workspace...'
            cleanWs()
        }
    }
}