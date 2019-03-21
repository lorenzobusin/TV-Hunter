SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS utente;
CREATE TABLE utente (
  id int  AUTO_INCREMENT PRIMARY KEY,
  username char(100) UNIQUE NOT NULL,
  password char(64) NOT NULL,
  email char(100) NOT NULL,
  foto_profilo varchar(500) NOT NULL DEFAULT '../img/fotoProfilo/user.png',
  nome char(64) NOT NULL,
  cognome char(64) NOT NULL,
  data_nascita date NOT NULL,
  tipo enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS serie;
CREATE TABLE serie (
  id int PRIMARY KEY,
  miniatura varchar(500),
  background varchar(500),
  titolo char(100) NOT NULL,
  distribuzione char(100) NOT NULL,
  descrizione varchar(1000) NOT NULL,
  creatore varchar(100) NOT NULL,
  terminata boolean NOT NULL,
  consigliato int DEFAULT 0,
  non_consigliato int DEFAULT 0,
  preferiti int DEFAULT 0,
  voto decimal(2,1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS preferiti;
CREATE TABLE preferiti (
  id_serie int,
  id_utente int,

  FOREIGN KEY (id_serie) REFERENCES serie(id) ,
  FOREIGN KEY (id_utente) REFERENCES utente(id),

  PRIMARY KEY (id_serie, id_utente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS consiglio;
CREATE TABLE consiglio (
  id_serie int,
  id_utente int,
  consigliato boolean NOT NULL,

  FOREIGN KEY (id_serie) REFERENCES serie(id) ,
  FOREIGN KEY (id_utente) REFERENCES utente(id),

  PRIMARY KEY (id_serie, id_utente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS post;
CREATE TABLE post (
  id int  AUTO_INCREMENT PRIMARY KEY,
  id_serie int,
  id_utente int,
  testo varchar (500) NOT NULL,
  cancellato boolean NOT NULL DEFAULT '0',

  FOREIGN KEY (id_serie) REFERENCES serie(id) ,
  FOREIGN KEY (id_utente) REFERENCES utente(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS commento;
CREATE TABLE commento (
  id int  AUTO_INCREMENT PRIMARY KEY,
  id_episodio int,
  id_utente int,
  immagine varchar(500),
  testo varchar (500) NOT NULL,

  FOREIGN KEY (id_episodio) REFERENCES episodio(id) ,
  FOREIGN KEY (id_utente) REFERENCES utente(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS risposta;
CREATE TABLE risposta (
  id int  AUTO_INCREMENT PRIMARY KEY,
  id_commento int,
  id_utente int,
  testo varchar (500) NOT NULL,

  FOREIGN KEY (id_commento) REFERENCES commento(id) ,
  FOREIGN KEY (id_utente) REFERENCES utente(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS attore;
CREATE TABLE attore (
  id int PRIMARY KEY,
  miniatura varchar(500),
  nome char(100) NOT NULL,
  cognome char(100) NOT NULL,
  bio varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS serie_attore;
CREATE TABLE serie_attore (
  id_serie int,
  id_attore int,

  FOREIGN KEY (id_serie) REFERENCES serie(id) ,
  FOREIGN KEY (id_attore) REFERENCES attore(id),

  PRIMARY KEY (id_serie, id_attore)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS genere;
CREATE TABLE genere (
  id int PRIMARY KEY,
  nome char(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS serie_genere;
CREATE TABLE serie_genere (
  id_serie int NOT NULL,
  id_genere int NOT NULL,

  FOREIGN KEY (id_serie) REFERENCES serie(id) ,
  FOREIGN KEY (id_genere) REFERENCES genere(id),

  PRIMARY KEY (id_serie, id_genere)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS episodio;
CREATE TABLE episodio (
  id int PRIMARY KEY,
  titolo char(100) NOT NULL,
  descrizione varchar(500) NOT NULL,
  numero int NOT NULL,
  data date NOT NULL,
  stagione int NOT NULL,
  visualizzato int DEFAULT 0, 
  id_serie int NOT NULL,

  FOREIGN KEY (id_serie) REFERENCES serie(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS visto;
CREATE TABLE visto (
  id_episodio int,
  id_utente int,

  FOREIGN KEY (id_episodio) REFERENCES episodio(id) ,
  FOREIGN KEY (id_utente) REFERENCES utente(id),

  PRIMARY KEY (id_episodio, id_utente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS voto;
CREATE TABLE voto (
  id_serie int,
  id_utente int,
  valutazione int NOT NULL,

  FOREIGN KEY (id_serie) REFERENCES serie(id) ,
  FOREIGN KEY (id_utente) REFERENCES utente(id),

  PRIMARY KEY (id_serie, id_utente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS segnalazione;
CREATE TABLE segnalazione (
  id int  AUTO_INCREMENT PRIMARY KEY,
  id_ref int,
  id_utente int,
  checked boolean NOT NULL DEFAULT '0',
  tipo enum ('post','commento','risposta'),

  UNIQUE(id_utente, id_ref),

  FOREIGN KEY (id_utente) REFERENCES utente(id)

  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS messaggi ;

CREATE TABLE IF NOT EXISTS messaggi (
  id INT(11) NOT NULL AUTO_INCREMENT,
  messaggio VARCHAR(256) NOT NULL,
  risposta VARCHAR(256) NULL DEFAULT NULL,
  user_id INT(11) NOT NULL,
  admin_id INT(11),
  PRIMARY KEY (id),
  INDEX fk_user_id_idx (user_id ASC),
  INDEX fk_admin_id_idx (admin_id ASC),
  CONSTRAINT fk_admin_id
    FOREIGN KEY (admin_id)
    REFERENCES utente (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_user_id
    FOREIGN KEY (user_id)
    REFERENCES utente (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;






