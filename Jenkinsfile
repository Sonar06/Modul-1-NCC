pipeline {
    agent any
    
    // Panggil instalasi yang sudah kamu buat di Global Tool Configuration
    tools {
        'org.sonarsource.scanner.jenkins.SonarQubeScannerInstaller' 'Sonarqube' 
    }

    environment {
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
                // Menjalankan pengecekan syntax python di dalam container
                sh 'docker run --rm route-app-image python -m py_compile app.py'
            }
        }

        stage('SonarQube Analysis') {
            steps {
                echo '=== Stage 4: SonarQube Analysis ==='
                script {
                    // Jenkins akan mencari folder instalasi scannerHome secara otomatis
                    def scannerHome = tool 'sonar-scanner'
                    withSonarQubeEnv('Sonarqube_server') {
                        sh "${scannerHome}/bin/sonar-scanner \
                        -Dsonar.projectKey=route-optimizer \
                        -Dsonar.sources=. \
                        -Dsonar.language=py \
                        -Dsonar.python.version=3"
                    }
                }
            }
        }

        stage('Quality Gate') {
            steps {
                echo '=== Stage 5: Quality Gate ==='
                timeout(time: 3, unit: 'MINUTES') {
                    // Pastikan webhook di SonarQube sudah mengarah ke IP-Jenkins/sonarqube-webhook/
                    waitForQualityGate abortPipeline: true
                }
            }
        }

        stage('Deploy') {
            steps {
                echo '=== Stage 6: Deploy with Docker Compose ==='
                sh '''
                docker rm -f route-app || true
                docker compose up -d --build
                ''' 
            }
        }
    }

      post {

        success {
            echo 'Pipeline BERHASIL!'
        }

        failure {
            echo 'Pipeline GAGAL!'
        }

        always {
            echo 'Pipeline selesai'
            cleanWs()
        }
    }
}