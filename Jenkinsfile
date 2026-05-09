pipeline {
    agent {
        docker { image 'python:3.11-slim' } // pakai image Python siap pakai
    }

    environment {
        SONARQUBE_ENV = 'Sonarqube'
        SCANNER_HOME  = tool 'Sonarqube'
    }

    stages {
        stage('Checkout') {
            steps { checkout scm }
        }

        stage('Setup Environment') {
            steps {
                sh '''
                python -m venv venv
                . venv/bin/activate
                pip install --upgrade pip
                pip install -r requirements.txt pytest pytest-cov flake8
                '''
            }
        }

        stage('Test') {
            steps {
                sh '''
                . venv/bin/activate
                pytest --cov=. --cov-report=xml:coverage.xml
                flake8 . --exit-zero
                '''
            }
        }

        stage('SonarQube Analysis') {
            steps {
                withSonarQubeEnv("${SONARQUBE_server}") {
                    sh """
                    ${SCANNER_HOME}/bin/sonar-scanner \
                      -Dsonar.projectKey=route-optimizer \
                      -Dsonar.sources=. \
                      -Dsonar.python.coverage.reportPaths=coverage.xml
                    """
                }
            }
        }

        stage('Deploy') {
            steps {
                sh '''
                docker stop route-app || true
                docker rm route-app || true
                docker compose up -d --build --remove-orphans
                '''
            }
        }
    }

    post {
        always {
            echo 'Pipeline selesai'
            cleanWs()
        }
        success { echo 'Pipeline berhasil!' }
        failure { echo 'Pipeline gagal!' }
    }
}