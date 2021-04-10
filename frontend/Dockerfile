# 開発ステージ
FROM node:14-alpine AS development

WORKDIR /app/src

RUN apk update && \
    yarn install

CMD [ "yarn", "dev" ]


# 本番ステージ
FROM development AS production

ENV HOST 0.0.0.0
ADD src /app/src

RUN yarn build

CMD [ "yarn", "start" ]
