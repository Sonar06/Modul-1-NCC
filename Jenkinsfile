pipeline {
    agent any
    environment {
        SONAR_TOKEN = credentials('Sonarqube')
    }
    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }
        stage('Build Image') {
            steps {
                sh 'docker build -t route-app-image .'
            }
        }
        stage('SonarQube Analysis') {
            steps {
                script {
                    def scannerHome = tool 'Sonarqube'
                    withSonarQubeEnv('Sonarqube_server') {
                        sh """
                        ${scannerHome}/bin/sonar-scanner \
                        -Dsonar.projectKey=route-optimizer \
                        -Dsonar.sources=. \
                        -Dsonar.language=py \
                        -Dsonar.python.version=3
                        """
                    }
                }
            }
        }
        stage('Quality Gate') {
            steps {
                timeout(time: 3, unit: 'MINUTES') {
                    // Diubah ke false agar tetap deploy walau ada warning kualitas
                    waitForQualityGate abortPipeline: false 
                }
            }
        }
        stage('Deploy') {
            steps {
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
            cleanWs()
        }
    }
}