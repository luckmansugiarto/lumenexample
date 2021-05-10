FROM webdevops/php-apache-dev:7.4-alpine

ADD https://github.com/ufoscout/docker-compose-wait/releases/download/2.8.0/wait /wait
RUN chmod +x /wait

RUN mkdir -p /app/vendor && chown -R application:application /app/vendor

CMD /wait && bash -c "cd ./app && composer install" && supervisord
