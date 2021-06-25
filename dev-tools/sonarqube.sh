#!/bin/bash
#
# Args: deploy.sh
#

mkdir /opt/sonarscanner

cd /opt/sonarscanner

wget https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-4.3.0.2102-linux.zip

unzip sonar-scanner-cli-4.3.0.2102-linux.zip

rm sonar-scanner-cli-4.3.0.2102-linux.zip

chmod +x sonar-scanner-4.3.0.2102-linux/bin/sonar-scanner

ln -s /opt/sonarscanner/sonar-scanner-4.3.0.2102-linux/bin/sonar-scanner /usr/local/bin/sonar-scanner

pwd

ls -lisa

chmod 777 sonar-scanner-4.3.0.2102-linux/conf/sonar-scanner.properties

echo 'sonar.Host.url=http://slump.aduxia.net:9000' >> sonar-scanner-4.3.0.2102-linux/conf/sonar-scanner.properties

# sonar-scanner-3.3.0.1492-linux/bin/sonar-scanner \
#   -Dsonar.projectKey=<project_name> \
#   -Dsonar.sources=. \
#   -Dsonar.Host.url=http://<your_sonarqube_server_url> \
#   -Dsonar.login=<token_from_gitlab_UI>




