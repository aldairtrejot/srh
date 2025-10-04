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
    'ADM_TOTAL'    => 1,  // ACCESO TOTAL A TODO EL SISTEMA
    'COR_TOTAL'    => 2,  // ACCESO TOTAL A CORRESPONDENCIA
    'COR_USUARIO'  => 3,  // ACCESO A ACCIONES POR ÁREA EN CORRESPONDENCIA
    'COR_ENLACE'   => 4,  // SOLO ENLACE (según tus políticas)
    'COR_CRH'      => 9,  // Rol para CRH
    'COR_VISTA'    => 10, // Solo vista de todas las áreas
    'COR_TUAF'     => 14, // Solo lectura por prefijo IB-UAF-TURNOS-
    // 'COR_CRHTOD' => X, // <-- Si defines este rol en DB, agrega su ID aquí

    /* VARIABLES PARA LA IDENTIFICACION DE TABLAS DE <CORRESPONDENCIA></CORRESPONDENCIA*/
    'CP_TABLE_CORRESPONDENCIA'         => 1,
    'CP_TABLE_OFICIO'                  => 2,
    'CP_TABLE_INTERNO'                 => 3,
    'CP_TABLE_CIRCULAR'                => 4,
    'CP_TABLE_EXPEDIENTE'              => 5,
    'CP_TABLE_CORRESPONDENCIA_INTERNO' => 6,
    'CP_TABLE_NOTAS_INTERNO'           => 7,
    'CP_TABLE_REQUERIMRNTOS_INTERNO'   => 8,
    'CP_TABLE_CERTIFICACIONES'         => 9,
    'CP_TABLE_CIRCULARES_EXT'          => 10,

    /*VARIABLES TIPO DE DOCUMENTO CLOUD ENTRADA Y SALIDA*/
    'CAT_TIPO_DOC_ENTRADA' => 1,
    'CAT_TIPO_DOC_SALIDA'  => 2,

    /*VARIABLES DE CONFIGURACION DE CLOUD  correspondencia.config_cloud */
    'MAX_OFICIOS_ENTRADA' => 1,
    'MAX_ANEXOS_ENTRADA'  => 2,
    'MAX_OFICIOS_SALIDA'  => 3,
    'MAX_ANEXOS_SALIDA'   => 4,
    'MAX_SIZE_ARCHIVO'    => 5,
    'EXTENSIONES_VALIDAS' => 7,

    /*VARIABLES DE CONFIGURACION correspondencia.cat_tipo_doc_cloud */
    'CONFIG_CLOUD_SALIDA'  => 2,
    'CONFIG_CLOUD_ENTRADA' => 1,

    /*VARIABLES DE TIPO DE DOCUMENTO A SUBIR ALFRESCO */
    'CLOUD_ALFRESCO_OFICIO'           => 1,
    'CLOUD_ALFRESCO_INTERNO'          => 2,
    'CLOUD_ALFRESCO_EXPEDIENTE'       => 3,
    'CLOUD_ALFRESCO_CIRCULAR'         => 4,
    'CLOUD_ALFRESCO_CORRESPONDENCIA'  => 5,
    'CLOUD_ALFRESCO_CIRCULAR_EXTERNO' => 6,

    // Mapeo dinámico rol -> columna de área en tbl_correspondencia
    'ROLE_AREA_COLUMN' => [
        'COR_CRH'    => 'id_cat_area_1',
        'COR_CRHTOD' => 'id_cat_area_2', // <-- solo si defines COR_CRHTOD en este mismo archivo
    ],

    // Prefijo opcional para rol COR_TUAF (si aplica a num_turno_sistema)
    'ROLE_PREFIX_FILTERS' => [
        'COR_TUAF' => 'IB-UAF-TURNOS-',
    ],

    // ====== NUEVOS FLAGS (OPCIONALES) ======
    // Si true, amplía visibilidad de áreas a descendientes via rel_cat_area_jerarquia_1/2
    'USE_HIERARCHY' => false,

    // Si true (default), incluye copias (ctrl_transcribir_correspondencia) en la visibilidad
    'INCLUDE_COPIES_IN_VISIBILITY' => true,
];
