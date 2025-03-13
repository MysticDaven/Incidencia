<?php

namespace App;

trait DelitosTrait
{
    const I = [2,1,0];

    const I2 = [3,2,1,0];
    
    const MONTH = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
    
    const ROBOS = ['182A','182B','182C','182D','182E','182F','182G','182L','182K','182H','182I','182J','181K','181L','181M','181N','181O','181P','181Q',
    '181R','181S','181T','181U','181V','181W','181X','181Y','181Z','2350','1811','1812','1815','1816','1817','1818','1819','181A','181B',
    '181C','181D','181E','181F','181G','181H','181I','181J'];

    const ROBO_VEHICULO = ['1814'];

    const LESIONES_DOLOSAS = ['1622','1633','1628','1629','1631','1632','1637','1638','1639','1624'];

    const HOMICIDIO_CULPOSO = ['161J','1613','1615','1616','161A','161B','161E'];

    const HOSTIGAMIENTO_SEXUAL = ['1431'];

    const NARCOMENUDEO = ['2311', '2312'];

    const VIOLENCIA_FAMILIAR = ['1160'];

    const LESIONES_CULPOSAS = ['1625','1626','1627','1623'];

    const LESIONES_CSIN_DATOS = ['1625', '1623'];

    const LESIONES_CUL_FUEGO = ['1626'];

    const LESIONES_CUL_BLANCA = ['1627'];

    const RECEPTACION = ['1820'];

    const HOMICIDIO_DOLOSO = ['161K','161L','1618','161C','161D','161H','161I','1671','1612','1614','1619','1617','161G'];

    const HOMICIDIO_DOLOSO_FEMINICIDIO = ['161K','161L','1618','161C','161D','161H','161I','1671','1612','1614','1619','1617','161G'];

    const HOMICIDIO_DOLOSO_SIN_DATOS = ['161G','1617','1614','1618','1619','161I','1612','161H','1671'];

    const HOMICIDIO_ARMA_FUEGO = ['161K','161C'];

    const HOMICIDIO_ARMA_BLANCA = ['161L','161D'];

    const DANIO_CULPOSO = ['1884','1894','1885','1883'];

    const DANIO_DOLOSO = ['1881', '1882'];

    const FRAUDE = ['1853','1854','1855','2510','1850'];

    const AMENAZAS = ['1340', '1341'];

    const AMENAZAS_V2 = ['1340'];

    const DESPOJO = ['1872','1873','1870'];

    const VIOLACION = ['1412','1413','1410'];

    const ABIGEATO = ['1830'];

    const ABUSO_SEXUAL = ['1432', '1430'];

    const PRIVACION_LIBERTAD = ['1310'];

    const ABUSO_AUTORIDAD = ['0771','0772','0770'];

    const FALSIFICACION_DOCUMENTOS = ['0921','0922','0923','0924','0920','1835'];

    const ABUSO_CONFIANZA = ['1841','1842','1840'];

    const ALLANAMIENTO_MORADA = ['1370','1372'];

    //-------------------------------------------------------------------
    const OBLIGACION_ALIMENTARIA = ['1140'];

    const SUSTRACCION_PERSONA = ['1150'];

    const DELITOS_ECOLOGIA = ['2111', '2112', '2113', '2110', '2240'];

    const ARMAS_PROHIBIDAS = ['0230'];

    const ESTUPRO = ['1420'];

    const EXTORSION = ['1350'];

    const FEMINICIDIO = ['161G'];

    const CONDUCTORES_VEHICULOS = ['0321', '0320'];

    const SECUESTRO = ['1322', '1323', '1324', '1325', '1326', '1327', '2631', '2632', '2633', '1320', '1328', '1329'];

    const SECUESTRO_V2 = ['1324','1325','1326','2631','1320'];

    const TRATA_PERSONAS = ['0532'];

    const TURISMO_SEXUAL = ['0552', '0550'];

    const RESTO_DELITOS = [
        '1350', '1341', '182A', '182B', '182C', '182D', '182E', '182F', '182G', '182L', '182K', '182H', '182I', '182J', '181K', '181L', '181M', 
        '181N', '181O', '181P', '181Q', '181R', '181S', '181T', '181U', '181V', '181W', '181X', '181Y', '181Z', '1811', '1812', '1815', '1816', '1817', 
        '1818', '1819', '181A', '181B', '181C', '181D', '181E', '181F', '181G', '181H', '181I', '181J', '1814', '1622', '1633', '1628', '1629', '1631', 
        '1632', '1637', '1638', '1639', '1624', '161J', '1613', '161E', '1615', '1616', '161A', '161B', '2311', '1160', '1625', '1626', '1627', '1623', 
        '1820', '161K', '161L', '1618', '161C', '161D', '161H', '161I', '1671', '1612', '1614', '1619', '1617', '1884', '1894', '1885', '1883', '1881', 
        '1882', '1853', '1854', '1855', '2510', '1850', '1340', '1872', '1873', '1870', '1412', '1413', '1410', '1830', '1432', '1430', '1310', '0771', 
        '0772', '0770', '0921', '0922', '0923', '0920', '0924', '1835', '1841', '1842', '1840', '1370', '1372', '1140', '1150', '2111', '2112', '2113', 
        '2110', '2240', '0230', '1420', '1350', '0321', '0320', '0532', '1431', '161G', '0552', '0550', '2312', '1322', '1323', '1324', '1325', '1326', 
        '1327', '2631', '2632', '2633', '1320', '1328', '1329'
    ];

