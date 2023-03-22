FROM mysql:5.7

MAINTAINER Stéphane

COPY ./my-config.cnf /etc/mysql/conf.d/
COPY ./.my.cnf /root/.my.cnf
RUN mkdir /var/snapshots && chown -R mysql: /var/snapshots
RUN mkdir /var/dumps && chown -R mysql: /var/dumps
