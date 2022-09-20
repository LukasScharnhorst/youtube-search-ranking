-- create database lsp_2_youtube_api;

use lsp_2_youtube_api;



drop table if exists kanal;

create table kanal (
	id varchar(255) primary key not null
);



drop table if exists video;

create table video (
	id varchar(255) primary key not null,
    titel varchar(255),
    thumbnail_url varchar(255),
    kanal_id varchar(255),
    foreign key (kanal_id) references kanal(id)
);



drop table if exists keyword;

create table keyword (
	name varchar(255) primary key not null,
    last_updated date
);



drop table if exists keyword_video;

create table keyword_video (
	keyword_name varchar(255) not null,
    video_id varchar(255) not null,
    ranking_heute integer,
    ranking_gestern integer,
    last_updated date,
    primary key (keyword_name, video_id),
    foreign key (keyword_name) references keyword(name),
    foreign key (video_id) references video(id)
);



drop table if exists refresh;

create table refresh (
	id int primary key not null,
	last_updated datetime
);



select * from kanal;

select * from video;

select * from keyword;

select * from keyword_video;

select * from refresh;