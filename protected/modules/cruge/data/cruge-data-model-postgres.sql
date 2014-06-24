/*
	Cruge Data Model
	----------------

	lista de tablas de Cruge.

	aqui van dos grupos:

		1. aquellas propias de Cruge.

		2. aquellas del paquete de autenticacion oficial del Yii, pero con una modificacion minima.

	tablas:
		cruge_system, cruge_user, cruge_session, cruge_field, cruge_fieldvalue
			@author: Christian Salazar H. <christiansalazarh@gmail.com> @salazarchris74

		cruge_authitem, cruge_authitemchild, cruge_authassignment
			paquete original de Yii, pero con modificaciones en cruge_authassignment
			para relacionarla con cruge_user (foregin key on delete cascade), ademas
			de cambiarle el tipo de clave del iduser de VARCHAR(64) a INT
*/
CREATE TABLE cruge_system (
  idsystem serial,
  name VARCHAR(45) NULL ,
  largename VARCHAR(45) NULL ,
  sessionmaxdurationmins integer NULL DEFAULT 30 ,
  sessionmaxsameipconnections integer NULL DEFAULT 10 ,
  sessionreusesessions integer NULL DEFAULT 1,
  sessionmaxsessionsperday integer NULL DEFAULT -1 ,
  sessionmaxsessionsperuser integer NULL DEFAULT -1 ,
  systemnonewsessions integer NULL DEFAULT 0,
  systemdown integer NULL DEFAULT 0 ,
  registerusingcaptcha integer NULL DEFAULT 0 ,
  registerusingterms integer NULL DEFAULT 0 ,
  terms varchar(4096) ,
  registerusingactivation integer NULL DEFAULT 1 ,
  defaultroleforregistration VARCHAR(64) NULL ,
  registerusingtermslabel VARCHAR(100) NULL ,
  registrationonlogin integer NULL DEFAULT 1 ,
  PRIMARY KEY (idsystem) )
;
delete from cruge_system;
INSERT INTO cruge_system (idsystem,name,largename,sessionmaxdurationmins,sessionmaxsameipconnections,sessionreusesessions,sessionmaxsessionsperday,sessionmaxsessionsperuser,systemnonewsessions,systemdown,registerusingcaptcha,registerusingterms,terms,registerusingactivation,defaultroleforregistration,registerusingtermslabel,registrationonlogin) VALUES
 (1,'default',NULL,30,10,1,-1,-1,0,0,0,0,'',0,'','',1);

CREATE TABLE cruge_session (
  idsession serial,
  iduser INT NOT NULL ,
  created BIGINT NULL ,
  expire bigint NULL ,
  status integer NULL DEFAULT 0 ,
  ipaddress VARCHAR(45) NULL ,
  usagecount integer NULL DEFAULT 0 ,
  lastusage bigint NULL ,
  logoutdate bigint NULL ,
  ipaddressout VARCHAR(45) NULL ,
  PRIMARY KEY (idsession)
  )
;

CREATE  TABLE cruge_user (
  iduser  serial,
  regdate bigint NULL ,
  actdate bigint NULL ,
  logondate bigint NULL ,
  username VARCHAR(64) NULL ,
  email VARCHAR(45) NULL ,
  password VARCHAR(64) NULL,
  authkey VARCHAR(100) NULL,
  state integer NULL DEFAULT 0 ,
  totalsessioncounter integer NULL DEFAULT 0 ,
  currentsessioncounter integer NULL DEFAULT 0 ,
  PRIMARY KEY (iduser)
  )
;

delete from cruge_user;
insert into cruge_user(username, email, password, state) values
 ('admin', 'admin@tucorreo.com','admin',1)
 ,('invitado', 'invitado','nopassword',1)
;


CREATE  TABLE cruge_field (
  idfield  serial,
  fieldname VARCHAR(20) NOT NULL ,
  longname VARCHAR(50) NULL ,
  position integer NULL DEFAULT 0 ,
  required integer NULL DEFAULT 0 ,
  fieldtype integer NULL DEFAULT 0 ,
  fieldsize integer NULL DEFAULT 20 ,
  maxlength integer NULL DEFAULT 45 ,
  showinreports integer NULL DEFAULT 0 ,
  useregexp VARCHAR(512) NULL ,
  useregexpmsg VARCHAR(512) NULL ,
  predetvalue varchar(4096),
  PRIMARY KEY (idfield)
  );

CREATE  TABLE cruge_fieldvalue (
  idfieldvalue  serial,
  iduser INT NOT NULL ,
  idfield INT NOT NULL ,
  value varchar(4096),
  PRIMARY KEY (idfieldvalue) ,
  CONSTRAINT fk_cruge_fieldvalue_cruge_user1
    FOREIGN KEY (iduser )
    REFERENCES cruge_user (iduser )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT fk_cruge_fieldvalue_cruge_field1
    FOREIGN KEY (idfield )
    REFERENCES cruge_field (idfield )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
 ;

CREATE TABLE cruge_authitem (
  name VARCHAR(64) NOT NULL ,
  type integer NOT NULL ,
  description TEXT NULL DEFAULT NULL ,
  bizrule TEXT NULL DEFAULT NULL ,
  data TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (name) )
;

CREATE TABLE cruge_authassignment (
  userid INT NOT NULL ,
  bizrule TEXT NULL DEFAULT NULL ,
  data TEXT NULL DEFAULT NULL ,
  itemname VARCHAR(64) NOT NULL ,
  PRIMARY KEY (userid, itemname) ,
  CONSTRAINT fk_cruge_authassignment_cruge_authitem1
    FOREIGN KEY (itemname )
    REFERENCES cruge_authitem (name )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_cruge_authassignment_user
    FOREIGN KEY (userid )
    REFERENCES cruge_user (iduser )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
;


CREATE TABLE cruge_authitemchild (
  parent VARCHAR(64) NOT NULL ,
  child VARCHAR(64) NOT NULL ,
  PRIMARY KEY (parent, child) ,
  CONSTRAINT crugeauthitemchild_ibfk_1
    FOREIGN KEY (parent )
    REFERENCES cruge_authitem (name )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT crugeauthitemchild_ibfk_2
    FOREIGN KEY (child )
    REFERENCES cruge_authitem (name )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
;
