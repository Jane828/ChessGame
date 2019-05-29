FROM registry.cn-hangzhou.aliyuncs.com/tenbayblockchain/game-php:1.0.0
MAINTAINER 329365307@qq.com

ENV DB_HOST=game_mysql \
    DB_USER=root \
    DB_PWD=jiulian666 \
    DB_NAME=poker \
    DB_PORT=3306 \
    DB_CHARSET=utf8mb4 \
    REDIS_HOST=game_redis:6379 \
    REDIS_PWD= \
    LOG_DIR=/wwwroot/logs/ \
    REGISTER_PORT=2003 \
    GATEWAY_CLIENT_PORT=20032 \
    GATEWAY_SERVER_PORT=20031 \
    GATEWAY_CLIENT_START_PORT=25032 \
    GATEWAY_SERVER_START_PORT=25031 \
    GAME_WEB_PORT=20010 \
    GAME_CLIENT_PORT=20032 \
    GAME_SERVER_PORT=20031 \
    TIMER_ADDR=127.0.0.1 \
    TIMER_PORT=20039 \
    PROCESSOR_PORT=12002 \
    PROCESSOR_ADDR=127.0.0.1 \
    REDIS_DB_LOGS=1 \
    REDIS_DB=10 \
    TICKET_MODE=2 \
    ROOM_IS_REUSE=0 \
    GAME_NUM_REACH_ROUND=10 \
    ROOM_AUTO_BREAK=1 \
    IS_SEND_GAME_SCORE=0 \
    DEALER_NUM=2 \
    VERIFICATION_CODE=987654321098765432109876543210zz

EXPOSE 20032
WORKDIR /wwwroot/

COPY payload/start_one.sh /wwwroot/
ADD payload/socket /wwwroot/socket
ADD payload/timer /wwwroot/timer
LABEL org.tenbayblockchain.tcc.version=1.0.1 \
      org.tenbayblockchain.tcc.base.version=0.0.1
