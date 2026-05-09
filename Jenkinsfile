pipeline {

    agent any

    environment {

        SONAR_TOKEN  = credentials('Sonarqube')
        SCANNER_HOME = tool 'Sonarqube'

        SONAR_SERVER = 'Sonarqube_server'
        PROJECT_KEY  = 'iniberita'
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

                withSonarQubeEnv("${SONAR_SERVER}") {

                    sh '''
                    ${SCANNER_HOME}/bin/sonar-scanner \
                    -Dsonar.projectKey=${PROJECT_KEY} \
                    -Dsonar.projectName=${PROJECT_KEY} \
                    -Dsonar.sources=. \
                    -Dsonar.host.url=http://20.196.72.213:9000 \
                    -Dsonar.token=$SONAR_TOKEN
                    '''
                }
            }
        }

        stage('Quality Gate') {

            steps {

                echo 'Checking Quality Gate...'

                timeout(time: 5, unit: 'MINUTES') {

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