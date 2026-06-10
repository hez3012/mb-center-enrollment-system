<?php

namespace App\Helpers;

class PhilippinesGeo
{
    private static array $data = [
        'National Capital Region (NCR)' => [
            'Metro Manila' => [
                'Caloocan City','Las Piñas City','Makati City','Malabon City',
                'Mandaluyong City','Manila','Marikina City','Muntinlupa City',
                'Navotas City','Parañaque City','Pasay City','Pasig City',
                'Pateros','Quezon City','San Juan City','Taguig City','Valenzuela City',
            ],
        ],
        'Region I - Ilocos Region' => [
            'Ilocos Norte'  => ['Laoag City','Batac City','Adams','Bacarra','Badoc','Bangui','Banna','Burgos','Carasi','Currimao','Dingras','Dumalneg','Marcos','Nueva Era','Pagudpud','Paoay','Pasuquin','Piddig','Pinili','San Nicolas','Sarrat','Solsona','Vintar'],
            'Ilocos Sur'    => ['Vigan City','Candon City','Alilem','Banayoyo','Bantay','Burgos','Cabugao','Caoayan','Cervantes','Galimuyod','Gregorio del Pilar','Lidlidda','Magsingal','Nagbukel','Narvacan','Quirino','Salcedo','San Emilio','San Esteban','San Ildefonso','San Juan','San Vicente','Santa','Santa Catalina','Santa Cruz','Santa Lucia','Santa Maria','Santiago','Santo Domingo','Sigay','Sinait','Sugpon','Suyo','Tagudin'],
            'La Union'      => ['San Fernando City','Agoo','Aringay','Bacnotan','Bagulin','Balaoan','Bangar','Bauang','Burgos','Caba','Luna','Naguilian','Pugo','Rosario','San Gabriel','San Juan','Santo Tomas','Santol','Sudipen','Tubao'],
            'Pangasinan'    => ['Dagupan City','San Carlos City','Urdaneta City','Alaminos City','Agno','Aguilar','Alcala','Anda','Asingan','Balungao','Bani','Basista','Bautista','Bayambang','Binalonan','Binmaley','Bolinao','Bugallon','Burgos','Calasiao','Dasol','Infanta','Labrador','Laoac','Lingayen','Mabini','Malasiqui','Manaoag','Mangaldan','Mangatarem','Mapandan','Natividad','Pozzorubio','Rosales','San Fabian','San Jacinto','San Manuel','San Nicolas','San Quintin','Santa Barbara','Santa Maria','Santo Tomas','Sison','Sual','Tayug','Umingan','Urbiztondo','Villasis'],
        ],
        'Region II - Cagayan Valley' => [
            'Batanes'       => ['Basco','Itbayat','Ivana','Mahatao','Sabtang','Uyugan'],
            'Cagayan'       => ['Tuguegarao City','Abulug','Alcala','Allacapan','Amulung','Aparri','Baggao','Ballesteros','Buguey','Calayan','Camalaniugan','Claveria','Enrile','Gattaran','Gonzaga','Iguig','Lal-lo','Lasam','Pamplona','Peñablanca','Piat','Rizal','Sanchez-Mira','Santa Ana','Santa Praxedes','Santa Teresita','Santo Niño','Solana','Tuao'],
            'Isabela'       => ['Ilagan City','Cauayan City','Santiago City','Alicia','Angadanan','Aurora','Benito Soliven','Burgos','Cabagan','Cabatuan','Cordon','Delfin Albano','Dinapigue','Divilacan','Echague','Gamu','Jones','Luna','Maconacon','Mallig','Naguilian','Palanan','Quezon','Quirino','Ramon','Reina Mercedes','Roxas','San Agustin','San Guillermo','San Isidro','San Manuel','San Mariano','San Mateo','San Pablo','Santa Maria','Santo Tomas','Tumauini'],
            'Nueva Vizcaya' => ['Bayombong','Solano','Alfonso Castañeda','Ambaguio','Aritao','Bagabag','Bambang','Diadi','Dupax del Norte','Dupax del Sur','Kasibu','Kayapa','Quezon','Santa Fe','Villaverde'],
            'Quirino'       => ['Cabarroguis','Aglipay','Diffun','Maddela','Nagtipunan','Saguday'],
        ],
        'Region III - Central Luzon' => [
            'Aurora'      => ['Baler','Casiguran','Dilasag','Dinalungan','Dingalan','Dipaculao','Maria Aurora','San Luis'],
            'Bataan'      => ['Balanga City','Abucay','Bagac','Dinalupihan','Hermosa','Limay','Mariveles','Morong','Orani','Orion','Pilar','Samal'],
            'Bulacan'     => ['Malolos City','Meycauayan City','San Jose del Monte City','Angat','Balagtas','Baliuag','Bocaue','Bulacan','Bustos','Calumpit','Doña Remedios Trinidad','Guiguinto','Hagonoy','Marilao','Norzagaray','Obando','Pandi','Paombong','Plaridel','Pulilan','San Ildefonso','San Miguel','San Rafael','Santa Maria'],
            'Nueva Ecija' => ['Palayan City','Cabanatuan City','Gapan City','Science City of Muñoz','Talavera','Aliaga','Bongabon','Cabiao','Carranglan','Cuyapo','Gabaldon','General Mamerto Natividad','General Tinio','Guimba','Jaen','Laur','Licab','Llanera','Lupao','Nampicuan','Pantabangan','Peñaranda','Quezon','Rizal','San Antonio','San Isidro','San Jose City','San Leonardo','Santa Rosa','Santo Domingo','Talugtug','Zaragoza'],
            'Pampanga'    => ['San Fernando City','Angeles City','Mabalacat City','Apalit','Arayat','Bacolor','Candaba','Floridablanca','Guagua','Lubao','Macabebe','Magalang','Masantol','Mexico','Minalin','Porac','San Luis','San Simon','Santa Ana','Santa Rita','Santo Tomas','Sasmuan'],
            'Tarlac'      => ['Tarlac City','Anao','Bamban','Camiling','Capas','Concepcion','Gerona','La Paz','Mayantoc','Moncada','Paniqui','Pura','Ramos','San Clemente','San Jose','San Manuel','Santa Ignacia','Victoria'],
            'Zambales'    => ['Olongapo City','Botolan','Cabangan','Candelaria','Castillejos','Iba','Masinloc','Olongapo','Palauig','San Antonio','San Felipe','San Marcelino','San Narciso','Santa Cruz','Subic'],
        ],
        'Region IV-A - CALABARZON' => [
            'Batangas' => ['Batangas City','Lipa City','Tanauan City','Agoncillo','Alitagtag','Balayan','Balete','Bauan','Calaca','Calatagan','Cuenca','Ibaan','Laurel','Lemery','Lian','Lobo','Mabini','Malvar','Mataas na Kahoy','Nasugbu','Padre Garcia','Rosario','San Jose','San Juan','San Luis','San Nicolas','San Pascual','Santa Teresita','Santo Tomas','Taysan','Tingloy','Tuy'],
            'Cavite'   => ['Bacoor City','Cavite City','Dasmariñas City','General Trias City','Imus City','Tagaytay City','Trece Martires City','Alfonso','Amadeo','Carmona','General Emilio Aguinaldo','General Mariano Alvarez','Indang','Kawit','Magallanes','Maragondon','Mendez','Naic','Noveleta','Rosario','Silang','Tanza','Ternate'],
            'Laguna'   => ['San Pablo City','Biñan City','Cabuyao City','Calamba City','San Pedro City','Santa Cruz','Alaminos','Bay','Calauan','Cavinti','Famy','Kalayaan','Liliw','Los Baños','Luisiana','Lumban','Mabitac','Magdalena','Majayjay','Nagcarlan','Paete','Pagsanjan','Pakil','Pangil','Pila','Rizal','Santa Maria','Santa Rosa','Siniloan','Victoria'],
            'Quezon'   => ['Lucena City','Tayabas City','Agdangan','Alabat','Atimonan','Buenavista','Burdeos','Calauag','Candelaria','Catanauan','Dolores','General Luna','General Nakar','Guinayangan','Gumaca','Infanta','Jomalig','Lopez','Lucban','Macalelon','Mauban','Mulanay','Padre Burgos','Pagbilao','Panukulan','Patnanungan','Perez','Pitogo','Plaridel','Polillo','Quezon','Real','Sampaloc','San Andres','San Antonio','San Francisco','San Narciso','Sariaya','Tagkawayan','Tiaong','Unisan'],
            'Rizal'    => ['Antipolo City','Angono','Baras','Binangonan','Cainta','Cardona','Jala-Jala','Morong','Pililla','Rodriguez','San Mateo','Tanay','Taytay','Teresa'],
        ],
        'Region IV-B - MIMAROPA' => [
            'Marinduque'         => ['Boac','Buenavista','Gasan','Mogpog','Santa Cruz','Torrijos'],
            'Occidental Mindoro' => ['Mamburao','Abra de Ilog','Calintaan','Looc','Magsaysay','Paluan','Rizal','Sablayan','San Jose','Santa Cruz'],
            'Oriental Mindoro'   => ['Calapan City','Baco','Bansud','Bongabong','Bulalacao','Gloria','Mansalay','Naujan','Pinamalayan','Pola','Puerto Galera','Roxas','San Teodoro','Socorro','Victoria'],
            'Palawan'            => ['Puerto Princesa City','Aborlan','Agutaya','Araceli','Balabac','Bataraza','Brooke\'s Point','Busuanga','Cagayancillo','Coron','Culion','Cuyo','El Nido','Espanola','Kalayaan','Linapacan','Magsaysay','Narra','Quezon','Rizal','Roxas','San Vicente','Sofronio Española','Taytay'],
            'Romblon'            => ['Romblon','Alcantara','Banton','Cajidiocan','Calatrava','Concepcion','Corcuera','Ferrol','Looc','Magdiwang','Odiongan','San Agustin','San Andres','San Fernando','San Jose','Santa Fe','Santa Maria'],
        ],
        'Region V - Bicol Region' => [
            'Albay'           => ['Legazpi City','Ligao City','Tabaco City','Bacacay','Camalig','Daraga','Guinobatan','Jovellar','Libon','Malilipot','Malinao','Manito','Oas','Pio Duran','Polangui','Rapu-Rapu','Santo Domingo','Tiwi'],
            'Camarines Norte' => ['Daet','Basud','Capalonga','Jose Panganiban','Labo','Mercedes','Paracale','San Lorenzo Ruiz','San Vicente','Santa Elena','Talisay','Vinzons'],
            'Camarines Sur'   => ['Naga City','Iriga City','Baao','Balatan','Bato','Bombon','Buhi','Bula','Cabusao','Calabanga','Camaligan','Canaman','Caramoan','Del Gallego','Gainza','Garchitorena','Goa','Lagonoy','Libmanan','Lupi','Magarao','Milaor','Minalabac','Nabua','Ocampo','Pamplona','Pasacao','Pili','Presentacion','Ragay','Sagñay','San Fernando','San Jose','Sipocot','Siruma','Tigaon','Tinambac'],
            'Catanduanes'     => ['Virac','Bagamanoc','Baras','Bato','Caramoran','Gigmoto','Pandan','Panganiban','San Andres','San Miguel','Viga'],
            'Masbate'         => ['Masbate City','Aroroy','Baleno','Balud','Batuan','Cataingan','Cawayan','Claveria','Dimasalang','Esperanza','Mandaon','Milagros','Mobo','Monreal','Palanas','Pio V. Corpuz','Placer','San Fernando','San Jacinto','San Pascual','Uson'],
            'Sorsogon'        => ['Sorsogon City','Barcelona','Bulan','Bulusan','Casiguran','Castilla','Donsol','Gubat','Irosin','Juban','Magallanes','Matnog','Pilar','Prieto Diaz','Santa Magdalena'],
        ],
        'Region VI - Western Visayas' => [
            'Aklan'             => ['Kalibo','Altavas','Balete','Banga','Batan','Buruanga','Ibajay','Lezo','Libacao','Madalag','Makato','Malay','Malinao','Nabas','New Washington','Numancia','Tangalan'],
            'Antique'           => ['San Jose de Buenavista','Anini-y','Barbaza','Belison','Bogong','Caluya','Culasi','Hamtic','Laua-an','Libertad','Pandan','Patnongon','San Gregorio','San Remigio','Sebaste','Sibalom','Tibiao','Tobias Fornier','Valderrama'],
            'Capiz'             => ['Roxas City','Cuartero','Dao','Dumalag','Dumarao','Ivisan','Jamindan','Mambusao','Panay','Panitan','Pilar','Pontevedra','President Roxas','Sapi-an','Sigma','Tapaz'],
            'Guimaras'          => ['Jordan','Buenavista','Nueva Valencia','San Lorenzo','Sibunag'],
            'Iloilo'            => ['Iloilo City','Passi City','Ajuy','Alimodian','Anilao','Badiangan','Balasan','Banate','Barotac Nuevo','Barotac Viejo','Batad','Bingawan','Cabatuan','Calinog','Carles','Concepcion','Dingle','Dueñas','Dumangas','Estancia','Guimbal','Igbaras','Janiuay','Lambunao','Leganes','Lemery','Leon','Maasin','Miagao','Mina','New Lucena','Oton','Pavia','Pototan','San Dionisio','San Enrique','San Joaquin','San Miguel','San Rafael','Santa Barbara','Sara','Tigbauan','Tubungan','Zarraga'],
            'Negros Occidental' => ['Bacolod City','Bago City','Cadiz City','Escalante City','Himamaylan City','Kabankalan City','La Carlota City','Sagay City','San Carlos City','Silay City','Sipalay City','Talisay City','Victorias City','Binalbagan','Calatrava','Candoni','Cauayan','Enrique B. Magalona','Hinigaran','Hinoba-an','Ilog','Isabela','La Castellana','Manapla','Moises Padilla','Murcia','Pontevedra','Pulupandan','Salvador Benedicto','San Enrique','Toboso','Valladolid'],
        ],
        'Region VII - Central Visayas' => [
            'Bohol'          => ['Tagbilaran City','Alburquerque','Alicia','Anda','Antequera','Baclayon','Balilihan','Batuan','Bien Unido','Bilar','Buenavista','Calape','Candijay','Carmen','Catigbian','Clarin','Corella','Cortes','Dagohoy','Danao','Dauis','Dimiao','Duero','Garcia Hernandez','Getafe','Guindulman','Inabanga','Jagna','Jetafe','Lila','Loay','Loboc','Loon','Mabini','Maribojoc','Panglao','Pilar','Sagbayan','San Isidro','San Miguel','Sevilla','Sierra Bullones','Sikatuna','Talibon','Trinidad','Tubigon','Ubay','Valencia'],
            'Cebu'           => ['Cebu City','Lapu-Lapu City','Mandaue City','Bogo City','Carcar City','Danao City','Naga City','Talisay City','Toledo City','Alcantara','Alcoy','Alegria','Aloguinsan','Argao','Asturias','Badian','Balamban','Bantayan','Barili','Borbon','Compostela','Consolacion','Cordova','Daanbantayan','Dalaguete','Dumanjug','Ginatilan','Liloan','Madridejos','Malabuyoc','Medellin','Minglanilla','Moalboal','Oslob','Pilar','Pinamungahan','Poro','Ronda','Samboan','San Fernando','San Francisco','San Remigio','Santa Fe','Santander','Sibonga','Sogod','Tabogon','Tabuelan','Tudela'],
            'Negros Oriental' => ['Dumaguete City','Bais City','Bayawan City','Canlaon City','Guihulngan City','Tanjay City','Amlan','Ayungon','Bacong','Basay','Bindoy','Dauin','Jimalalud','La Libertad','Mabinay','Manjuyod','Pamplona','San Jose','Santa Catalina','Siaton','Sibulan','Tayasan','Valencia','Vallehermoso','Zamboanguita'],
            'Siquijor'       => ['Siquijor','Enrique Villanueva','Larena','Lazi','Maria','San Juan'],
        ],
        'Region VIII - Eastern Visayas' => [
            'Biliran'        => ['Naval','Almeria','Biliran','Cabucgayan','Caibiran','Culaba','Kawayan','Maripipi'],
            'Eastern Samar'  => ['Borongan City','Arteche','Balangiga','Balangkayan','Can-avid','Dolores','General MacArthur','Giporlos','Guiuan','Hernani','Jipapad','Lawaan','Llorente','Maslog','Maydolong','Mercedes','Oras','Quinapondan','Salcedo','San Julian','San Policarpo','Sulat','Taft'],
            'Leyte'          => ['Tacloban City','Baybay City','Ormoc City','Abuyog','Alangalang','Albuera','Babatngon','Bato','Barugo','Burauen','Calubian','Capoocan','Carigara','Dagami','Dulac','Dulag','Hilongos','Hindang','Inopacan','Isabel','Jaro','Javier','Julita','Kananga','La Paz','Leyte','Liloan','Mahaplag','Matag-ob','Matalom','Mayorga','MacArthur','Merida','Palo','Palompon','Pastrana','San Isidro','San Miguel','Santa Fe','Tabango','Tabontabon','Tanauan','Tolosa','Tunga','Villaba'],
            'Northern Samar' => ['Catarman','Allen','Biri','Bobon','Capul','Catubig','Gamay','Laoang','Lapinig','Las Navas','Lavezares','Lope de Vega','Mapanas','Mondragon','Palapag','Pambujan','Rosario','San Antonio','San Isidro','San Jose','San Roque','San Vicente','Silvino Lobos','Victoria'],
            'Samar'          => ['Catbalogan City','Calbayog City','Almagro','Basey','Calbiga','Daram','Gandara','Hinabangan','Jiabong','Marabut','Matuguinao','Motiong','Pagsanghan','Paranas','Pinabacdao','San Jorge','San Jose de Buan','San Sebastian','Santa Margarita','Santa Rita','Santo Niño','Tagapul-an','Talalora','Tarangnan','Villareal','Zumarraga'],
            'Southern Leyte' => ['Maasin City','Anahawan','Bontoc','Hinunangan','Hinundayan','Libagon','Liloan','Limasawa','Macrohon','Malitbog','Padre Burgos','Pintuyan','Saint Bernard','San Francisco','San Juan','San Ricardo','Silago','Sogod','Tomas Oppus'],
        ],
        'Region IX - Zamboanga Peninsula' => [
            'Zamboanga del Norte' => ['Dipolog City','Dapitan City','Godod','Gutalac','Jose Dalman','Kalawit','Katipunan','La Libertad','Labason','Leon B. Postigo','Liloy','Manukan','Mutia','Piñan','Polanco','President Manuel A. Roxas','Rizal','Salug','San Jose','San Miguel','Sergio Osmeña Sr.','Siayan','Sibuco','Sibutad','Sindangan','Siocon','Sirawai','Tampilisan'],
            'Zamboanga del Sur'   => ['Pagadian City','Zamboanga City','Aurora','Bayog','Dimataling','Dinas','Dumalinao','Dumingag','Guipos','Josefina','Kumalarang','Labangan','Lapuyan','Lakewood','Mahayag','Margosatubig','Midsalip','Molave','Ramon Magsaysay','San Miguel','San Pablo','Tabina','Tambulig','Tukuran','Vincenzo A. Sagun'],
            'Zamboanga Sibugay'   => ['Ipil','Alicia','Buug','Diplahan','Imelda','Kabasalan','Mabuhay','Malangas','Naga','Olutanga','Payao','Roseller Lim','Siay','Talusan','Titay','Tungawan'],
        ],
        'Region X - Northern Mindanao' => [
            'Bukidnon'          => ['Malaybalay City','Valencia City','Baungon','Cabanglasan','Damulog','Dangcagan','Don Carlos','Impasugong','Kadingilan','Kalilangan','Kibawe','Kitaotao','Lantapan','Libona','Malitbog','Manolo Fortich','Maramag','Pangantucan','Quezon','San Fernando','Sumilao','Talakag'],
            'Camiguin'          => ['Mambajao','Catarman','Guinsiliban','Mahinog','Sagay'],
            'Lanao del Norte'   => ['Iligan City','Bacolod','Baloi','Baroy','Kapatagan','Kauswagan','Kolambugan','Lala','Linamon','Magsaysay','Maigo','Munai','Nunungan','Pantao Ragat','Pantar','Poona Piagapo','Salvador','Sapad','Sultan Naga Dimaporo','Tagoloan','Tangcal','Tubod'],
            'Misamis Occidental'=> ['Oroquieta City','Ozamiz City','Tangub City','Aloran','Baliangao','Bonifacio','Calamba','Clarin','Concepcion','Don Victoriano Chiongbian','Jimenez','Lopez Jaena','Plaridel','Panaon','Sapang Dalaga','Sinacaban','Tudela'],
            'Misamis Oriental'  => ['Cagayan de Oro City','El Salvador City','Gingoog City','Alubijid','Balingasag','Balingoan','Binuangan','Claveria','Gitagum','Initao','Jasaan','Kinoguitan','Lagonglong','Laguindingan','Libertad','Lugait','Magsaysay','Manticao','Medina','Naawan','Opol','Salay','Sugbongcogon','Tagoloan','Talisayan','Villanueva'],
        ],
        'Region XI - Davao Region' => [
            'Davao de Oro'     => ['Nabunturan','Compostela','Laak','Mabini','Maco','Maragusan','Mawab','Monkayo','Montevista','New Bataan','Pantukan'],
            'Davao del Norte'  => ['Tagum City','Island Garden City of Samal','Asuncion','Braulio E. Dujali','Carmen','Kapalong','New Corella','San Isidro','Santo Tomas','Talaingod'],
            'Davao del Sur'    => ['Davao City','Digos City','Bansalan','Don Marcelino','Hagonoy','Jose Abad Santos','Kiblawan','Magsaysay','Malalag','Matanao','Padada','Santa Cruz','Sulop'],
            'Davao Occidental' => ['Malita','Don Marcelino','Jose Abad Santos','Santa Maria','Sarangani'],
            'Davao Oriental'   => ['Mati City','Baganga','Banaybanay','Boston','Caraga','Cateel','Governor Generoso','Lupon','Manay','San Isidro','Tarragona'],
        ],
        'Region XII - SOCCSKSARGEN' => [
            'Cotabato'      => ['Kidapawan City','Alamada','Aleosan','Antipas','Arakan','Banisilan','Carmen','Kabacan','Libungan','Magpet','Makilala','Matalam','Midsayap','Milan','Mlang','Pigcawayan','Pikit','President Roxas'],
            'Sarangani'     => ['Alabel','Glan','Kiamba','Maitum','Malapatan','Malungon','Maasim'],
            'South Cotabato'=> ['Koronadal City','Banga','Lake Sebu','Norala','Polomolok','Santo Niño','Surallah','T\'boli','Tampakan','Tantangan','Tupi'],
            'Sultan Kudarat'=> ['Isulan','Tacurong City','Bagumbayan','Columbio','Esperanza','Kalamansig','Lebak','Lutayan','Lambayong','Palimbang','President Quirino','Senator Ninoy Aquino'],
        ],
        'Region XIII - Caraga' => [
            'Agusan del Norte' => ['Butuan City','Cabadbaran City','Buenavista','Carmen','Jabonga','Kitcharao','Las Nieves','Magallanes','Nasipit','Remedios T. Romualdez','Santiago','Tubay'],
            'Agusan del Sur'   => ['Prosperidad','Bayugan City','Bunawan','Esperanza','La Paz','Loreto','Rosario','San Francisco','San Luis','Santa Josefa','Sibagat','Talacogon','Trento','Veruela'],
            'Dinagat Islands'  => ['San Jose','Basilisa','Cagdianao','Dinagat','Libjo','Loreto','Tubajon'],
            'Surigao del Norte'=> ['Surigao City','Alegria','Bacuag','Burgos','Claver','Dapa','Del Carmen','General Luna','Gigaquit','Mainit','Malimono','Pilar','Placer','San Benito','San Francisco','San Isidro','Santa Monica','Sison','Socorro','Tagana-an','Tubod'],
            'Surigao del Sur'  => ['Tandag City','Bislig City','Barobo','Bayabas','Cagwait','Cantilan','Carmen','Carrascal','Cortes','Hinatuan','Lanuza','Lianga','Lingig','Madrid','Marihatag','San Agustin','San Miguel','Tagbina','Tago'],
        ],
        'CAR - Cordillera Administrative Region' => [
            'Abra'              => ['Bangued','Boliney','Bucay','Bucloc','Daguioman','Danglas','Dolores','La Paz','Lacub','Lagangilang','Lagayan','Langiden','Licuan-Baay','Luba','Malibcong','Manabo','Peñarrubia','Pidigan','Pilar','Sallapadan','San Isidro','San Juan','San Quintin','Tayum','Tineg','Tubo','Villaviciosa'],
            'Apayao'            => ['Calanasan','Conner','Flora','Kabugao','Luna','Pudtol','Santa Marcela'],
            'Benguet'           => ['La Trinidad','Baguio City','Atok','Bakun','Bokod','Buguias','Itogon','Kabayan','Kapangan','Kibungan','Mankayan','Sablan','Tuba','Tublay'],
            'Ifugao'            => ['Lagawe','Aguinaldo','Alfonso Lista','Asipulo','Banaue','Hingyon','Hungduan','Kiangan','Lamut','Mayoyao','Tinoc'],
            'Kalinga'           => ['Tabuk City','Balbalan','Lubuagan','Pasil','Pinukpuk','Rizal','Tanudan','Tinglayan'],
            'Mountain Province' => ['Bontoc','Bauko','Besao','Natonin','Paracelis','Sabangan','Sadanga','Sagada','Tadian'],
        ],
        'BARMM - Bangsamoro Autonomous Region' => [
            'Basilan'               => ['Isabela City','Akbar','Al-Barka','Hadji Muhtamad','Lamitan City','Lantawan','Maluso','Sumisip','Tipo-Tipo','Tuburan','Ungkaya Pukan'],
            'Lanao del Sur'         => ['Marawi City','Balabagan','Bacolod-Kalawi','Balindong','Bangon','Barira','Bayang','Binidayan','Bumbaran','Butig','Calanogas','Capanori','Ditsaan-Ramain','Ganassi','Kapai','Kapatagan','Lumba-Bayabao','Lumbaca-Unayan','Lumbatan','Lumbayanague','Madalum','Madamba','Maguing','Malabang','Marantao','Marogong','Masiu','Molundo','Mulondo','Pagayawan','Piagapo','Picong','Poona Bayabao','Pualas','Saguiaran','Sultan Dumalondong','Tagoloan II','Tamparan','Taraka','Tubaran','Tugaya','Wao'],
            'Maguindanao del Norte' => ['Cotabato City','Barira','Buldon','Datu Blah T. Sinsuat','Datu Odin Sinsuat','Kabuntalan','Matanog','Parang','Sultan Kudarat','Sultan Mastura','Upi'],
            'Maguindanao del Sur'   => ['Buluan','Datu Abdullah Sangki','Datu Anggal Midtimbang','Datu Hoffer Ampatuan','Datu Montawal','Datu Piang','Datu Salibo','Datu Saudi-Ampatuan','Datu Unsay','Gen. Salipada K. Pendatun','Guindulungan','Mamasapano','Mangudadatu','Pagalungan','Paglat','Pandag','Rajah Buayan','Shariff Aguak','South Upi','Sultan sa Barongis','Talayan','Talitay'],
            'Sulu'                  => ['Jolo','Banguingui','Hadji Panglima Tahil','Indanan','Kalingalan Caluang','Lugus','Luuk','Maimbung','Old Panamao','Omar','Pandami','Panglima Estino','Pangutaran','Parang','Pata','Patikul','Siasi','Talipao','Tapul','Tongkil'],
            'Tawi-Tawi'             => ['Bongao','Languyan','Mapun','Panglima Sugala','Sapa-Sapa','Sibutu','Simunul','Sitangkai','South Ubian','Tandubas','Turtle Islands'],
        ],
    ];

    public function getRegions(): array
    {
        return array_keys(self::$data);
    }

    public function getProvinces(string $region = ''): array
    {
        if (!$region || !isset(self::$data[$region])) {
            return [];
        }
        return array_keys(self::$data[$region]);
    }

    public function getCities(string $province = ''): array
    {
        if (!$province) {
            return [];
        }
        foreach (self::$data as $regionData) {
            if (isset($regionData[$province])) {
                return $regionData[$province];
            }
        }
        return [];
    }
}