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
COMMIT;
