pipeline {
    agent any  // Menjalankan pipeline di node yang tersedia

    environment {
        APP_ENV = 'staging'
        SONARQUBE_ENV = 'Sonarqube'
        SCANNER_HOME = tool 'Sonarqube'
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Setup Environment') {
            steps {
                // Gunakan Python image via Docker jika tersedia, atau pastikan Python terinstall di node
                sh '''
                python3 -m venv venv
                . venv/bin/activate
                pip install --upgrade pip
                pip install -r requirements.txt pytest pytest-cov flake8
                '''
            }
        }

        stage('Build') {
            steps {
                echo 'Build step: untuk Python biasanya tidak ada kompilasi, bisa dilewati atau untuk pre-processing'
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

        stage('Analyze') {
            steps {
                withSonarQubeEnv("${Sonarqube_server}") {
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
                echo 'Deploy step: bisa jalankan docker compose atau script deployment lain'
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
        success {
            echo 'Pipeline berhasil!'
        }
        failure {
            echo 'Pipeline gagal!'
        }
    }
}