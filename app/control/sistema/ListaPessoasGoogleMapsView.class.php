<?php

use Adianti\Control\TPage;

class ListaPessoasGoogleMapsView extends TPage
{
    public function __construct()
    {
        parent::__construct();

        // add api google: https://maps.google.com/maps/api/js?sensor=false&callback=initialize

        // TPage::include_js('https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&callback=initialize');
        TPage::include_js('https://maps.google.com/maps/api/js?sensor=false&libraries=places&key=AIzaSyCvlHIxGDuqD4hbZP2hQ0ojfelVlQT-u1s&callback=initialize');

        // create the HTML Renderer
        $this->html = new THtmlRenderer('app/resources/pessoas_google_maps.html');
        
        $pessoaList = $this->pessoasDummy();
        $realdata = $this->getPessoas();
        
        $replace_detail = [];

        
       
        foreach($realdata as $key => $pessoa){
            
           if(!empty($pessoa['localizacao'])){

               $replace_detail[] = array(
                   
                   'nome' => $pessoa['nome'],
                   'endereco' => $pessoa['endereco'],
                   'localizacao' => json_decode($pessoa['localizacao'], true),
                   
                );
                
            }
            
        }
 
       $replace = array();
        
        $replace['peoples'] = json_encode($replace_detail);

        // replace the main section variables
        $this->html->enableSection('main', $replace);

        parent::add($this->html);

    }

