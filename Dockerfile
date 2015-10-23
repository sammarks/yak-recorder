FROM node:0.10-onbuild
RUN apt-get update
RUN apt-get install php5 php5-cli php5-mcrypt curl php5-curl -y
