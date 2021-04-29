FROM nginx:1.19 AS development

COPY docker/web/setup.sh /bin
COPY docker/web/default.conf /etc/nginx/conf.d
RUN chmod +x /bin/*.sh

WORKDIR /app/src/public

CMD [ "setup.sh" ]

FROM development AS production

ADD src /app/src
WORKDIR /app/src

CMD [ "setup.sh" ]
