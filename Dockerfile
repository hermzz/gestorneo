FROM ubuntu
MAINTAINER Jon Surrell, jon@surrell.es

# Get our required stuff
RUN apt-get update
RUN apt-get install -y git apache2 php5 php5-mysqlnd mysql-server
RUN git clone --branch dev https://github.com/SirReal/gestorneo.git /var/www

# Daemons
ENTRYPOINT ["mysqld"]
USER daemon

ENTRYPOINT ["apache2ctl", "start"]
USER daemon

EXPOSE 80
