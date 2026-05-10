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
                    // Backslash dihapus dan digabung agar tidak ada error spasi lagi
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
                sh '''
                # Ini langkah minimal install Docker CLI di Debian (slim)
                apt-get update && apt-get install -y ca-certificates curl gnupg
                install -m 0755 -d /etc/apt/keyrings
                curl -fsSL https://download.docker.com/linux/debian/gpg | gpg --dearmor -o /etc/apt/keyrings/docker.gpg
                chmod a+r /etc/apt/keyrings/docker.gpg

                echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/debian $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null
                
                apt-get update && apt-get install -y docker-ce-cli docker-compose-plugin

                # Baru jalankan deploy
                docker compose down || true
                docker compose up -d --build
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
            echo 'Pipeline sukses! Aplikasi sudah terdeploy.' 
        }
        failure { 
            echo 'Pipeline gagal! Periksa log SonarScanner atau Testing.' 
        }
    }
}