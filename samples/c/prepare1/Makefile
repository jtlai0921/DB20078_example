CC = gcc
INCLUDES = -I/usr/include/mysql

# 動態連結 libmysqlclient 函式庫
LIBS =  -lmysqlclient

# 靜態連結 libmysqlclient 函式庫
# LIBS =  -L/usr/lib/mysql/  -lmysqlclient -lz

all:prepare

main.o: main.c
	$(CC) -c $(INCLUDES)  main.c

prepare: main.o
	$(CC) -o prepare main.o $(LIBS)

clean:
	rm -f prepare main.o *~
