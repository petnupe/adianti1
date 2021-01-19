BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS "parentesco" (
	"id"	INTEGER NOT NULL,
	"nome"	text,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "paciente" (
	"id"	INTEGER NOT NULL,
	"nome"	text,
	"dataNasc"	date,
	"cartaoSus"	text,
	"rg"	text,
	"cpf"	text,
	"dataEntrada"	date,
	"genero"	char,
	"ativo"	boolean,
	"alfabetizado"	boolean,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "patologia" (
	"id"	INTEGER NOT NULL,
	"nome"	text,
	"descricao"	text,
	"cid"	integer,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "telefone" (
	"id"	INTEGER NOT NULL,
	"ddd"	text,
	"numero"	text,
	"responsavel_id"	int NOT NULL,
	FOREIGN KEY("responsavel_id") REFERENCES "responsavel"("id"),
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "medicamento" (
	"id"	INTEGER NOT NULL,
	"nome"	text,
	"descricao"	text,
	"medidas_id"	int NOT NULL,
	"miligramas"	integer,
	FOREIGN KEY("medidas_id") REFERENCES "medidas"("id"),
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "medidas" (
	"id"	INTEGER NOT NULL,
	"sigla"	text,
	"descricao"	text,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE IF NOT EXISTS "responsavel" (
	"id"	INTEGER NOT NULL,
	"nome"	integer,
	"parentesco_id"	int NOT NULL,
	PRIMARY KEY("id" AUTOINCREMENT),
	FOREIGN KEY("parentesco_id") REFERENCES "parentesco"("id")
);
CREATE TABLE IF NOT EXISTS "alergia" (
	"id"	INTEGER NOT NULL,
	"descricao"	text,
	"paciente_id"	int NOT NULL,
	PRIMARY KEY("id" AUTOINCREMENT),
	FOREIGN KEY("paciente_id") REFERENCES "paciente"("id")
);
CREATE TABLE IF NOT EXISTS "historico" (
	"id"	INTEGER NOT NULL,
	"descricao"	text,
	"paciente_id"	int NOT NULL,
	PRIMARY KEY("id" AUTOINCREMENT),
	FOREIGN KEY("paciente_id") REFERENCES "paciente"("id")
);
CREATE TABLE IF NOT EXISTS "patologia_paciente" (
	"id"	INTEGER NOT NULL,
	"patologia_id"	int NOT NULL,
	"paciente_id"	int NOT NULL,
	PRIMARY KEY("id" AUTOINCREMENT),
	FOREIGN KEY("paciente_id") REFERENCES "paciente"("id"),
	FOREIGN KEY("patologia_id") REFERENCES "patologia"("id")
);
CREATE TABLE IF NOT EXISTS "responsavel_paciente" (
	"id"	INTEGER NOT NULL,
	"responsavel_id"	int NOT NULL,
	"paciente_id"	int NOT NULL,
	PRIMARY KEY("id" AUTOINCREMENT),
	FOREIGN KEY("responsavel_id") REFERENCES "responsavel"("id"),
	FOREIGN KEY("paciente_id") REFERENCES "paciente"("id")
);
CREATE TABLE IF NOT EXISTS "medicamento_paciente" (
	"id"	INTEGER NOT NULL,
	"medicamento_id"	int NOT NULL,
	"paciente_id"	int NOT NULL,
	"mes_referente"	integer,
	"quantidade"	numeric(10, 2),
	"hora"	integer,
	"miligramas"	NUMERIC(10, 2) NOT NULL,
	PRIMARY KEY("id" AUTOINCREMENT),
	FOREIGN KEY("medicamento_id") REFERENCES "medicamento"("id"),
	FOREIGN KEY("paciente_id") REFERENCES "paciente"("id")
);
INSERT INTO "paciente" ("id","nome","dataNasc","cartaoSus","rg","cpf","dataEntrada","genero","ativo","alfabetizado") VALUES (1,'PETERSON NUNES PEDROSO','10/02/1979','12345678909','9065208101','984.945.460-15','01/01/2021','M',1,1),
 (2,'TAIS DE FATIMA OLIVEIRA','31/03/1974','1234567899','12313132','781.826.200-82','07/01/2021','F',1,1),
 (3,'MATEUS OLIVEIRA PEDORSO','04/02/2010','1234568795645645','12313165465','665.947.470-61','07/01/2021','M',1,1),
 (4,'DAVI OLIVEIRA PEDROSO','08/08/2012',NULL,NULL,'053.409.470-84','12/01/2021','M',1,1);
INSERT INTO "medicamento" ("id","nome","descricao","medidas_id","miligramas") VALUES (1,'Propanolol','Remédio para convulsões',1,150),
 (2,'Diazepam ',NULL,1,250),
 (3,'Tetraciclina',NULL,1,25),
 (4,'Xarope','Bom para a tosse',2,100),
 (5,'Paracetamol',NULL,1,100),
 (6,'Paracetamol',NULL,1,200),
 (7,'Paracetamol',NULL,1,500),
 (8,'Paracetamol',NULL,1,750);
INSERT INTO "medidas" ("id","sigla","descricao") VALUES (1,'CP','Comprimidos'),
 (2,'GT','Gotas');
INSERT INTO "medicamento_paciente" ("id","medicamento_id","paciente_id","mes_referente","quantidade","hora","miligramas") VALUES (1,1,1,NULL,10,8,100),
 (2,1,1,NULL,1,NULL,750),
 (3,4,1,NULL,1,NULL,170),
 (4,1,1,NULL,10,NULL,100),
 (5,1,1,NULL,1,NULL,100),
 (6,1,1,NULL,1,18,750);
COMMIT;
