pipeline {
    agent any

    environment {
        SONAR_TOKEN        = credentials('Sonarqube')
        SONAR_PROJECT_KEY  = 'iniberita'
        SONAR_PROJECT_NAME = 'iniberita'
    }

    stages {

        stage('Checkout') {
            steps {
                echo 'Checking out source code...'
                checkout scm
            }
        }

        stage('Build') {
            steps {
                echo 'Build stage...'

                // Optional build docker
                // sh 'docker build -t iniberita .'
            }
        }

        stage('Test') {
            steps {
                echo 'Running tests...'

                sh '''
                if [ -f requirements.txt ]; then
                    pip install -r requirements.txt
                fi
                '''
            }
        }

        stage('SonarQube Analysis') {
            steps {

                script {

                    def scannerHome = tool 'Sonarqube'

                    withSonarQubeEnv('Sonarqube_server') {

                        sh """
                        ${scannerHome}/bin/sonar-scanner \
                          -Dsonar.projectKey=${SONAR_PROJECT_KEY} \
                          -Dsonar.projectName=${SONAR_PROJECT_NAME} \
                          -Dsonar.sources=. \
                          -Dsonar.host.url=http://70.153.136.203:9000 \
                          -Dsonar.token=${SONAR_TOKEN} \
                          -Dsonar.python.version=3
                        """
                    }
                }
            }
        }

        stage('Quality Gate') {
            steps {

                echo 'Checking Quality Gate...'

                timeout(time: 2, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }

        stage('Deploy') {
            when {
                anyOf {
                    branch 'main'
                    branch 'Modul-2'
                }
            }
            steps {
                echo 'Deploying application...'
                // Tambahkan perintah docker compose kamu di sini agar benar-benar jalan
                sh 'docker compose down || true'
                sh 'docker compose up -d --build'
            }
        }
    }

    post {

        success {
            echo 'Pipeline berhasil!'
        }

        failure {
            echo 'Pipeline gagal!'
        }

        always {
            echo 'Pipeline selesai'
            cleanWs()
        }
    }
}
