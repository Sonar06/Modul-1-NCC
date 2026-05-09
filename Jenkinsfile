pipeline {
    agent any

    environment {
        // Poin Plus: Menggunakan Credential Management (ID harus sesuai dengan di Jenkins)
        SONAR_TOKEN  = credentials('Sonarqube') 
        // Poin Plus: Menggunakan Environment Variable untuk fleksibilitas
        SCANNER_HOME = tool 'Sonarqube' 
        SONAR_SERVER = 'Sonarqube_server'
        PROJECT_KEY  = 'iniberita'
    }

    stages {
        stage('Preparation & Linting') {
            // Poin Plus: Optimasi Pipeline dengan Parallel Stage
            parallel {
                stage('Checkout') {
                    steps {
                        echo 'Checking out source code...'
                        // Langkah ini otomatis jika menggunakan "Pipeline script from SCM"
                        checkout scm
                    }
                }
                stage('PHP Syntax Test') {
                    steps {
                        echo 'Running PHP Syntax Check using Docker...'
                        // Kita meminjam image php:8.2-cli untuk mengecek syntax
                        sh 'docker run --rm -v $(pwd):/app -w /app php:8.2-cli php -l index.php' 
                        // Atau jika ingin cek semua file:
                        sh 'docker run --rm -v $(pwd):/app -w /app php:8.2-cli find . -name "*.php" -exec php -l {} +'
                    }
                }
            }
        }

        stage('SonarQube Analysis') {
            steps {
                echo 'Starting Code Quality Analysis...'
                // Menghubungkan Jenkins dengan SonarQube
                withSonarQubeEnv("${SONAR_SERVER}") {
                    sh """
                    ${SCANNER_HOME}/bin/sonar-scanner \
                    -Dsonar.projectKey=${PROJECT_KEY} \
                    -Dsonar.projectName=${PROJECT_KEY} \
                    -Dsonar.sources=. \
                    -Dsonar.host.url=http://20.196.72.213:9000 \
                    -Dsonar.login=${SONAR_TOKEN}
                    """
                }
            }
        }

        stage('Quality Gate') {
            steps {
                echo 'Waiting for SonarQube Quality Gate...'
                // Menggagalkan pipeline jika standar kualitas tidak terpenuhi
                timeout(time: 5, unit: 'MINUTES') {
                    waitForQualityGate abortPipeline: true
                }
            }
        }

        stage('Deploy') {
            steps {
                echo 'Deploying Application...'
                /*
                   Hanya akan jalan jika lolos Quality Gate.
                   Menggunakan Docker Compose sesuai file Docker kamu.
                */
                sh '''
                docker compose down || true
                docker compose up -d --build
                '''
            }
        }
    }

    post {
        always {
            echo "Build finished with status: ${currentBuild.result}"
        }
        success {
            echo 'Pipeline SUCCESS: Kode lulus sensor dan berhasil dideploy!'
        }
        failure {
            echo 'Pipeline FAILED: Cek log build atau dashboard SonarQube.'
        }
    }
}