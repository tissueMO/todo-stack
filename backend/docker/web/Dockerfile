FROM nginx:1.19-alpine

RUN apk add shadow && \
  usermod -u 82 -g 82 nginx

COPY docker/web/setup.sh /bin
COPY docker/web/default.conf /etc/nginx/conf.d
RUN chmod +x /bin/*.sh

WORKDIR /app/src/public

CMD [ "setup.sh" ]
