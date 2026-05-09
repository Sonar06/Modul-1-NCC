pipeline {
    agent any

    environment {
        SONAR_TOKEN        = credentials('Sonarqube')
        SONAR_PROJECT_KEY  = 'iniberita'
        SONAR_PROJECT_NAME = 'iniberita'
        SCANNER_HOME       = tool 'Sonarqube' 
    }

    stages {
        stage('Checkout') {
            steps {
                echo '=== Stage 1: Ambil kode dari repository ==='
                checkout scm
            }
        }

        stage('Build') {
            steps {
                echo '=== Stage 2: Kompilasi / build Docker Image ==='
                // Paksa menggunakan socket lokal dengan mengosongkan DOCKER_HOST
                sh 'export DOCKER_HOST=""; docker build -t iniberita .'
            }
        }

        stage('Test') {
            steps {
                echo '=== Stage 3: Jalankan pengujian (Syntax Check) ==='
                // Paksa menggunakan socket lokal
                sh 'export DOCKER_HOST=""; docker run --rm -v $(pwd):/app -w /app php:8.2-cli php -l index.php'
            }
        }

        stage('Analyze') {
            steps {
                echo '=== Stage 4: Analisis SonarQube ==='
                script {
                    withSonarQubeEnv('Sonarqube_server') {
                        sh """
                        ${SCANNER_HOME}/bin/sonar-scanner \
                          -Dsonar.projectKey=${SONAR_PROJECT_KEY} \
                          -Dsonar.projectName=${SONAR_PROJECT_NAME} \
                          -Dsonar.sources=. \
                          -Dsonar.host.url=http://70.153.136.203:9000 \
                          -Dsonar.token=${SONAR_TOKEN}
                        """
                    }
                }
            }
        }

        stage('Quality Gate') {
            steps {
                echo '=== Menunggu Standar Kualitas SonarQube ==='
                timeout(time: 5, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }


        stage('Deploy') {
            steps {

                sh '''
                export DOCKER_HOST=""

                docker stop iniberita || true
                docker rm iniberita || true

                docker run -d --name iniberita -p 80:80 iniberita

                docker ps -a
                docker logs iniberita || true
                '''
            }
        }


    }

    post {
        success { echo 'Pipeline SUCCESS!' }
        failure { echo 'Pipeline FAILED!' }
        always { cleanWs() }
    }
}