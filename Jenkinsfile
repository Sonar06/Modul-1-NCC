pipeline {
    agent any

    environment {
        // Poin Plus: Menggunakan Credential Management
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

        stage('SonarQube Analysis') {
            steps {
                script {
                    // Poin Plus: Integrasi SonarQube dengan Jenkins
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
                // Hanya berjalan di branch Modul-2
                branch '*/Modul-2'
            }
            steps {
                echo 'Deploying application to Docker...'
                
                // Proses build dan run container
                sh '''
                docker build -t iniberita .
                docker stop iniberita || true
                docker rm iniberita || true
                docker run -d --name iniberita -p 80:80 iniberita
                '''
                
                echo 'Deploy berhasil! Silakan cek web kamu di IP VPS.'
            }
        }
    }

    post {
        success {
            echo 'Pipeline SUCCESS!'
        }
        failure {
            echo 'Pipeline FAILED!'
        }
        always {
            echo 'Cleaning up workspace...'
            cleanWs()
        }
    }
}