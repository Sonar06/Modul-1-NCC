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
                      -Dsonar.exclusions=venv/**,static/js/*.js \
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