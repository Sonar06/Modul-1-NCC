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

        stage('Test & Coverage') {
            steps {
                sh '''
                python3 -m venv venv
                source venv/bin/activate
                pip install --upgrade pip
                pip install -r requirements.txt pytest pytest-cov
                pytest --cov=./ --cov-report=xml:reports/coverage.xml
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
                          -Dsonar.python.coverage.reportPaths=reports/coverage.xml
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
            cleanWs()
        }
    }
}