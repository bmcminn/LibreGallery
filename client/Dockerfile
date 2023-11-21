FROM node:lts-alpine

WORKDIR /app/client

COPY package*.json ./

RUN npm i

COPY . .

CMD [ "npm", "run", "dev" ]