<?php

/*
CONFIGURACION DE ROLES PARA APP
Las configuraciones deben estar como lo establece la base _> administration.cat_modulo_rol
donde 
    @var => @valor
    ADM_TOTAL => 1
*/

return [
    /* ROLES_TABLA_DATABASE*/
    'ADM_TOTAL' => 1, //PERMITE EL ACCESO TOTAL A LOS MODULOS DEL SISTEMA
    'COR_TOTAL' => 2, //PERMITE EL ACCESO TOTAL A LOS MODULOS DE CORRESPONDENCIA
    'COR_USUARIO' => 3, //PERMITE EL ACCESO A ACCIONES COMO AGREGAR/MODIFICAR/ELIMINAR FILTRADO POR AREA DE LOS MODULOS DE CORRESPONDENCIA
    'COR_ENLACE' => 4, //PERMITE EL ACCESO A INFORMACION SOLO PARA ENLACE Y ACCIONES COMO MODIFICAR

    /* VARIABLES PARA LA IDENTIFICACION DE TABLAS DE <CORRESPONDENCIA></CORRESPONDENCIA*/
    'CP_TABLE_CORRESPONDENCIA' => 1, //Hace referencia a la tabla principal de correspondencia para identificar su id en la tabla de consecutivo asi como la tabla correspondencia.cat_tipo_docuemnto
    'CP_TABLE_OFICIO' => 2, //Hace referencia a la tabla de oficio para identificar su id en la tabla de consecutivo asi como la tabla correspondencia.cat_tipo_docuemnto
    'CP_TABLE_INTERNO' => 3,
    'CP_TABLE_CIRCULAR' => 4,
    'CP_TABLE_EXPEDIENTE' => 5,
    
    /*VARIABLES TIPO DE DOCUMENTO CLOUD ENTRADA Y SALIDA, HACE REFERENCIA A LA TABLA correspondencia.cat_tipo_doc_cloud */
    'CAT_TIPO_DOC_ENTRADA' => 1, //HACE REFERENCIA A EL ID DE ENTRADA DE LA TABLA
    'CAT_TIPO_DOC_SALIDA' => 2, //HACE REFERENCIA A EL ID DE SALIDA DE LA TABLA

    /*VARIABLES DE CONFIGURACION DE CLOUD  correspondencia.config_cloud */
    'MAX_OFICIOS_ENTRADA' => 1, //HACE REFERENCIA A NOMBRE Y ID DE LA LLAVE PRIMARY
    'MAX_ANEXOS_ENTRADA' => 2,//HACE REFERENCIA A NOMBRE Y ID DE LA LLAVE PRIMARY
    'MAX_OFICIOS_SALIDA' => 3,//HACE REFERENCIA A NOMBRE Y ID DE LA LLAVE PRIMARY
    'MAX_ANEXOS_SALIDA' => 4,//HACE REFERENCIA A NOMBRE Y ID DE LA LLAVE PRIMARY
    'MAX_SIZE_ARCHIVO' => 5,//HACE REFERENCIA A NOMBRE Y ID DE LA LLAVE PRIMARY
    'EXTENSIONES_VALIDAS' => 7,//HACE REFERENCIA A NOMBRE Y ID DE LA LLAVE PRIMARY

    /*VARIABLES DE CONFIGURACION correspondencia.cat_tipo_doc_cloud */
    'CONFIG_CLOUD_SALIDA' => 2, //HACE REFERENCIA AL ID DE SALIDA
    'CONFIG_CLOUD_ENTRADA' => 1, //HACE REFERENCIA AL ID DE ENTRADA

    /*VARIABLES DE TIPO DE DOCUMENTO A SUBIR ALFRESCO TABLE- correspondencia.cat_nombre_oficio, sirve para validar la carpeta a inserccion */
    'CLOUD_ALFRESCO_OFICIO' => 1, //HACE REFERENCIA AL ID DE OFICIO
    'CLOUD_ALFRESCO_INTERNO' => 2, //HACE REFERENCIA AL ID DE INTERNO
    'CLOUD_ALFRESCO_EXPEDIENTE' => 3, //HACE REFERENCIA AL ID DE EXPEDIENTE
    'CLOUD_ALFRESCO_CIRCULAR' => 4, //HACE REFERENCIA AL ID DE CIRCULAR


];