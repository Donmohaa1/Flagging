<?php

// create_tables();
function create_tables(){

    $string = "mysql:hostname=".DBHOST."";
    $con = new PDO($string, DBUSER,DBPASSWORD);

    $query = "create database if not exists ".DBNAME;
    $stm = $con->prepare($query);
    $stm->execute();

    $query = "use ".DBNAME;
    $stm = $con->prepare($query);
    $stm->execute();

    //flag_data
    $query = "create table if not exists flag_data(
        id int primary key auto_increment,
        issuingBody varchar(100) not null,
        certificateType varchar(255) not null,
        cargoOrigin varchar(150) not null,
        shipmentRoute varchar(150) not null,
        certificateNo varchar(150) not null,
        certificateCopy varchar(1000) not null,
        customsDeclNo varchar(150) not null,
        customsEntryCopy varchar(1000) not null,
        importerName varchar(150) not null,
        importerContact varchar(150) not null,
        exporterName varchar(150) not null,
        agentName varchar(150) not null,
        agentContact varchar(150) not null,
        transportMode varchar(150) not null,
        transporterName varchar(150) not null,
        vehicleNumber varchar(150) not null,
        vehicleImages varchar(1000) not null,
        dischargeLocation varchar(150) not null,
        finalDestination varchar(150) not null,
        fobCurrency varchar(150) not null,
        fobValue varchar(150) not null,
        incoterm varchar(150) not null,
        freightCurrency varchar(150) not null,
        freightValue varchar(150) not null,
        invoiceCopy varchar(1000) not null,
        supportingDocs varchar(1000) not null,
        validationNotes varchar(150) not null,

        status  BIT default false, 

        date datetime default current_timestamp,

        key certificateNo (certificateNo)

    )";
    $stm = $con->prepare($query);
    $stm->execute();
}
