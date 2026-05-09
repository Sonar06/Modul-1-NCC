pipeline {
    agent any
    
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
                withSonarQubeEnv('Sonarqube_server') {
                    sh """
                    sonar-scanner \
                    -Dsonar.projectKey=route-optimizer \
                    -Dsonar.sources=. \
                    -Dsonar.language=py \
                    -Dsonar.python.version=3
                    """
                }
            }
        }

        stage('Deploy') {
            steps {
                echo '=== Stage 5: Deploy with Docker Compose ==='
                sh '''
                docker rm -f route-app || true
                docker compose up -d --build
                '''
            }
        }
    }
}