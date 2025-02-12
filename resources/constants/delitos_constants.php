<?php
// ESTE ARCHIVO CONTIENE LAS AGRUPACIONES DE LOS DELITOS PARA LA GENERACION DE LOS INFORMES 

// ROBOS ROBO MENOS TENTATIVAS Y ROBO DE VEHICULO
$robos = ['182A','182B','182C','182D','182E','182F','182G','182L','182K','182H','182I','182J','181K','181L','181M','181N','181O','181P','181Q',
    '181R','181S','181T','181U','181V','181W','181X','181Y','181Z','2350','1811','1812','1815','1816','1817','1818','1819','181A','181B',
    '181C','181D','181E','181F','181G','181H','181I','181J'];

// ROBO DE VEHICULO
$robo_vehiculo = ['1814'];

// LESIONES DOLOSAS
$lesiones_dolosas = ['1622','1633','1628','1629','1631','1632','1637','1638','1639','1624'];

// HOMICIDIO CULPOSO
$homicidio_culposo = ['161J','1613','1615','1616','161A','161B','161E'];

// HOSTIGAMIENTO SEXUAL
$hostigamiento_sexual = ['1431'];

// NARCOMENUDEO
$narcomenudeo = ['2311','2312'];

// VIOLENCIA FAMILIAR
$violencia_familiar = ['1160'];

// LESIONES CULPOSAS
$lesiones_culposas = ['1625','1626','1627','1623'];

// LESIONES CULPOSAS SIN DATOS
$lesiones_csin_datos = ['1625','1623'];

// LESIONES CULPOSAS CON ARMA DE FUEGO
$lesiones_cul_fuego = ['1626'];

// LESIONES CULPOSAS ARMA BLANCA
$lesiones_cul_blanca = ['1627'];

// RECEPTACIÓN
$receptacion = ['1820'];

// HOMICIDIO DOLOSO
$homicidio_doloso = ['161K','161L','1618','161C','161D','161H','161I','1671','1612','1614','1619','1617','161G'];

// HOMICIDIO DOLOSO CON FEMINICIDIO
$homicidio_doloso_feminicidio = ['161K','161L','1618','161C','161D','161H','161I','1671','1612','1614','1619','1617','161G'];

// HOMICIDIO DOLOSO SIN DATOS
$homicidio_doloso_sin_datos = ['161G','1617','1614','1618','1619','161I','1612','161H','1671'];

// HOMICIDIO DOLOSO CON ARMA DE FUEGO
$homicidio_arma_fuego = ['161K','161C'];

// HOMICIDIO DOLOSO CON ARMA BLANCA
$homicidio_arma_blanca = ['161L','161D'];

// DAÑO EN LAS COSAS CULPOSO
$daño_culposo = ['1884','1894','1885','1883'];

// DAÑO EN LAS COSAS DOLOSO
$daño_doloso = ['1881','1882'];

// FRAUDE
$fraude = ['1853','1854','1855','2510','1850'];

// AMENAZAS
$amenazas = ['1340','1341'];

// DESPOJO
$despojo = ['1872','1873','1870'];

// VIOLACIÓN
$violacion = ['1412','1413','1410'];

// ABIGEATO
$abigeato = ['1830'];

// ABUSO SEXUAL
$abuso_sexual = ['1432','1430'];

// PRIVACIÓN DE LA LIBERTAD
$privacion_libertad = ['1310'];

// ABUSO DE AUTORIDAD Y USO ILÍCITO DE LA FUERZA PÚBLICA
$abuso_autoridad = ['0771','0772','0770'];

// FALSIFICACIÓN DE DOCUMENTOS
$falsificacion_documentos = ['0921','0922','0923','0924','0920','1835'];

// ABUSO DE CONFIANZA
$abuso_confianza = ['1841','1842','1840'];

// ALLANAMIENTO DE MORADA
$allanamiento_morada = ['1370','1372'];

// INCUMPLIMIENTO DE LA OBLIGACIÓN ALIMENTARIA
$obligacion_alimentaria = ['1140'];

// RETENCIÓN O SUSTRACCIÓN DE PERSONA
$sustraccion_persona = ['1150'];

// DELITOS FORESTALES
$delitos_ecologia = ['2111','2112','2113','2110','2240'];

// ARMAS PROHIBIDAS
$armas_prohibidas = ['0230'];

