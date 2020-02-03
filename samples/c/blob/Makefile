CC = gcc
INCLUDES = -I/usr/include/mysql 
# 動態連結 libmysqlclient 函式庫
LIBS =  -lmysqlclient
# 靜態連結 libmysqlclient 函式庫
# LIBS =  -L/usr/lib/mysql -lmysqlclient -lz

all:blobtest

main.o: main.c
	$(CC) -c $(INCLUDES)  main.c

blobtest: main.o
	$(CC) -o blobtest main.o $(LIBS)

clean:
	rm -f blobtest main.o test-copy.jpg *~
