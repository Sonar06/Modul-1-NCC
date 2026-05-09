pipeline {

    agent any

    environment {
        SONAR_TOKEN = credentials('Sonarqube')
        SCANNER_HOME = tool 'Sonarqube'
    }

    stages {

        stage('Checkout') {
            steps {
                echo 'Repository Checked Out'
            }
        }

        stage('PHP Syntax Test') {
            steps {
                echo 'Running PHP Syntax Check...'

                sh '''
                find . -name "*.php" -exec php -l {} \\;
                '''
            }
        }

        stage('SonarQube Analysis') {
            steps {

                echo 'Running SonarQube Analysis...'

                withSonarQubeEnv('Sonarqube_server') {

                    sh """
                    ${SCANNER_HOME}/bin/sonar-scanner \
                    -Dsonar.projectKey=iniberita \
                    -Dsonar.projectName=iniberita \
                    -Dsonar.sources=. \
                    -Dsonar.host.url=http://20.196.72.213:9000 \
                    -Dsonar.login=$SONAR_TOKEN
                    """
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
    }

    post {

        success {
            echo 'Pipeline SUCCESS'
        }

        failure {
            echo 'Pipeline FAILED'
        }
    }
}