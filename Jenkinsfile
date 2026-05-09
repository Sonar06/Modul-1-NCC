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

                withSonarQubeEnv('Sonarqube_server') {

                    sh '''
                    sonar-scanner \
                      -Dsonar.projectKey=${SONAR_PROJECT_KEY} \
                      -Dsonar.projectName=${SONAR_PROJECT_NAME} \
                      -Dsonar.sources=. \
                      -Dsonar.host.url=http://70.153.136.203:9000 \
                      -Dsonar.token=${SONAR_TOKEN} \
                      -Dsonar.python.version=3
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

            when {
                branch 'main'
            }

            steps {

                echo 'Deploying application...'
                echo 'Deploy berhasil.'
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