FROM mysql:8

MAINTAINER 329365307@qq.com

ENV MYSQL_USER=mysql \
MYSQL_PASSWORD=mysql \
MYSQL_ROOT_PASSWORD=jiulian666

COPY payload/conf/custom.cnf /etc/mysql/conf.d/
COPY payload/poker_init.sql /docker-entrypoint-initdb.d/

EXPOSE 3306
LABEL org.tenbayblockchain.tcc.version=1.0.0 \
      org.tenbayblockchain.tcc.base.version=0.0.1