    const LESIONES_ARMA_FUEGO = ['1628'];

    const LESIONES_ARMA_BLANCA = ['1629'];

    const LESIONES_SIN_DATOS = ['1622', '1633', '1631', '1632', '1637', '1638', '1639', '1624'];

    const HOMICIDIO_CULPOSO_FUEGO = ['161A'];

    const HOMICIDIO_CULPOSO_BLANCA = ['161B'];

    const HOMICIDIO_CULPOSO_RESTO = ['161J', '1613', '1615', '1616', '161E'];

    const DELITOS = [
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
        '1160'];

    const AROBO_SIMPLE = ['1811'];
    
    const AROBO_VEHICULO = ['1814'];
    
    const AROBO_CHOFER_REPARTIDOR = ['1815'];
    
    const AROBO_CHOFER_AUTOBUS = ['1816'];
    
    const AROBO_A_VEHICULOS = ['1817', '181Y', '181M'];
    
    const AROBO_TRANSEUNTES = ['182A', '1818', '182B', '182L'];
    
    const AROBO_HABITACION = ['1819'];
    
    const AROBO_BANCOS = ['181A', '181S', '181W'];
    
    const AROBO_A_COMERCIOS = ['181B'];
    
    const AROBO_INDUSTRIA = ['181C'];
    
    const AROBO_ESCUELA = ['181D'];
    
    const AROBO_OFICINA = ['181E'];
    
    const AROBO_GASOLINERA = ['181F'];
    
    const AROBO_TALLER = ['181G'];
    
    const AROBO_MOTOCICLETA = ['181H'];
    
    const AROBO_DOCUMENTO = ['181I'];
    
    const AROBO_MAQUINARIA = ['182G', '181J'];
    
    const AROBO_CALIFICADO = [
        '182H', '182K', '182J', '182I', '181X', '181R', '181Q', '181P', '1812', '181N', 
        '181L', '181O', '181K', '2350', '181Z', '182E', '182D', '182C', '181U', '181V'
    ];

    const AROBO = ['1810'];
    
    const ABANCOS = ['181A'];
    
    const ACAJA_AHORRO = ['181S'];
    
    const ACASA_HABITACION = ['1819'];
    
    const ACOMERCIOS = ['181B'];

    const ACUENTAHAB_DENTRO_SUCURSAL = ['182H'];

    const ACUENTAHAB_TARJETA = ['182K'];

    const ACUENTAHAB_RETIRO_CAJERO = ['182J'];

    const ACUENTAHAB_RETIRO_VENTANILLA = ['182I'];

    const AESCUELAS = ['181D'];

    const AGASOLINERAS = ['181F'];

    const AINDUSTRIA = ['181C'];

    const ABANCARIA_ATM = ['181W'];

    const ARELIGIOSA = ['181X'];

    const AINTERIOR_VEHICULO = ['181Y'];

    const AOFICINAS = ['181E'];

    const APERSON_LUGAR_PRIVADO = ['182L'];

    const ATALLERES = ['181G'];

    const ATRANSEUNTE_VIA_PUBLICA = ['182A'];

    const ATRANSEUNTE_ABIERTO_PUBLICO = ['182B'];

    const ATRANSEUNTES = ['1818'];

    const APUBLICO_CAMION = ['181R'];

    const APUBLICO_COMBI = ['181Q'];

    const APUBLICO_TAXI = ['181P'];

    const AA_VEHICULOS = ['1817'];

    const ACALIFICADO = ['1812'];

    const ACHOFER_AUTOBUSES = ['1816'];

    const ACHOFER_REPARTIDOR = ['1815'];

    const AARTE_SACRO = ['181N'];

    const AAUTOPARTES = ['181M'];

    const ACOBRE = ['181L'];

    const ADOCUMENTOS = ['181I'];

    const AEMBARCACIONES = ['182F'];

    const AHIDROCARBUROS = ['181O'];

    const AMOTOCICLETA = ['181H'];

    const AREMOLQUE = ['181K'];

    const ATRACTOR = ['182G'];

    const AUSO = ['2350'];

    const ADE_VEHICULOS = ['1814'];

    const AVEHICULOS_MAQUINARIA = ['181J'];

    const ACARRETERA = ['181Z'];

    const ATRANSPORTE_INDIVIDUAL = ['182E'];

    const ATRANSPORTE_PUBLICO_COLECTIVO = ['182D'];

    const ATRANSPORTE_PUBLICO_INDIVIDUAL = ['182C'];

    const AASCENDENTES_Y_DESCENDENTES = ['181U'];

    const AENTRE_CONYUGES = ['181V'];

    const AEQUIPARADO = ['181T'];

    const AROBO_SIMPLE2 = ['1811'];

    const ROBOS_LIST = ['1810','181A','181S','1819','181B','182H','182K','182J','182I','181D','181F','181C','181W','181X','181Y','181E','182L','181G','182A','182B','1818','181R','181Q','181P','1817','1812','1816','1815','181N','181M','181L','181I','182F','181O','181H','181K','182G','2350','1814','181J','181Z','182E','182D','182C','181U','181V','181T','1811'];
}