FROM registry.cn-hangzhou.aliyuncs.com/tenbayblockchain/game-php:1.0.0
MAINTAINER 329365307@qq.com

ENV DB_HOST=game_mysql \
    DB_DATABSE=poker

COPY payload/www.conf /etc/php/7.0/fpm/pool.d/www.conf
RUN mkdir -p /wwwroot/log/app /run/php/
ADD payload/domain /wwwroot/domain
ADD payload/app /wwwroot/app
ADD payload/poker /wwwroot/poker
ADD payload/wechat /wwwroot/wechat
ADD payload/admin /wwwroot/admin

CMD ["php-fpm7.0", "-R"]
LABEL org.tenbayblockchain.tcc.version=1.0.1 \
      org.tenbayblockchain.tcc.base.version=0.0.1
