pipeline {
    agent {
        docker {
            image 'python:3.11-slim'
            args '-u root:root'   // Optional: supaya bisa install package
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

        stage('Setup Environment') {
            steps {
                sh '''
                python -m venv venv
                . venv/bin/activate
                pip install --upgrade pip
                pip install -r requirements.txt pytest pytest-cov flake8
                '''
            }
        }

        stage('Verification') {
            parallel {
                stage('Unit Tests & Coverage') {
                    steps {
                        sh '''
                        . venv/bin/activate
                        pytest --cov=. --cov-report=xml:reports/coverage.xml
                        '''
                    }
                }
                stage('Code Linting') {
                    steps {
                        sh '''
                        . venv/bin/activate
                        flake8 . --exit-zero
                        '''
                    }
                }
            }
        }

        stage('SonarQube Analysis') {
            steps {
                withSonarQubeEnv('Sonarqube_server') {
                    sh '''
                    . venv/bin/activate
                    sonar-scanner \
                      -Dsonar.projectKey=route-optimizer \
                      -Dsonar.sources=. \
                      -Dsonar.python.coverage.reportPaths=reports/coverage.xml
                    '''
                }
            }
        }

        stage('Quality Gate') {
            steps {
                timeout(time: 10, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }
    }

    post {
        always   { cleanWs() }
        success  { echo 'Pipeline sukses! Periksa SonarQube.' }
        failure  { echo 'Pipeline gagal!' }
    }
}