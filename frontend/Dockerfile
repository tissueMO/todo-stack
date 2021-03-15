FROM node:14-alpine AS development

WORKDIR /app/src

RUN apk update && \
    yarn install

CMD [ "yarn", "dev" ]

FROM development as build

ADD src /app/src

ENV HOST 0.0.0.0
