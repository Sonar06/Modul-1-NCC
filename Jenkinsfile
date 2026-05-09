pipeline {
    agent any

    environment {
        SONAR_TOKEN = credentials('sonar-token')
    }

    tools {
        // Sonar Scanner tool dari Jenkins
        sonarScanner 'Sonarqube'
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
                echo 'Running SonarQube Analysis...'

                withSonarQubeEnv('Sonarqube_server') {

                    sh '''
                    $SCANNER_HOME/bin/sonar-scanner \
                    -Dsonar.projectKey=iniberita \
                    -Dsonar.projectName=iniberita \
                    -Dsonar.sources=. \
                    -Dsonar.token=$SONAR_TOKEN
                    '''
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
            steps {
                echo 'Deploying application...'

                sh '''
                docker compose up -d --build
                '''
            }
        }
    }

    post {
        always {
            echo 'Pipeline selesai'
        }

        success {
            echo 'Pipeline berhasil!'
        }

        failure {
            echo 'Pipeline gagal!'
        }
    }
}