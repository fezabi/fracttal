<?php

    // Datos de conexión para BD (AWS)
    const SERVER = 'ls-390c9ec1f4da8e6e7326ac9bcd9df95f1a9868ef.cbqfvl6qckj7.us-east-2.rds.amazonaws.com';
    const DB = 'activo-fracttal';
    const USER = 'dbmasteruser';
    const PASS = 'Tt[yCAh#=E:wYp+}JF`hg4)lY}YFd8HR';
    
    // URL para conexión
    const SGBD = "mysql:host=".SERVER.";dbname=".DB;

    // Datos para encriptación de data
    const METHOD = 'AES-256-CBC';
    const SECRET_KEY = 'C@$T@N0';
    const SECRET_IV = '140428';

    // Otros datos de conexión
    const API_KEY = '';
    const API_TOKEN = '';
    const COMPANY_DOMAIN = 'castano';

    // Datos conexion api Fracttal
    const API_URL = 'https://app.fracttal.com/api/';
    const ACCESS_TOKEN_URL = 'https://one.fracttal.com/oauth/token';
    const CLIENT_ID = 'pjK36G94KZxMVAogk3';
    const CLIENT_SECRET = 'wrt9Cphz6VO3PS8bAojNznYdtF2oUaey';