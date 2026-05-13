pipeline {
    agent {
        docker {
            image 'python:3.11-slim'
            // Gunakan -u 0:0 untuk memastikan akses root agar bisa install apt-get & pakai docker.sock
            args '-u 0:0 -v /var/run/docker.sock:/var/run/docker.sock'
        }
    }

    environment {
        SONAR_TOKEN = credentials('Sonarqube')
    }

    stages {
        stage('Build Environment') {
            steps {
                sh '''
                apt-get update && apt-get install -y wget unzip docker-compose
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
                stage('Unit Tests') {
                    steps {
                        sh 'pytest --cov=. --cov-report=xml:reports/coverage.xml'
                    }
                }

                stage('Linting') {
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
                      -Dsonar.exclusions=venv/**,static/js/*.js,**/reports/** \
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
                // Langsung jalankan karena docker-compose sudah diinstall di stage Build
                sh 'docker-compose down || true'
                sh 'docker-compose up -d --build'
            }
        }
    }

    post {
        always {
            // cleanWs dipanggil di dalam agen docker yang aktif
            script {
                echo 'Cleaning workspace...'
                cleanWs()
            }
        }

        failure {
            echo 'Build failed! Please check the logs for details.'
        }

        success {
            echo 'Build succeeded! Application deployed successfully.'
        }
    }
}