CREATE DATABASE DiabetesDB;
USE DiabetesDB;

CREATE TABLE USUARIO (
  id_usu INT NOT NULL AUTO_INCREMENT,
  fecha_nacimiento DATE NOT NULL,
  nombre VARCHAR(25) NOT NULL,
  apellidos VARCHAR(25) NOT NULL,
  usuario VARCHAR(25) NOT NULL,
  contra VARCHAR(250) NOT NULL,
  PRIMARY KEY (id_usu)
);

CREATE TABLE CONTROL_GLUCOSA (
  fecha DATE NOT NULL,
  deporte INT NOT NULL,
  lenta INT NOT NULL,
  id_usu INT NOT NULL,
  PRIMARY KEY (fecha, id_usu),
  FOREIGN KEY (id_usu) REFERENCES USUARIO(id_usu)
    ON DELETE CASCADE 
    ON UPDATE CASCADE
);

CREATE TABLE COMIDA (
  tipo_comida VARCHAR(30) NOT NULL,
  gl_1h INT NOT NULL,
  gl_2h INT NOT NULL,
  raciones INT NOT NULL,
  insulina INT NOT NULL,
  fecha DATE NOT NULL,
  id_usu INT NOT NULL,
  PRIMARY KEY (tipo_comida, fecha, id_usu),
  FOREIGN KEY (fecha, id_usu) REFERENCES CONTROL_GLUCOSA(fecha, id_usu)
    ON DELETE CASCADE 
    ON UPDATE CASCADE
);

CREATE TABLE HIPERGLUCEMIA (
  glucosa INT NOT NULL,
  hora TIME NOT NULL,
  correccion INT NOT NULL,
  tipo_comida VARCHAR(30) NOT NULL,
  fecha DATE NOT NULL,
  id_usu INT NOT NULL,
  PRIMARY KEY (tipo_comida, fecha, id_usu),
  FOREIGN KEY (tipo_comida, fecha, id_usu) REFERENCES COMIDA(tipo_comida, fecha, id_usu)
    ON DELETE CASCADE 
    ON UPDATE CASCADE
);

CREATE TABLE HIPOGLUCEMIA (
  glucosa INT NOT NULL,
  hora TIME NOT NULL,
  tipo_comida VARCHAR(30) NOT NULL,
  fecha DATE NOT NULL,
  id_usu INT NOT NULL,
  PRIMARY KEY (tipo_comida, fecha, id_usu),
  FOREIGN KEY (tipo_comida, fecha, id_usu) REFERENCES COMIDA(tipo_comida, fecha, id_usu)
    ON DELETE CASCADE 
    ON UPDATE CASCADE
);
