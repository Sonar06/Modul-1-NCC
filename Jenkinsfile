pipeline {
    agent {
        docker {
            image 'python:3.11-slim'
            // Menggunakan user root agar bisa install package & akses socket host
            // Menonaktifkan TLS verify untuk menghindari error x509 certificate
            args '-u 0:0 -v /var/run/docker.sock:/var/run/docker.sock -e DOCKER_TLS_VERIFY=0'
        }
    }

    environment {
        // Mengambil token dari Jenkins Credentials dengan ID 'Sonarqube'
        SONAR_TOKEN = credentials('Sonarqube')
        // Memaksa Docker menggunakan socket unix daripada network TCP
        DOCKER_HOST = 'unix:///var/run/docker.sock'
    }

    stages {
        stage('Checkout') {
            steps {
                // Mengambil kode dari GitHub
                checkout scm
            }
        }

        stage('Build Environment') {
            steps {
                sh '''
                apt-get update && apt-get install -y wget unzip docker-compose
                pip install --upgrade pip
                pip install -r requirements.txt pytest pytest-cov flake8
                
                # Install Sonar Scanner jika belum ada
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
                        // Pastikan folder reports ada untuk menyimpan hasil coverage
                        sh 'mkdir -p reports'
                        sh 'pytest --cov=. --cov-report=xml:reports/coverage.xml'
                    }
                }
                stage('Code Linting') {
                    steps {
                        // Flake8 untuk kerapian kode (exit-zero agar build tidak stop hanya karena spasi)
                        sh 'flake8 . --exclude=venv --exit-zero'
                    }
                }
            }
        }

        stage('SonarQube Analysis') {
            steps {
                // Menggunakan konfigurasi server Sonarqube yang sudah didaftarkan di Jenkins
                withSonarQubeEnv('Sonarqube_server') {
                    sh '''
                    /opt/sonar-scanner/bin/sonar-scanner \
                      -Dsonar.projectKey=route-optimizer \
                      -Dsonar.sources=. \
                      -Dsonar.exclusions=venv/**,static/js/*.js,**/reports/**,**/test_app.py \
                      -Dsonar.python.coverage.reportPaths=reports/coverage.xml
                    '''
                }
            }
        }

        stage('Quality Gate') {
            steps {
                // Menunggu feedback dari server SonarQube
                timeout(time: 5, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }

        stage('Deploy') {
            steps {
                echo 'Mendeploy aplikasi menggunakan Docker Compose...'
                // Perintah deploy langsung ke host via socket binding
                sh 'docker-compose down || true'
                sh 'docker-compose up -d --build'
            }
        }
    }

    post {
        always {
            // Langsung panggil tanpa bungkus node lagi
            script {
                echo 'Cleaning workspace...'
                cleanWs()
            }
        }
    }
}