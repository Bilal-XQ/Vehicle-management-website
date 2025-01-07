/*==============================================================*/
/* Nom de SGBD :  MySQL 5.0                                     */
/* Date de création :  1/7/2025 9:05:05 PM                      */
/*==============================================================*/


drop table if exists AFFECTATION_VEHICULE;

drop table if exists ASSOCIE;

drop table if exists CAMPUS;

drop table if exists DIRECTEUR;

drop table if exists DOCUMENT_VEHICULE;

drop table if exists ELEVE;

drop table if exists EST_AFFECTE_A;

drop table if exists ITINERAIRE;

drop table if exists PERSONNEL;

drop table if exists POINT_ARRET;

drop table if exists TRANSACTION;

drop table if exists VEHICULE;

/*==============================================================*/
/* Table : AFFECTATION_VEHICULE                                 */
/*==============================================================*/
create table AFFECTATION_VEHICULE
(
   ID_AFFECTATION_      char(10) not null,
   ANNEE_SCOLAIRE_      varchar(20),
   ID_VEHICULE_         varchar(50),
   primary key (ID_AFFECTATION_)
);

/*==============================================================*/
/* Table : ASSOCIE                                              */
/*==============================================================*/
create table ASSOCIE
(
   NUM_IDENTIFICATION_  varchar(50) not null,
   ID_ITINERAIRE_       bigint not null,
   ID_AFFECTATION_      char(10) not null,
   primary key (NUM_IDENTIFICATION_, ID_ITINERAIRE_)
);

/*==============================================================*/
/* Table : CAMPUS                                               */
/*==============================================================*/
create table CAMPUS
(
   ID_CAMPUS_           bigint not null,
   CIN_                 varchar(20),
   ADRESSE_             varchar(255),
   TELEPHONE_           varchar(15),
   VILLE_               varchar(100),
   primary key (ID_CAMPUS_)
);

/*==============================================================*/
/* Table : DIRECTEUR                                            */
/*==============================================================*/
create table DIRECTEUR
(
   CIN_                 varchar(20) not null,
   NOM_                 varchar(100),
   PRENOM_              varchar(100),
   ADRESSE_             varchar(255),
   TELEPHONE_           varchar(15),
   DERNIER_DIPLOME      varchar(100),
   HISTORIQUE_PROFESSIONNEL text,
   primary key (CIN_)
);

/*==============================================================*/
/* Table : DOCUMENT_VEHICULE                                    */
/*==============================================================*/
create table DOCUMENT_VEHICULE
(
   ID_DOCUMENT_         bigint not null,
   NUM_IDENTIFICATION_  varchar(50) not null,
   TYPE_DOCUMENT_       varchar(50),
   DATE_VALIDITE_       date,
   ID_VEHICULE_         varchar(50),
   primary key (ID_DOCUMENT_)
);

/*==============================================================*/
/* Table : ELEVE                                                */
/*==============================================================*/
create table ELEVE
(
   ID_ELEVE_            bigint not null,
   NOM_                 varchar(100),
   PRENOM_              varchar(100),
   CLASSE_NIVEAU        varchar(10),
   FRAIS_ASSURANCE      float(8,2),
   DATE_PAIEMENT_ASSURANCE date,
   primary key (ID_ELEVE_)
);

/*==============================================================*/
/* Table : EST_AFFECTE_A                                        */
/*==============================================================*/
create table EST_AFFECTE_A
(
   ID_PERSONNEL_        bigint not null,
   NUM_IDENTIFICATION_  varchar(50) not null,
   primary key (ID_PERSONNEL_, NUM_IDENTIFICATION_)
);

/*==============================================================*/
/* Table : ITINERAIRE                                           */
/*==============================================================*/
create table ITINERAIRE
(
   ID_ITINERAIRE_       bigint not null,
   ID_ELEVE_            bigint not null,
   LIBELLE_             varchar(100),
   DESCRIPTION_         text,
   TARIF_               float(8,2),
   primary key (ID_ITINERAIRE_)
);

