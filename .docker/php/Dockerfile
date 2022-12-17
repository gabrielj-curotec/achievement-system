FROM ambientum/php:8.0-nginx

USER root

COPY ./start.sh /usr/local/bin/start
RUN chmod u+x /usr/local/bin/start

RUN touch /var/log/cron.log
RUN touch /var/log/queue.log

# COPY cron /etc/cron.d/cron

# RUN chmod 0644 /etc/cron.d/cron
# RUN crontab /etc/cron.d/cron

USER ambientum

WORKDIR /var/www/app

CMD ["/usr/local/bin/start"]
