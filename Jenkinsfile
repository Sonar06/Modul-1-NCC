pipeline {
    agent {
        docker {
            image 'python:3.11-slim'
            args '-u root:root'
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

        stage('Build') {
            steps {
                sh '''
                # Update dan install dependencies untuk download/unzip
                apt-get update
                apt-get install -y wget unzip

                # Upgrade pip dan install requirements proyek
                pip install --upgrade pip
                pip install -r requirements.txt pytest pytest-cov flake8

                # Download dan Setup Sonar Scanner jika belum ada
                if [ ! -d "/opt/sonar-scanner" ]; then
                    wget https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-5.0.1.3006-linux.zip
                    unzip sonar-scanner-cli-5.0.1.3006-linux.zip
                    mv sonar-scanner-5.0.1.3006-linux /opt/sonar-scanner
                fi
                '''
            }
        }

        stage('test') {
            parallel {
                stage('Unit Tests & Coverage') {
                    steps {
                        sh 'pytest --cov=. --cov-report=xml:reports/coverage.xml'
                    }
                }
                stage('Code Linting') {
                    steps {
                        // Tambahkan --exclude venv agar tidak error membaca library pihak ketiga
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
                timeout(time: 10, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }
    }

        stage('Deploy') {
            steps {
                echo 'Mendeploy aplikasi ke VPS...'
                sh '''
                docker compose down || true
                docker compose up -d --build
                '''
            }
        }

    post {
        always   { 
            echo 'Membersihkan workspace...'
            cleanWs() 
        }
        success  {
            echo 'Pipeline sukses! Periksa SonarQube.' 
        }
        failure  { 
            echo 'Pipeline gagal! Periksa log SonarScanner atau Testing.' 
        }
    }
}