/*==============================================================*/
/* Table : PERSONNEL                                            */
/*==============================================================*/
create table PERSONNEL
(
   ID_PERSONNEL_        bigint not null,
   NOM_                 varchar(100),
   PRENOM_              varchar(100),
   TYPE_PERSONNEL_      varchar(20),
   primary key (ID_PERSONNEL_)
);

/*==============================================================*/
/* Table : POINT_ARRET                                          */
/*==============================================================*/
create table POINT_ARRET
(
   ID_POINT_            bigint not null,
   ID_ITINERAIRE_       bigint not null,
   NOM_ARRET_           varchar(100),
   ORDRE_               int,
   primary key (ID_POINT_)
);

/*==============================================================*/
/* Table : TRANSACTION                                          */
/*==============================================================*/
create table TRANSACTION
(
   NUM_TRANSACTION_     bigint not null,
   ID_ELEVE_            bigint not null,
   ANNEE_SCOLAIRE_      varchar(20),
   DATE_PAIEMENT_       date,
   TYPE_PAIEMENT_       varchar(50),
   MONTANT_             float(8,2),
   ATTRIBREDUCTION_UT_43 float(8,2),
   primary key (NUM_TRANSACTION_)
);

/*==============================================================*/
/* Table : VEHICULE                                             */
/*==============================================================*/
create table VEHICULE
(
   NUM_IDENTIFICATION_  varchar(50) not null,
   ID_CAMPUS_           bigint not null,
   TYPE_VEHICULE_       varchar(50),
   CAPACITE_MAX_        int,
   ETAT_                varchar(50),
   MARQUE_              varchar(100),
   MODELE_              varchar(100),
   ANNEE_FABRICATION_   int,
   primary key (NUM_IDENTIFICATION_)
);

alter table ASSOCIE add constraint FK_ASSOCIE foreign key (ID_ITINERAIRE_)
      references ITINERAIRE (ID_ITINERAIRE_) on delete restrict on update restrict;

alter table ASSOCIE add constraint FK_ASSOCIE2 foreign key (NUM_IDENTIFICATION_)
      references VEHICULE (NUM_IDENTIFICATION_) on delete restrict on update restrict;

alter table ASSOCIE add constraint FK_ASSOCIE3 foreign key (ID_AFFECTATION_)
      references AFFECTATION_VEHICULE (ID_AFFECTATION_) on delete restrict on update restrict;

alter table CAMPUS add constraint FK_EST_DIRIGE_PAR foreign key (CIN_)
      references DIRECTEUR (CIN_) on delete restrict on update restrict;

alter table DOCUMENT_VEHICULE add constraint FK_EST_DOCUMENTE_PAR foreign key (NUM_IDENTIFICATION_)
      references VEHICULE (NUM_IDENTIFICATION_) on delete restrict on update restrict;

alter table EST_AFFECTE_A add constraint FK_EST_AFFECTE_A foreign key (NUM_IDENTIFICATION_)
      references VEHICULE (NUM_IDENTIFICATION_) on delete restrict on update restrict;

alter table EST_AFFECTE_A add constraint FK_EST_AFFECTE_A2 foreign key (ID_PERSONNEL_)
      references PERSONNEL (ID_PERSONNEL_) on delete restrict on update restrict;

alter table ITINERAIRE add constraint FK_UTILISE foreign key (ID_ELEVE_)
      references ELEVE (ID_ELEVE_) on delete restrict on update restrict;

alter table POINT_ARRET add constraint FK_CONTIENT foreign key (ID_ITINERAIRE_)
      references ITINERAIRE (ID_ITINERAIRE_) on delete restrict on update restrict;

alter table TRANSACTION add constraint FK_EFFECTUE foreign key (ID_ELEVE_)
      references ELEVE (ID_ELEVE_) on delete restrict on update restrict;

alter table VEHICULE add constraint FK_POSSEDE foreign key (ID_CAMPUS_)
      references CAMPUS (ID_CAMPUS_) on delete restrict on update restrict;