// ESTUPRO
$estupro = ['1420'];

// EXTORSIÓN
$extorsion = ['1350'];

// FEMINICIDIO
$feminicidio = ['161G'];

// DELITOS COMETIDOS POR CONDUCTORES DE VEHÍCULOS
$conductores_vehiculos = ['0321','0320'];

// SECUESTRO
$secuestro = ['1322','1323','1324','1325','1326','1327','2631','2632','2633','1320','1328','1329'];

// TRATA DE PERSONAS
$trata_personas = ['0532'];

// TURISMO SEXUAL
$turismo_sexual = ['0552','0550'];

// OTROS DELITOS (RESTO)
$resto_delitos = ['1350','1341','182A','182B','182C','182D','182E','182F','182G','182L','182K','182H','182I','182J','181K','181L','181M','181N','181O',
    '181P','181Q','181R','181S','181T','181U','181V','181W','181X','181Y','181Z','2350','1811','1812','1815','1816','1817','1818','1819','181A','181B',
    '181C','181D','181E','181F','181G','181H','181I','181J','1814','1622','1633','1628','1629','1631','1632','1637','1638','1639','1624','161J','1613',
    '161E','1615','1616','161A','161B','2311','1160','1625','1626','1627','1623','1820','161K','161L','1618','161C','161D','161H','161I','1671','1612',
    '1614','1619','1617','1884','1894','1885','1883','1881','1882','1853','1854','1855','2510','1850','1340','1872','1873','1870','1412','1413','1410',
    '1830','1432','1430','1310','0771','0772','0770','0921','0922','0923','0920','0924','1835','1841','1842','1840','1370','1372','1140','1150','2111',
    '2112','2113','2110','2240','0230','1420','1350','0321','0320','0532','1431','161G','0552','0550','2312','1322','1323','1324','1325','1326','1327',
    '2631','2632','2633','1320','1328','1329'];

// LESIONES DOLOSAS ARMA DE FUEGO
$lesiones_arma_fuego = ['1628'];

// LESIONES DOLOSAS ARMA BLANCA
$lesiones_arma_blanca = ['1629'];

// LESIONES DOLOSAS SIN DATOS
$lesiones_sin_datos = ['1622', '1633', '1631', '1632', '1637', '1638', '1639', '1624'];

// HOMICIDIO CULPOSO ARMA DE FUEGO
$homicidio_culposo_fuego = ['161A'];

// HOMICIDIO CULPOSO ARMA BLANCA
$homicidio_culposo_blanca = ['161B'];

// HOMICIDIO CULPOSO RESTO
$homicidio_culposo_resto = ['161J', '1613', '1615', '1616', '161E'];
?>

<?php
// Todos los ids de los delitos
$delitos = [
    '2630', '1830', '1831', '1661', '1664', '1660', '0770', '1840', '1842', '1841', '1432', '1430', '2313', '1372', '1370', '1371',
    '1110', '1835', '1340', '0230', '1360', '1363', '1369', '1364', '136A', '1367', '1362', '1368', '1365', '1361', '1560', '1570',
    '0310', '1550', '2340', '1120', '1530', '0750', '0710', '0730', '0140', '0520', '0521', '2151', '2150', '1883', '1885', '1882',
    '1894', '1884', '1881', '2312', '0790', '1910', '0240', '0870', '0150', '0440', '0320', '0321', '2240', '2120', '0810', '0260',
    '0261', '2110', '1020', '2010', '1312', '0411', '0410', '1870', '1871', '1540', '0860', '0740', '2580', '0850', '0780', '1420',
    '0210', '0211', '1350', '1351', '1352', '0840', '0920', '0910', '0922', '0923', '161G', '1669', '1850', '1851', '1855', '1853',
    '1617', '161K', '1613', '161B', '161A', '161J', '1614', '161D', '161C', '161I', '1619', '1611', '1618', '1615', '161E', '1612',
    '1616', '1431', '0830', '1130', '1131', '2370', '1140', '0760', '1210', '2360', '2330', '1630', '0540', '0531', '0530', '1639',
    '1623', '1627', '1626', '1624', '1633', '1629', '1628', '1631', '1632', '1621', '1637', '1638', '1625', '1622', '2152', '0130',
    '2311', '2410', '1740', '1730', '1720', '1890', '0420', '1640', '0720', '1750', '0551', '0550', '2610', '1310', '1311', '0220',
    '1331', '0110', '1820', '0412', '0820', '0610', '181A', '181S', '1819', '1816', '1815', '181B', '182H', '182K', '182J', '182I',
    '181D', '181F', '181C', '181X', '181Y', '181W', '181E', '182L', '181G', '182A', '1818', '181R', '181Q', '181P', '1817', '1812',
    '181N', '181M', '181L', '181I', '181O', '181H', '181K', '182G', '2350', '1814', '181J', '181Z', '1813', '182E', '182D', '182C',
    '181U', '181V', '1811', '182B', '1320', '1321', '1324', '1326', '1322', '1328', '0120', '0924', '1150', '1151', '1710', '0450',
    '0451', '1330', '0532', '0460', '0510', '0950', '1860', '0940', '1010', '0930', '1410', '0330', '0430', '1412', '1411', '1413',
    '1160'
];

