pipeline {
    agent any

    environment {
        SONAR_TOKEN        = credentials('Sonarqube')
        SONAR_PROJECT_KEY  = 'iniberita'
        SONAR_PROJECT_NAME = 'iniberita'
        SCANNER_HOME       = tool 'Sonarqube'
    }

    stages {

        stage('Checkout') {
            steps {
                echo '=== Stage 1: Checkout Source Code ==='
                checkout scm
            }
        }

        stage('Build') {
            steps {
                echo '=== Stage 2: Build Docker Image ==='

                sh '''
                docker build -t iniberita .
                '''
            }
        }

        stage('Test') {
            steps {
                echo '=== Stage 3: Testing Application ==='

                sh '''
                docker run --rm iniberita php -l /var/www/html/index.php
                '''
            }
        }

        stage('SonarQube Analysis') {
            steps {
                echo '=== Stage 4: SonarQube Analysis ==='

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

        stage('Quality Gate') {
            steps {

                echo '=== Stage 5: Quality Gate ==='

                timeout(time: 5, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }

        stage('Deploy') {
            steps {

                echo '=== Stage 6: Deploy Container ==='

                sh '''
                docker stop iniberita || true
                docker rm iniberita || true

                docker run -d \
                  --name iniberita \
                  -p 80:80 \
                  iniberita
                '''

                echo 'Deployment berhasil!'
            }
        }
    }

    post {

        success {
            echo 'Pipeline BERHASIL!'
        }

        failure {
            echo 'Pipeline GAGAL!'
        }

        always {
            echo 'Pipeline selesai'
            cleanWs()
        }
    }
}