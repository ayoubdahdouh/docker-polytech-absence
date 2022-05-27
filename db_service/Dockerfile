FROM mysql

ENV MYSQL_DATABASE store 
ENV MYSQL_USER "user1" 
ENV MYSQL_PASSWORD "#@B5d1be" 
ENV MYSQL_ROOT_PASSWORD "#@B5d1be" 

COPY ./scripts/ /docker-entrypoint-initdb.d/



# FROM mysql as builder

# RUN ["sed", "-i", "s/exec \"$@\"/echo \"not running $@\"/", "/usr/local/bin/docker-entrypoint.sh"]

# ENV MYSQL_ROOT_PASSWORD=root

# COPY store.sql /docker-entrypoint-initdb.d/

# RUN ["/usr/local/bin/docker-entrypoint.sh", "mysqld", "--datadir", "/initialized-db", "--aria-log-dir-path", "/initialized-db"]

# FROM mysql

# COPY --from=builder /initialized-db /var/lib/mysql