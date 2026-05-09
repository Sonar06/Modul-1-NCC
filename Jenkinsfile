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

        stage('Run Tests') {
            steps {
                sh '''
                pip install -r requirements.txt
                pytest --junitxml=reports/test-results.xml --cov=. --cov-report=xml
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
                          -Dsonar.projectKey=route-optimizer \
                          -Dsonar.sources=. \
                          -Dsonar.language=py \
                          -Dsonar.python.version=3 \
                          -Dsonar.python.coverage.reportPaths=coverage.xml
                        """
                    }
                }
            }
        }

        stage('Quality Gate') {
            steps {
                timeout(time: 3, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
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
            echo 'Cleaning workspace...'
            cleanWs()
        }

        success {
            echo 'Pipeline SUCCESS'
        }

        failure {
            echo 'Pipeline FAILED'
        }

    }
}