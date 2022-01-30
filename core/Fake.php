<?php

class Fake{
    /**
     * Arreglo de distintos nombres.
     * 
     * @access private
     * @var array $name contiene más de 5 nombres con cada letra del alfabeto.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    private static $name = [
        'Ambar', 'Aneuris', 'Angel', 'Anthony', 'Arturo',
        'Bruno', 'Bladimir', 'Bernardo', 'Brayan', 'Bartolo',
        'Cándido', 'Catherine', 'Cintia', 'Camila', 'Camilo',
        'Daniel', 'Dalmacio', 'Damián', 'Darío', 'David',
        'Eduardo', 'Edwin', 'Efraín', 'Eleazar', 'Edgar',
        'Fabián', 'Fabio', 'Federico', 'Felipe', 'Fabio',
        'Gabriela', 'Genesis', 'Gastón', 'Gabino', 'Gaspar',
        'Héctor', 'Hércules', 'Harry', 'Henrietta', 'Harold',
        'Iara', 'Idalia', 'Inmaculada', 'Irma', 'Isabela',
        'Jack','Jeremaia','Joel','Joshua','Jochua',
        'Keila','Kelly','Kenia','Kiara','Kim',
        'Lincols','Labán','Ladislao','Larry','Lamberto',
        'Matteo','Mathias','Matusalén','Mahalaleel','Macario',
        'Nahuel','Nahum','Napoleón','Narciso','Narsés','Neizan','Natanael','Nazareno',
        'Omaira','Onilda','Oneida','Opal', 'Olivia',
        'Paulo','Pablo','Paciano','Pacífico','Paolo','Pacomio','Pancho','Pancracio',
        'Quintiliano','Quintín','Quinto','Quirico','Quirino','Quique','Quetzal',
        'Renato','Reimon','Remus','Ren','Royer','Robert','Rolexi','Ronel',
        'Sabina','Salime','Sabrina','Sacramento','Sagrario','Salomé','Samanta','Sandra',
        'Timothy','Toby','Tadeo','TaeHyung','Taemin','Tancredo','Taro','Tarsicio',
        'Ubaldo','Ulises','Ulrico','Urbano','Urías','Uriel','Ursino','Urso',
        'Valentín','Valentiniano','Valeriano','Valerio','Vasco','Velasco','Venancio','Ventura',
        'WENDY', 'WALID', 'WILLIAM', 'WALDO', 
        'Xuxa','Ximena','Xóchitl','Xitlal','Xiomara','Xenia','Xunaxi','Xilene',
        'Yanice','Yanina','Yennifer','Yolanda','Yolima',
        'Zabulón','Zacarías','Zacharias','Zahír','Zaqueo','Zacher','Zeke','Zenón'
    ];

    /**
     * Arreglo de distintos apellidos.
     * 
     * @access private
     * @var array $last_name contiene más de 5 apellido con cada letra del alfabeto.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    private static $last_name = [
        'Aguilar','Alonso','Álvarez','Arias','Benítez',
        'Blanco','Blesa','Bravo','Caballero','Cabrera','Calvo',
        'Cambil','Campos','Cano','Carmona','Carrasco',
        'Castillo','Castro','Cortés','Crespo','Cruz',
        'Delgado','Díaz','Díez','Domínguez','Durán',
        'Esteban','Fernández','Ferrer','Flores','Fuentes',
        'Gallardo','Gallego','García','Garrido','Gil',
        'Giménez','Gómez','González','Guerrero','Gutiérrez',
        'Hernández','Herrera','Herrero','Hidalgo','Ibáñez',
        'Iglesias','Jiménez','León','López','Lorenzo',
        'Lozano','Marín','Márquez','Martín','Martínez',
        'Medina','Méndez','Molina','Montero','Montoro',
        'Mora','Morales','Moreno','Moya','Muñoz',
        'Navarro','Nieto','Núñez','Ortega','Ortiz',
        'Parra','Pascual','Pastor','Peña','Pérez',
        'Prieto','Ramírez','Ramos','Rey','Reyes',
        'Rodríguez','Román','Romero','Rubio','Ruiz',
        'Sáez','Sánchez','Santana','Santiago','Santos',
        'Sanz','Serrano','Soler','Soto','Suárez',
        'Torres','Vargas','Vázquez','Vega','Velasco',
        'Vicente','Vidal'
    ];

    /**
     * Arreglo de distintos países.
     * 
     * @access private
     * @var array $country contiene diversos países.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    private static $country = [
        'Argentina', 'Australia', 'Rusia', 'España', 'Venezuela', 
        'República Dominicana', 'Mexico'
    ];

    /**
     * Matriz de distintas ciudades.
     * 
     * @access private
     * @var array $city contiene distintas ciudades.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    private static $city = [
        [
            'Buenos Aires', 'Córdoba', 'Rosario', 
            'Mar del Plata', 'San Miguel de Tucumán', 'Santa Fe', 
            'Corrientes', 'Bahía Blanca', 'Resistencia',
            'Posadas', 'Quilmes'
        ], [
            'SIDNEY', 'BRISBANE', 'MELBOURNE', 'GOLD COAST', 'BYRON BAY',
            'PERTH', 'ADELAIDE', 'CANBERRA', 'HOBART'
        ], [
            'Moscú', 'San Petersburgo', 'Novosibirsk', 'Ekaterimburgo', 'Nizhni Nóvgorod',
            'Samara', 'Omsk', 'Kazán', 'Cheliábinsk', 'Rostov del Don', 'Ufá', 'Volgogrado',
            'Perm', 'Krasnoyarsk', 'Vorónezh', 'Sarátov', 'Krasnodar', 'Tolyatti'
        ], [
            'Alcalá de Henares', 'Ávila', 'Baeza (Jaén)', 'Barcelona', 'Bilbao',
            'Burgos', 'Cáceres', 'Cartagena (Murcia)', 'Córdoba', 'Cuenca', 'Granada',
            'Ibiza', 'León', 'Málaga', 'Mérida (Badajoz)', 'Madrid', 'Oviedo (Asturias)'
        ], [
            'Caracas', 'Maracaibo', 'Valencia', 'Barquisimeto', 'Maracay', 'Ciudad Guayana', 
            'Barcelona', 'San Cristóbal', 'Maturín', 'Barinas', 'Ciudad Bolívar', 'Cumaná',
            'Mérida', 'Valera', 'Cabimas'
        ], [
            'Santo Domingo', 'Santiago', 'San Cristobal', 'Boca Chica', 'Samana', 'La Vega', 
            'San Juan de la Maguana', 'La Romana', 'Santo Domingo Este', 'Santo Domingo Oeste', 
            'Santo Domingo Norte', 'Mao', 'San Isidro', 'Cotuí', 'Haina', 'Barahona'
        ], [
            'Ciudad de México', 'Tijuana', 'San Cristóbal Ecatepec', 'León', 'Puebla de Zaragoza',
            'Ciudad Juárez', 'Guadalajara', 'Zapopan', 'Monterrey', 'Nezahualcóyotl', 'Chihuahua',
            'Mérida', 'Cancún', 'Saltillo', 'Aguascalientes', 'Hermosillo', 'Mexicali'
        ]
    ];

    /**
     * Arreglo de distintas extensiones teléfonicas.
     * 
     * @access private
     * @var array $phone contiene extensiones teléfonicas.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    private static $phone = ['829', '809', '849'];

    /**
     * Arreglo de distintos correos.
     * 
     * @access private
     * @var array $email contiene nombres de dominios.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    private static $email = [
        '@hotmail.com', '@gmail.com', '@example.com', 
        '@yahoo.com', '@outlook.com', '@tutanota.com',
        '@free.com', '@hot.com', '@sugar.com'
    ];

    /**
     * Arreglo de distintos colores.
     * 
     * @access private
     * @var array $color contiene distintos colores.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    private static $color = [
        'alizarin','amaranth','amber','amethyst','apricot','aqua','aquamarine','asparagus',
        'auburn','azure','beige','bistre','black','blue','blue-green','blue-violet','bondi-blue',
        'brass','bronze','brown','buff','burgundy','camouflage-green','caput-mortuum','cardinal',
        'carmine','carrot-orange','celadon','cerise','cerulean','champagne','charcoal','chartreuse',
        'cherry-blossom-pink','chestnut','chocolate','cinnabar','cinnamon','cobalt','copper',
        'coral','corn','cornflower','cream','crimson','cyan','dandelion','denim','ecru','emerald',
        'eggplant','falu-red','fern-green','firebrick','flax','forest-green','french-rose','fuchsia',
        'gamboge','gold','goldenrod','green','grey','han-purple','harlequin','heliotrope',
        'hollywood-cerise','indigo','ivory','jade','kelly-green','khaki','lavender','lawn-green','lemon',
        'lemon-chiffon','lilac','lime','lime-green','linen','magenta','magnolia','malachite','maroon',
        'mauve','midnight-blue','mint-green','misty-rose','moss-green','mustard','myrtle','navajo-white',
        'navy-blue','ochre','office-green','olive','olivine','orange','orchid','papaya-whip','peach',
        'pear','periwinkle','persimmon','pine-green','pink','platinum','plum','powder-blue','puce',
        'prussian-blue','psychedelic-purple','pumpkin','purple','quartz-grey','raw-umber','razzmatazz',
        'red','robin-egg-blue','rose','royal-blue','royal-purple','ruby','russet','rust','safety-orange',
        'saffron','salmon','sandy-brown','sangria','sapphire','scarlet','school-bus-yellow','sea-green',
        'seashell','sepia','shamrock-green','shocking-pink','silver','sky-blue','slate-grey','smalt',
        'spring-bud','spring-green','steel-blue','tan','tangerine','taupe','teal','tenné-(tawny)',
        'terra-cotta','thistle','titanium-white','tomato','turquoise','tyrian-purple','ultramarine',
        'van-dyke-brown','vermilion','violet','viridian','wheat','white','wisteria','yellow','zucchini'
    ];

    /**
     * Genera numeros aleatorios.
     * 
     * @access private
     * @param int $cant recive la cantidad de números a generar.
     * @return string retorna los números genrados y concatenados.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    private static function generateNumbers(int $cant):string{
        $numbers = '';
        for($i=0; $i < $cant; $i++){
            $numbers .= rand(0, 9);
        }
        return $numbers;
    }

    /**
     * Genera un nombre aleatorio.
     * 
     * @access private
     * @return string retorna un nombre.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public static function name():string{
        return self::$name[rand(0, count(self::$name)-1)];
    }

    /**
     * Genera un apellido aleatorio.
     * 
     * @access private
     * @return string retorna un apellido.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public static function lastName():string{
        return self::$last_name[rand(0, count(self::$last_name)-1)];
    }

    /**
     * Genera un país aleatorio.
     * 
     * @access private
     * @return string retorna un país.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public static function country():string{
        $pos = rand(0, count(self::$country)-1);
        $country = self::$country[$pos];
        $country .= ', ' . self::$city[$pos][rand(0, count(self::$city[$pos])-1)];
        return $country;
    }

    /**
     * Genera un teléfono aleatorio.
     * 
     * @access private
     * @return string retorna un teléfono.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public static function phone():string{
        return '(' . self::$phone[rand(0, count(self::$phone)-1)] . ') ' . self::generateNumbers(3) . '-' . self::generateNumbers(4);
    }

    /**
     * Genera un correo aleatorio.
     * 
     * @access private
     * @return string retorna un correo.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public static function email():string{
        return self::username() . self::$email[rand(0, count(self::$email)-1)];
    }

    /**
     * Genera un nombre de usuario aleatorio.
     * 
     * @access private
     * @return string retorna un nombre de usuario.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public static function username():string{
        return strtolower(self::name()) . self::generateNumbers(rand(1, 9));
    }

    /**
     * Genera una cédula aleatorio.
     * 
     * @access private
     * @return string retorna una cédula.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public static function cardID():string{
        return self::generateNumbers(3) . '-' . self::generateNumbers(7) . '-' . self::generateNumbers(1);
    }

    /**
     * Genera un color aleatorio.
     * 
     * @access private
     * @return string retorna un color.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public static function color():string{
        return self::$color[rand(0, count(self::$color)-1)];
    }

    /**
     * Genera una ciudad aleatorio.
     * 
     * @access private
     * @return string retorna una ciudad.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public static function city():string{
        $pos = rand(0, count(self::$city)-1);
        return self::$city[$pos][
            rand(0, count(self::$city[$pos])-1)
        ];
    }

    /**
     * Genera una fecha de cumpleaños aleatorio.
     * 
     * @access private
     * @return string retorna una fecha de cumpleaños.
     * @author Rafael Minaya
     * @copyright R.M.B.
     * @version 1.0
     */
    public static function birthday(int $year_from, int $year_to):string{
        $year = rand($year_from, $year_to);
        $num_month = rand(1, 12);
        $day = date('t', strtotime("$year-$num_month-1"));
        $day = rand(1, $day);
        return date('D d M Y', strtotime("$year-$num_month-$day"));
    }
}