pipeline {
    agent any

    environment {
        CI_ENV = 'production'
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: 'main', url: 'https://github.com/callmevan1120/CodeIgniter.git'
            }
        }

        stage('Install Dependencies') {
            steps {
                sh '''
                # Install dependencies
                composer install --no-dev --optimize-autoloader

                composer update
                
                # Pastikan package mikey179/vfsStream terinstal
                if ! composer show | grep -q "mikey179/vfsStream"; then
                    echo "Package mikey179/vfsStream tidak ditemukan, menginstal..."
                    composer require --dev mikey179/vfsStream
                fi

                # File yang akan diubah
                FILE="mikey179/vfsStream/src/main/php/org/bovigo/vfs/vfsStream.php"

                # Periksa apakah file ada sebelum menjalankan sed
                if [ -f "$FILE" ]; then
                    sed -i.bak s/name{0}/name[0]/ "$FILE"
                else
                    echo "File $FILE tidak ditemukan, melewati perintah sed."
                fi
                '''
            }
        }

        stage('Run Tests') {
            steps {
                sh 'phpunit'
            }
            post {
                success {
                    junit 'application/tests/results/*.xml'
                }
                failure {
                    echo 'Tests failed!'
                }
            }
        }

        stage('Deploy') {
            steps {
                echo 'Deploying to production environment...'
                // Tambahkan perintah deploy sesuai kebutuhan Anda
            }
        }
    }

    post {
        success {
            echo 'Pipeline completed successfully!'
        }
        failure {
            echo 'Pipeline failed!'
        }
    }
}
