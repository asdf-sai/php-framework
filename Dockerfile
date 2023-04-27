FROM ubuntu:22.04

RUN apt update
RUN ln -s /usr/share/zoneinfo/Asia/Almaty /etc/localtime
RUN apt install -y nginx
RUN apt install -y php php-mysql php-fpm
RUN apt install -y mysql-server

COPY . /var/www/html
WORKDIR /var/www/html

# CMD ["tail", "-f", "/dev/null"]
CMD ["php", "-S", "0.0.0.0:8000"]
