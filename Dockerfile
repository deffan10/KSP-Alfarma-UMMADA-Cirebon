# Kita ambil basis dari image yang Bapak pakai
FROM serversideup/php:8.1-fpm-nginx

# Masuk sebagai ROOT untuk install aplikasi
USER root

# Update sistem dan Install GD secara paksa
RUN apt-get update && \
    apt-get install -y php8.1-gd php8.1-intl php8.1-zip && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Kembali menjadi user biasa agar aman
USER www-data
