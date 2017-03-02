# Start from apisilex image
FROM spdc1k8sregistry11.privalia.pin/apisilex:latest

# Use nginx server config
COPY docker-nginx-server.conf /etc/nginx/conf.d/sites-available/default.conf

# Prepare environment
RUN mkdir -p /var/log/nginx/ && \
    rm -rf /etc/nginx/conf.d/sites-enabled/*.conf && \
    cd /etc/nginx/conf.d/sites-enabled/ && \
    ln -s ../sites-available/default.conf

RUN touch /etc/php.d/00-custom-php.ini && \
    echo "[PHP]"                            >> /etc/php.d/00-custom-php.ini && \
    echo "display_errors = On"              >> /etc/php.d/00-custom-php.ini && \
    echo "error_reporting = E_ALL"          >> /etc/php.d/00-custom-php.ini && \
    echo -e "\n[Date]"                      >> /etc/php.d/00-custom-php.ini && \
    echo "date.timezone = Europe/Madrid"    >> /etc/php.d/00-custom-php.ini && \
    echo -e "\n"                            >> /etc/php.d/00-custom-php.ini

WORKDIR /var/www
