FROM node:7.9.0

WORKDIR /app
COPY ./webpack.config.js /app/webpack.config.js

RUN apt-get update && apt-get install -my wget gnupg
RUN apt-get install -y gnupg2

RUN npm init -y
RUN npm install -g webpack
RUN npm install --save react react-dom
RUN npm install --save react react-bootstrap
RUN npm install --save redux react-redux
RUN npm install --save-dev babel-loader babel-core babel-preset-es2015 babel-preset-react babel-preset-stage-3

CMD ["webpack", "-d", "--watch"]