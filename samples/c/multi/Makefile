CC = gcc
INCLUDES = -I/usr/include/mysql

# 動態連結 libmysqlclient 函式庫
LIBS =  -lmysqlclient

# 靜態連結 libmysqlclient 函式庫
# LIBS =  -L/usr/lib/mysql/  -lmysqlclient -lz

all:multi

main.o: main.c
	$(CC) -c $(INCLUDES)  main.c

multi: main.o
	$(CC) -o multi main.o $(LIBS)

clean:
	rm -f multi main.o *~
