(UTF-8)

執行範例之前，您必須先建立 test_images 資料庫、以及 images 資料表。

CREATE DATABASE testimages;

USE testimages;

CREATE TABLE images (
  id    BIGINT NOT NULL AUTO_INCREMENT,
  name  VARCHAR(100) NOT NULL,
  type  VARCHAR(100) NOT NULL,
  image LONGBLOB NOT NULL,
  ts    TIMESTAMP(14) NOT NULL,
  PRIMARY KEY  (id));