// AGRUPACIÓN PARA ROBOS POR MODALIDAD

$Arobo_simple = ['1811'];
$Arobo_vehiculo = ['1814'];
$Arobo_chofer_repartidor = ['1815'];
$Arobo_chofer_autobus = ['1816'];
$Arobo_a_vehiculos = ['1817', '181Y', '181M'];
$Arobo_transeuntes = ['182A', '1818', '182B', '182L'];
$Arobo_habitacion = ['1819'];
$Arobo_bancos = ['181A', '181S', '181W'];
$Arobo_a_comercios = ['181B'];
$Arobo_industria = ['181C'];
$Arobo_escuela = ['181D'];
$Arobo_oficina = ['181E'];
$Arobo_gasolinera = ['181F'];
$Arobo_taller = ['181G'];
$Arobo_motocicleta = ['181H'];
$Arobo_documento = ['181I'];
$Arobo_maquinaria = ['182G', '181J'];
$Arobo_calificado = [
    '182H', '182K', '182J', '182I', '181X', '181R', '181Q', '181P', '1812', '181N',
    '181L', '181O', '181K', '2350', '181Z', '182E', '182D', '182C', '181U', '181V'
];

// LISTA DE LOS 48 DELITOS DE ROBO
$Arobo = ['1810'];
$Abancos = ['181A'];
$Acaja_ahorro = ['181S'];
$Acasa_habitacion = ['1819'];
$Acomercios = ['181B'];
$Acuentahab_dentro_sucursal = ['182H'];
$Acuentahab_tarjeta = ['182K'];
$Acuentahab_retiro_cajero = ['182J'];
$Acuentahab_retiro_ventanilla = ['182I'];
$Aescuelas = ['181D'];
$Agasolineras = ['181F'];
$Aindustria = ['181C'];
$Abancaria_atm = ['181W'];
$Areligiosa = ['181X'];
$Ainterior_vehiculo = ['181Y'];
$Aoficinas = ['181E'];
$Aperson_lugar_privado = ['182L'];
$Atalleres = ['181G'];
$Atranseunte_via_publica = ['182A'];
$Atranseunte_abierto_publico = ['182B'];
$Atranseuntes = ['1818'];
$Apublico_camion = ['181R'];
$Apublico_combi = ['181Q'];
$Apublico_taxi = ['181P'];
$Aa_vehiculos = ['1817'];
$Acalificado = ['1812'];
$Achofer_autobuses = ['1816'];
$Achofer_repartidor = ['1815'];
$Aarte_sacro = ['181N'];
$Aautopartes = ['181M'];
$Acobre = ['181L'];
$Adocumentos = ['181I'];
$Aembarcaciones = ['182F'];
$Ahidrocarburos = ['181O'];
$Amotocicleta = ['181H'];
$Aremolque = ['181K'];
$Atractor = ['182G'];
$Auso = ['2350'];
$Ade_vehiculos = ['1814'];
$Avehiculos_maquinaria = ['181J'];
$Acarretera = ['181Z'];
$Atransporte_individual = ['182E'];
$Atransporte_publico_colectivo = ['182D'];
$Atransporte_publico_individual = ['182C'];
$Aascendentes_y_descendentes = ['181U'];
$Aentre_conyuges = ['181V'];
$Aequiparado = ['181T'];
$Arobo_simple2 = ['1811'];
?>
