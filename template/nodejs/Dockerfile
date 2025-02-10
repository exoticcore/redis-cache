# Use the official Node.js image as the base image
FROM node:20

WORKDIR /app

COPY package.json yarn.lock ./

RUN yarn install --production

COPY . .

EXPOSE 3034

ENV NODE_ENV=production
ENV PORT=3034
ENV DB_HOST=localhost
ENV DB_USER=root
ENV DB_PASSWORD=secret
ENV DB_NAME=rediscasedb
ENV DB_PORT=3308
ENV REDIS_URL=redis://localhost:6380
ENV REDIS_PASSWORD=secretpwd
ENV TTL_CACHE=3600

# Start the application
CMD ["yarn", "start"]