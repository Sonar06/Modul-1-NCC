pipeline {
    agent any

    environment {
        SONAR_TOKEN   = credentials('Sonarqube') 
        SONAR_SERVER  = 'Sonarqube_server' 
        SCANNER_HOME  = tool 'Sonarqube' 
        PROJECT_KEY   = 'iniberita'
    }

    stages {
        stage('Clone Repository') {
            steps {
                echo 'Cloning Repository...'
                git branch: 'Modul-2',
                    credentialsId: 'github',
                    url: 'https://github.com/Sonar06/Modul-NCC.git'
            }
        }

        stage('Build & Test') {
            // Poin Plus 6: Optimasi dengan Parallel
            parallel {
                stage('Docker Build') {
                    steps {
                        echo 'Building Docker Image...'
                        sh 'docker compose build'
                    }
                }
                stage('PHP Lint') {
                    steps {
                        echo 'Checking PHP Syntax...'
                        sh 'find . -name "*.php" -exec php -l {} \\;'
                    }
                }
            }
        }

        stage('SonarQube Analysis') {
            steps {
                echo 'Starting Analysis...'
                withSonarQubeEnv("${SONAR_SERVER}") {
                    sh """
                        ${SCANNER_HOME}/bin/sonar-scanner \
                        -Dsonar.projectKey=${PROJECT_KEY} \
                        -Dsonar.projectName=${PROJECT_KEY} \
                        -Dsonar.sources=. \
                        -Dsonar.login=${SONAR_TOKEN}
                    """
                }
            }
        }

        stage('Quality Gate') {
            steps {
                echo 'Waiting for Quality Gate result...'
                timeout(time: 5, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }

        stage('Deploy') {
            steps {
                echo 'Deploying Application...'
                sh '''
                    docker compose down || true
                    docker compose up -d --build
                '''
            }
        }
    }

    post {
        success {
            echo 'Pipeline SUCCESS: Kode berkualitas dan berhasil dideploy!'
        }
        failure {
            echo 'Pipeline FAILED: Ada masalah pada kode atau kualitas tidak lulus.'
        }
    }
}