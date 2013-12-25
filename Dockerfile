FROM ubuntu
MAINTAINER Jon Surrell, jon@surrell.es

# Update
RUN echo "deb http://archive.ubuntu.com/ubuntu precise main universe" > /etc/apt/sources.list
RUN apt-get update

# Get our required stuff
RUN apt-get install -y git vim apache2 php5 php5-mysqlnd mysql-server
RUN git clone --branch dev https://github.com/SirReal/gestorneo.git /var/www

# Daemons
ENTRYPOINT ["mysqld"]
USER daemon

ENTRYPOINT ["apache2ctl", "start"]
USER daemon

EXPOSE 80
