FROM openresty/openresty:centos

MAINTAINER 329365307@qq.com

RUN yum install -y wget \
    && wget https://busybox.net/downloads/binaries/1.28.1-defconfig-multiarch/busybox-x86_64 \
    && chmod +x busybox-x86_64 && mv busybox-x86_64 /usr/bin/busybox

COPY payload/config/nginx.conf /usr/local/openresty/nginx/conf/nginx.conf
COPY payload/config/conf.d/* /etc/nginx/conf.d/
ADD payload/files /wwwroot/app/files
ADD payload/all /wwwroot/poker/all
ADD payload/admin /wwwroot/admin

LABEL org.tenbayblockchain.tcc.version=1.0.1 \
      org.tenbayblockchain.tcc.base.version=0.0.1
