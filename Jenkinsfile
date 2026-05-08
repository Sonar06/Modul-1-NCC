pipeline {
    agent any

    environment {
        SONAR_TOKEN = credentials('1')
    }

    stages {

        stage('Clone Repository') {
            steps {
                echo 'Cloning Repository...'

                git branch: '*/Modul-2',
                    credentialsId: 'github',
                    url: 'https://github.com/Sonar06/Modul-NCC.git'
            }
        }

        stage('Build Docker') {
            steps {
                echo 'Building Docker Image...'

                sh 'docker compose build'
            }
        }

        stage('PHP Syntax Test') {
            steps {
                echo 'Running PHP Syntax Test...'

                sh 'find . -name "*.php" -exec php -l {} \\;'
            }
        }

        stage('SonarQube Analysis') {
            steps {
                echo 'Starting SonarQube Analysis...'
                script {

                    // Nama scanner HARUS sama
                    // dengan yang dibuat di
                    // Manage Jenkins -> Tools
                    def scannerHome = tool 'Sonarqube'

                    withSonarQubeEnv('Sonarqube') {

                        sh """
                        ${scannerHome}/bin/sonar-scanner \
                        -Dsonar.projectKey=iniberita \
                        -Dsonar.projectName=iniberita \ 
                        -Dsonar.sources=. \
                        -Dsoonar.tests=. \
                        -Dsonar.host.url=http://20.196.72.213:9000 \
                        -Dsonar.login=$SONAR_TOKEN
                        """
                    }
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
                echo 'Deploying Application...'

                sh '''
                docker compose down
                docker compose up -d --build
                '''
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