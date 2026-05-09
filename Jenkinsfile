pipeline {
    agent any

    environment {
        // Poin Plus: Credential & Environment Management
        SONAR_TOKEN        = credentials('Sonarqube')
        SONAR_PROJECT_KEY  = 'iniberita'
        SONAR_PROJECT_NAME = 'iniberita'
        SCANNER_HOME       = tool 'Sonarqube' 
    }

    stages {
        stage('Checkout') {
            steps {
                echo 'Checking out source code...'
                checkout scm
            }
        }

        stage('PHP Syntax Test') {
            steps {
                echo 'Running PHP Syntax Check...'
                // Menjalankan pengecekan sintaks PHP
                sh 'find . -name "*.php" -exec php -l {} +'
            }
        }

        stage('SonarQube Analysis') {
            steps {
                script {
                    // Poin Plus: Menghubungkan Jenkins dengan SonarQube
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
                echo 'Waiting for SonarQube Quality Gate...'
                // Poin Plus: Menggagalkan pipeline jika standar kualitas tidak terpenuhi
                timeout(time: 5, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }

        stage('Deploy') {
            when {
                // HANYA berjalan jika branch adalah Modul-2
                branch 'Modul-2'
            }
            steps {
                echo 'Deploying to Docker Container...'
                sh '''
                # Build image dari Dockerfile yang kamu buat
                docker build -t iniberita .
                
                # Hentikan container lama jika ada
                docker stop iniberita || true
                docker rm iniberita || true
                
                # Jalankan container baru
                docker run -d --name iniberita -p 80:80 iniberita
                '''
                echo 'Deploy Berhasil! Silakan akses IP VPS kamu.'
            }
        }
    }

    post {
        success {
            echo 'Pipeline Berhasil: Semua tahap terlewati!'
        }
        failure {
            echo 'Pipeline Gagal: Cek log atau standar kualitas SonarQube.'
        }
        always {
            echo 'Cleaning up workspace...'
            cleanWs()
        }
    }
}