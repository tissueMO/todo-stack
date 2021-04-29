FROM nginx:1.19-alpine AS development

RUN apk add shadow && \
  usermod -u 82 -g 82 nginx

COPY docker/web/setup.sh /bin
COPY docker/web/default.conf /etc/nginx/conf.d
RUN chmod +x /bin/*.sh

WORKDIR /app/src/public

CMD [ "setup.sh" ]

FROM development AS production

ADD src /app/src
WORKDIR /app/src

CMD [ "setup.sh" ]