    private function getPessoas(){
        try 
        {
            TTransaction::open('unit_a'); // open transaction
            
            // query criteria
            $criteria = new TCriteria; 
            // $criteria->add(new TFilter('gender', '=', 'F')); 
            // $criteria->add(new TFilter('status', '=', 'M')); 
            
            // load using repository
            $repository = new TRepository('SisPessoas'); 
            $cadastros = $repository->load($criteria); 

            // var_dump($cadastros);
            
                $array = [];
            foreach ($cadastros as $cadastro) 
            { 
                $array[] =  $cadastro->toArray();
// var_dump($array);
            }
            
            TTransaction::close(); // close transaction

            return $array;
        } 
        catch (Exception $e) 
        { 
            new TMessage('error', $e->getMessage()); 
        } 
    }
    public function pessoasDummy(){

        $pessoas = '
        [
            {
                "nome": "Ryan Lucca Duarte",
                "idade": 30,
                "cpf": "96897330442",
                "rg": "409943113",
                "data_nasc": "08\/05\/1991",
                "sexo": "Masculino",
                "signo": "Touro",
                "mae": "Letícia Maya",
                "pai": "Geraldo Luiz Benício Duarte",
                "email": "ryanluccaduarte..ryanluccaduarte@ceuazul.ind.br",
                "senha": "o1anePjMm5",
                "cep": "67124160",
                "endereco": "Passagem Vinte e Um de Abril",
                "numero": 915,
                "bairro": "Icuí-Laranjeira",
                "cidade": "Ananindeua",
                "estado": "PA",
                "telefone_fixo": "9135701165",
                "celular": "91985955089",
                "altura": "1,60",
                "peso": 87,
                "tipo_sanguineo": "AB+",
                "cor": "vermelho"
            },
            {
                "nome": "Camila Priscila Elza Assunção",
                "idade": 30,
                "cpf": "34584034427",
                "rg": "462861624",
                "data_nasc": "13\/11\/1991",
                "sexo": "Feminino",
                "signo": "Escorpião",
                "mae": "Lívia Larissa Simone",
                "pai": "André Fábio Assunção",
                "email": "camilapriscilaelzaassuncao_@tvglobo.com.br",
                "senha": "lBX5f5w78J",
                "cep": "66823265",
                "endereco": "Passagem Deus Proverá",
                "numero": 780,
                "bairro": "Coqueiro",
                "cidade": "Belém",
                "estado": "PA",
                "telefone_fixo": "9139293452",
                "celular": "91999232812",
                "altura": "1,83",
                "peso": 78,
                "tipo_sanguineo": "AB+",
                "cor": "roxo"
            },
            {
                "nome": "Ryan Juan Nathan Almeida",
                "idade": 30,
                "cpf": "70063381400",
                "rg": "291217813",
                "data_nasc": "23\/12\/1991",
                "sexo": "Masculino",
                "signo": "Capricórnio",
                "mae": "Mirella Bianca",
                "pai": "Francisco Bruno Almeida",
                "email": "ryanjuannathanalmeida__ryanjuannathanalmeida@danielvasconcelos.com.br",
                "senha": "XOEEUGtodn",
                "cep": "68555113",
                "endereco": "Rua das Castanheiras",
                "numero": 542,
                "bairro": "Centro",
                "cidade": "Xinguara",
                "estado": "PA",
                "telefone_fixo": "9438944936",
                "celular": "94983008247",
                "altura": "1,92",
                "peso": 57,
                "tipo_sanguineo": "AB+",
                "cor": "preto"
            },
            {
                "nome": "Cauê Yuri da Mata",
                "idade": 30,
                "cpf": "04735828486",
                "rg": "200092054",
                "data_nasc": "27\/09\/1991",
                "sexo": "Masculino",
                "signo": "Libra",
                "mae": "Andrea Isadora",
                "pai": "Fábio Arthur da Mata",
                "email": "caueyuridamata__caueyuridamata@jammer.com.br",
                "senha": "YF0djmzIaE",
                "cep": "66913190",
                "endereco": "Rua Variante do Murubira",
                "numero": 544,
                "bairro": "Chapéu Virado (Mosqueiro)",
                "cidade": "Belém",
                "estado": "PA",
                "telefone_fixo": "9126810466",
                "celular": "91986490724",
                "altura": "1,93",
                "peso": 91,
                "tipo_sanguineo": "A-",
                "cor": "vermelho"
            },
            {
                "nome": "Márcio Pietro Aragão",
                "idade": 30,
                "cpf": "01866763440",
                "rg": "121177105",
                "data_nasc": "02\/01\/1991",
                "sexo": "Masculino",
                "signo": "Capricórnio",
                "mae": "Marlene Emanuelly",
                "pai": "Pedro Henrique Bruno Aragão",
                "email": "marciopietroaragao..marciopietroaragao@helpvale.com.br",
                "senha": "r8WsILUt8A",
                "cep": "67105155",
                "endereco": "Quadra Seis",
                "numero": 242,
                "bairro": "Marituba",
                "cidade": "Ananindeua",
                "estado": "PA",
                "telefone_fixo": "9135750800",
                "celular": "91997108348",
                "altura": "1,80",
                "peso": 71,
                "tipo_sanguineo": "O+",
                "cor": "vermelho"
            },
            {
                "nome": "Eloá Carla Emily Pires",
                "idade": 30,
                "cpf": "06326161487",
                "rg": "113050914",
                "data_nasc": "26\/06\/1991",
                "sexo": "Feminino",
                "signo": "Câncer",
                "mae": "Maya Vitória Isadora",
                "pai": "Manuel Benjamin Juan Pires",
                "email": "eloacarlaemilypires-89@pubdesign.com.br",
                "senha": "sg4xJODybo",
                "cep": "68744280",
                "endereco": "Alameda das Samambaias",
                "numero": 462,
                "bairro": "São José",
                "cidade": "Castanhal",
                "estado": "PA",
                "telefone_fixo": "9126539530",
                "celular": "91995785772",
                "altura": "1,85",
                "peso": 75,
                "tipo_sanguineo": "A+",
                "cor": "vermelho"
            },
            {
                "nome": "Geraldo Luiz da Cruz",
                "idade": 30,
                "cpf": "72620487404",
                "rg": "180634951",
                "data_nasc": "03\/11\/1991",
                "sexo": "Masculino",
                "signo": "Escorpião",
                "mae": "Alana Marlene Ana",
                "pai": "Vicente Bruno da Cruz",
                "email": "geraldoluizdacruz_@prositeweb.com.br",
                "senha": "SEjDSORTrH",
                "cep": "66025008",
                "endereco": "Rua Fernando Guilhon",
                "numero": 752,
                "bairro": "Jurunas",
                "cidade": "Belém",
                "estado": "PA",
                "telefone_fixo": "9136823669",
                "celular": "91992960985",
                "altura": "1,68",
                "peso": 72,
                "tipo_sanguineo": "AB+",
                "cor": "azul"
            },
            {
                "nome": "Luiza Sandra Ester Figueiredo",
                "idade": 30,
                "cpf": "02811411429",
                "rg": "227014273",
                "data_nasc": "10\/01\/1991",
                "sexo": "Feminino",
                "signo": "Capricórnio",
                "mae": "Ester Sebastiana",
                "pai": "Bryan Pietro Vitor Figueiredo",
                "email": "luizasandraesterfigueiredo_@macroengenharia.com",
                "senha": "l0dnDQxOr9",
                "cep": "66813420",
                "endereco": "Rua Sargento Joaquim Resende",
                "numero": 438,
                "bairro": "Campina de Icoaraci (Icoaraci)",
                "cidade": "Belém",
                "estado": "PA",
                "telefone_fixo": "9127657029",
                "celular": "91982077262",
                "altura": "1,58",
                "peso": 61,
                "tipo_sanguineo": "O+",
                "cor": "amarelo"
            },
            {
                "nome": "Pedro Henrique César Souza",
                "idade": 30,
                "cpf": "69280952455",
                "rg": "243155384",
                "data_nasc": "10\/11\/1991",
                "sexo": "Masculino",
                "signo": "Escorpião",
                "mae": "Agatha Stella",
                "pai": "Otávio Matheus Souza",
                "email": "pedrohenriquecesarsouza_@raffinimobiliario.com.br",
                "senha": "FFE3I9cbfR",
                "cep": "68509630",
                "endereco": "Quadra Especial",
                "numero": 471,
                "bairro": "Nova Marabá",
                "cidade": "Marabá",
                "estado": "PA",
                "telefone_fixo": "9436633030",
                "celular": "94997907418",
                "altura": "1,62",
                "peso": 78,
                "tipo_sanguineo": "O-",
                "cor": "laranja"
            },
            {
                "nome": "Igor Cauã César Almada",
                "idade": 30,
                "cpf": "85754839430",
                "rg": "143028947",
                "data_nasc": "15\/07\/1991",
                "sexo": "Masculino",
                "signo": "Câncer",
                "mae": "Maria Adriana Rafaela",
                "pai": "César Nathan Osvaldo Almada",
                "email": "iigorcauacesaralmada@azulcargo.com.br",
                "senha": "0JkZncuhc8",
                "cep": "68625120",
                "endereco": "Rua Estado de Goiás",
                "numero": 321,
                "bairro": "Centro",
                "cidade": "Paragominas",
                "estado": "PA",
                "telefone_fixo": "9126686062",
                "celular": "91999418968",
                "altura": "1,81",
                "peso": 81,
                "tipo_sanguineo": "B-",
                "cor": "verde"
            },
            {
                "nome": "Emily Regina Moraes",
                "idade": 30,
                "cpf": "07491581404",
                "rg": "166810411",
                "data_nasc": "23\/09\/1991",
                "sexo": "Feminino",
                "signo": "Libra",
                "mae": "Alessandra Giovanna Kamilly",
                "pai": "Francisco Lucca Moraes",
                "email": "emilyreginamoraes..emilyreginamoraes@rodrigofranco.com",
                "senha": "atkdc9cogT",
                "cep": "66913070",
                "endereco": "Travessa Eurico Romariz",
                "numero": 385,
                "bairro": "Chapéu Virado (Mosqueiro)",
                "cidade": "Belém",
                "estado": "PA",
                "telefone_fixo": "9127972521",
                "celular": "91987445020",
                "altura": "1,66",
                "peso": 62,
                "tipo_sanguineo": "O-",
                "cor": "vermelho"
            },
            {
                "nome": "Fernando Noah Mário Souza",
                "idade": 30,
                "cpf": "27591052454",
                "rg": "226336591",
                "data_nasc": "25\/08\/1991",
                "sexo": "Masculino",
                "signo": "Virgem",
                "mae": "Malu Aline Rafaela",
                "pai": "Roberto Filipe Diogo Souza",
                "email": "fernandonoahmariosouza..fernandonoahmariosouza@absoluta.med.br",
                "senha": "MwyaeTZpU8",
                "cep": "68377640",
                "endereco": "Rua Quatro",
                "numero": 148,
                "bairro": "Mutirão",
                "cidade": "Altamira",
                "estado": "PA",
                "telefone_fixo": "9325525179",
                "celular": "93984983123",
                "altura": "1,82",
                "peso": 50,
                "tipo_sanguineo": "AB+",
                "cor": "laranja"
            },
            {
                "nome": "Isadora Francisca das Neves",
                "idade": 30,
                "cpf": "72411253478",
                "rg": "319095319",
                "data_nasc": "18\/08\/1991",
                "sexo": "Feminino",
                "signo": "Leão",
                "mae": "Olivia Giovanna Beatriz",
                "pai": "Arthur Yuri Heitor das Neves",
                "email": "isadorafranciscadasneves__isadorafranciscadasneves@vitalliimoveis.com",
                "senha": "w7YhKxrSFQ",
                "cep": "66814480",
                "endereco": "Rua Ametista",
                "numero": 365,
                "bairro": "Paracuri (Icoaraci)",
                "cidade": "Belém",
                "estado": "PA",
                "telefone_fixo": "9139703433",
                "celular": "91981997769",
                "altura": "1,60",
                "peso": 46,
                "tipo_sanguineo": "A-",
                "cor": "azul"
            },
            {
                "nome": "Felipe Thomas Oliver Pereira",
                "idade": 30,
                "cpf": "38547414487",
                "rg": "151722705",
                "data_nasc": "01\/12\/1991",
                "sexo": "Masculino",
                "signo": "Sagitário",
                "mae": "Giovana Rayssa",
                "pai": "Benício Bernardo Nelson Pereira",
                "email": "felipethomasoliverpereira_@policiapenal.com",
                "senha": "18xMwTlgyx",
                "cep": "68742360",
                "endereco": "Travessa Rui Barbosa",
                "numero": 188,
                "bairro": "Nova Olinda",
                "cidade": "Castanhal",
                "estado": "PA",
                "telefone_fixo": "9139235548",
                "celular": "91981039946",
                "altura": "1,79",
                "peso": 108,
                "tipo_sanguineo": "B+",
                "cor": "vermelho"
            },
            {
                "nome": "Nelson Augusto da Rocha",
                "idade": 30,
                "cpf": "34134423414",
                "rg": "499262062",
                "data_nasc": "13\/08\/1991",
                "sexo": "Masculino",
                "signo": "Leão",
                "mae": "Malu Lívia Pietra",
                "pai": "Caio Renato Benjamin da Rocha",
                "email": "nnelsonaugustodarocha@tradevalle.com.br",
                "senha": "KODdpJzGnG",
                "cep": "68555900",
                "endereco": "Rua Francisco Caldeira Castelo Branco, s\/n",
                "numero": 951,
                "bairro": "Centro",
                "cidade": "Xinguara",
                "estado": "PA",
                "telefone_fixo": "9437700288",
                "celular": "94988758361",
                "altura": "1,85",
                "peso": 102,
                "tipo_sanguineo": "AB-",
                "cor": "vermelho"
            },
            {
                "nome": "Cauê Lucca da Silva",
                "idade": 30,
                "cpf": "69747320401",
                "rg": "474332093",
                "data_nasc": "25\/04\/1991",
                "sexo": "Masculino",
                "signo": "Touro",
                "mae": "Milena Valentina",
                "pai": "Raul Elias da Silva",
                "email": "caueluccadasilva..caueluccadasilva@defensoria.sp.gov.br",
                "senha": "MMR0IER2Xi",
                "cep": "66816217",
                "endereco": "Rua Carneiro",
                "numero": 238,
                "bairro": "Pratinha (Icoaraci)",
                "cidade": "Belém",
                "estado": "PA",
                "telefone_fixo": "9128865926",
                "celular": "91989604417",
                "altura": "1,64",
                "peso": 98,
                "tipo_sanguineo": "A+",
                "cor": "vermelho"
            },
            {
                "nome": "Nicole Elisa da Cunha",
                "idade": 30,
                "cpf": "49535689401",
                "rg": "405871958",
                "data_nasc": "16\/07\/1991",
                "sexo": "Feminino",
                "signo": "Câncer",
                "mae": "Sebastiana Elisa",
                "pai": "Cauê Guilherme Samuel da Cunha",
                "email": "nicoleelisadacunha__nicoleelisadacunha@zoomfoccus.com.br",
                "senha": "eEi59IPjwE",
                "cep": "66820079",
                "endereco": "Quadra Quatorze",
                "numero": 955,
                "bairro": "Tenoné",
                "cidade": "Belém",
                "estado": "PA",
                "telefone_fixo": "9135093938",
                "celular": "91996890373",
                "altura": "1,60",
                "peso": 50,
                "tipo_sanguineo": "O+",
                "cor": "amarelo"
            },
            {
                "nome": "Sophia Yasmin Sônia Fogaça",
                "idade": 30,
                "cpf": "80349352470",
                "rg": "184897014",
                "data_nasc": "02\/06\/1991",
                "sexo": "Feminino",
                "signo": "Gêmeos",
                "mae": "Laura Sophie Elisa",
                "pai": "Caio Bryan Erick Fogaça",
                "email": "sophiayasminsoniafogaca..sophiayasminsoniafogaca@equipavmineracao.com.br",
                "senha": "mFyHAJqRRm",
                "cep": "68501262",
                "endereco": "Acampamento São Francisco",
                "numero": 279,
                "bairro": "Liberdade",
                "cidade": "Marabá",
                "estado": "PA",
                "telefone_fixo": "9439740397",
                "celular": "94984299704",
                "altura": "1,54",
                "peso": 68,
                "tipo_sanguineo": "O+",
                "cor": "vermelho"
            },
            {
                "nome": "Silvana Sandra Brito",
                "idade": 30,
                "cpf": "04947835400",
                "rg": "487621591",
                "data_nasc": "12\/03\/1991",
                "sexo": "Feminino",
                "signo": "Peixes",
                "mae": "Rosângela Heloise",
                "pai": "Geraldo Nathan Nelson Brito",
                "email": "ssilvanasandrabrito@bfgadvogados.com",
                "senha": "lpfoJbF4Sa",
                "cep": "66843350",
                "endereco": "Rua Miriti",
                "numero": 119,
                "bairro": "Água Boa (Outeiro)",
                "cidade": "Belém",
                "estado": "PA",
                "telefone_fixo": "9136079305",
                "celular": "91985712486",
                "altura": "1,77",
                "peso": 52,
                "tipo_sanguineo": "AB+",
                "cor": "azul"
            },
            {
                "nome": "Lavínia Rosa Monteiro",
                "idade": 30,
                "cpf": "40011250488",
                "rg": "482037465",
                "data_nasc": "24\/08\/1991",
                "sexo": "Feminino",
                "signo": "Virgem",
                "mae": "Jennifer Daniela",
                "pai": "Augusto Theo Alexandre Monteiro",
                "email": "laviniarosamonteiro-85@flex-erp.com",
                "senha": "klUClYLxox",
                "cep": "68456254",
                "endereco": "Rua Vinte e Cinco de Dezembro",
                "numero": 508,
                "bairro": "Colorado",
                "cidade": "Tucuruí",
                "estado": "PA",
                "telefone_fixo": "9439898232",
                "celular": "94986873798",
                "altura": "1,80",
                "peso": 67,
                "tipo_sanguineo": "AB+",
                "cor": "vermelho"
            }
        ]
        ';


        $response = json_decode($pessoas, true);

        return $response;

    }
}
