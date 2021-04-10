FROM nginx:1.19-alpine

RUN apk add shadow && \
  usermod -u 82 -g 82 nginx

COPY docker/web/setup.sh /bin
RUN chmod +x /bin/*.sh

WORKDIR /app/src/public

CMD [ "setup.sh" ]
