pipeline {
    agent {
        docker {
            image 'python:3.11-slim'
            args '-u root:root' // optional untuk izin root
        }
    }

    environment {
        SONARQUBE_ENV = 'Sonarqube'
        SCANNER_HOME  = tool 'Sonarqube'
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
                source venv/bin/activate
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
                        source venv/bin/activate
                        pytest --cov=. --cov-report=xml
                        '''
                    }
                }
                stage('Code Linting') {
                    steps {
                        sh '''
                        source venv/bin/activate
                        flake8 . --exit-zero
                        '''
                    }
                }
            }
        }

        stage('SonarQube Analysis') {
            steps {
                withSonarQubeEnv("${SONARQUBE_ENV}") {
                    sh """
                    ${SCANNER_HOME}/bin/sonar-scanner \
                      -Dsonar.projectKey=ncc-health \
                      -Dsonar.projectName=ncc-health \
                      -Dsonar.sources=. \
                      -Dsonar.python.coverage.reportPaths=coverage.xml
                    """
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
        success {
            echo 'Pipeline Sukses! Check SonarQube for Code Quality.'
        }
        failure {
            echo 'Pipeline Gagal!'
        }
    }
}