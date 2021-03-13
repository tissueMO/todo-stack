FROM nginx:1.19-alpine

RUN apk add shadow && \
  usermod -u 82 -g 82 nginx

WORKDIR /app/src/public
