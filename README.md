lodfe
=====
SPARQLエンドポイントを用いたLinked Open Dataフロントエンド( [Pubby](https://github.com/cygri/pubby) みたいなやつ)

## Installation
### From source
```
$ git clone https://github.com/Babibubebon/lodfe.git
$ cd lodfe
$ composer install
$ cp config/datasets.php{.example,}
```

And configure your Web server.

### Docker image
Create `docker-compose.yml`
```
version: "3"
services:
  php:
    image: babibubebon/lodfe:latest-php
    volumes:
      - ./datasets.php:/var/www/lodfe/config/datasets.php:ro

  nginx:
    image: babibubebon/lodfe:latest-nginx
    ports:
      - 80:80
    depends_on:
      - php
```

Create `datasets.php` like the below example:
```
<?php

return [
    'dbpedia-ja' => [
        'host_name' => 'localhost',
        'resource_uri' => 'http://ja.dbpedia.org/resource/{id}',
        'html_uri' => 'http://ja.dbpedia.org/page/{id}',
        'data_uri' => 'http://ja.dbpedia.org/data/{id}',
        'endpoint' => 'http://ja.dbpedia.org/sparql',
        'http' => [
            'timeout' => 30,
        ]
    ],
];
```

Launch the containers
```
$ docker-compose up -d
```

Open browser and goto http://localhost/resource/WHITE_ALBUM2
