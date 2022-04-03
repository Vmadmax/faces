def customContainerName = UUID.randomUUID().toString().replace("-", "");
pipeline {
  agent any
  options {
      // This is required if you want to clean before build
      skipDefaultCheckout(true)
    }
  stages {
    stage('Build') {
      steps {
          sh 'sudo rm -rf *'
          // Clean before build
          checkout scm
          sh 'cp ./laravel/.env.example ./laravel/.env'
          sh 'ln -s laravel/.env .env'
          sh "sed -i 's/HOST_PORT=8080/HOST_PORT=35${env.EXECUTOR_NUMBER}00/g' laravel/.env"
          sh "sed -i 's/DB_PORT=33061/DB_PORT=35${env.EXECUTOR_NUMBER}01/g' laravel/.env"
          sh "sed -i 's/REDIS_PORT=6379/REDIS_PORT=35${env.EXECUTOR_NUMBER}02/g' laravel/.env"
          sh "sed -i 's/DOCKER_CONTAINER_NAME=ayda/DOCKER_CONTAINER_NAME=${customContainerName}/g' laravel/.env"
          sh 'cp ${HOME}/dockerfile_check.sh dockerfile_check.sh && bash dockerfile_check.sh docker/php/Dockerfile'
          sh "docker-compose up -d"
          sh 'chmod +x ./bin/docker-exec'
          sh "./bin/docker-exec composer install"
          sh "./bin/docker-exec npm install"
          sh "./bin/docker-exec npm run production"
          sh 'sudo chown -R jenkins *'
          sh 'sudo chmod -R 777 laravel/storage'
        }
    }
    stage('Deploy master') {
      when { branch "master" }
      steps {
        sh 'sed -i "s/APP_ENV=local/APP_ENV=production/g" laravel/.env'
        sh 'sudo rm -rf laravel/vendor'
        sh "./bin/docker-exec composer install --no-dev"
        sh "./bin/docker-exec php vendor/bin/dep deploy stage=master"
        slackSend(message: 'Master-Release')
      }
    }

  }
  post {
      always {
        sh 'docker-compose down --volumes'
        sh 'sudo rm -rf *'
      }
    }
}
