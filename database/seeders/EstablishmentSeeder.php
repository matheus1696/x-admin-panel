<?php

namespace Database\Seeders;

use App\Models\Configuration\Establishment\Establishment\Establishment;
use Illuminate\Database\Seeder;

class EstablishmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Establishment::create([ 'code'=>2682303, 'title'=>'AME Caiuca', 'filter'=>'ame caiuca', 'address'=>'Av Leao Dourado', 'number'=>'1248', 'district'=>'Vila Kennedy', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82862536', 'longitude'=>'-359923311977', 'type_establishment_id'=>36, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>3105563, 'title'=>'AME Diagnostico', 'filter'=>'ame diagnostico', 'address'=>'Rua Djalma Dutra', 'number'=>'S/N', 'district'=>'Nossa das Senhora das Dores', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82883905', 'longitude'=>'-359767625', 'type_establishment_id'=>39, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>3020932, 'title'=>'AME Fernando Lyra', 'filter'=>'ame fernando lyra', 'address'=>'Rua Dep Magalhaes R07', 'number'=>'S/N', 'district'=>'Sao Joao da Escocia', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82694330941', 'longitude'=>'-359571576118', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>9070532, 'title'=>'AME Infantojuvenil', 'filter'=>'ame infantojuvenil', 'address'=>'Rua General Dionisio Siqueira Porto', 'number'=>'709', 'district'=>'Mauricio de Nassau', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-8268063462', 'longitude'=>'-359712553024', 'type_establishment_id'=>36, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>9415610, 'title'=>'AME Luiz Bezerra Torres', 'filter'=>'ame luiz bezerra torres', 'address'=>'Rua Gerbera', 'number'=>'18717', 'district'=>'Nossa Senhora das Graças', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82974511116', 'longitude'=>'-360450053215', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>7484747, 'title'=>'AME Maria Lira Morada Nova Rendeiras', 'filter'=>'ame maria lira morada nova rendeiras', 'address'=>'Rua Francisco Maximiniano', 'number'=>'S/N', 'district'=>'Rendeiras', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82791265558', 'longitude'=>'-359208726883', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2682532, 'title'=>'AME Pedro Justino Rodrigues UBS Kennedy II Padre Inacio', 'filter'=>'ame pedro justino rodrigues ubs kennedy ii padre inacio', 'address'=>'Rua Joaquim Alves Souza E 08', 'number'=>'S/N', 'district'=>'Vila Kennedy', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82848968658', 'longitude'=>'-359974068403', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>4290402, 'title'=>'UBS Riachão', 'filter'=>'ubs riachão', 'address'=>'Rua Aymore', 'number'=>'165', 'district'=>'Riachão', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'', 'longitude'=>'', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>6563317, 'title'=>'AME Saúde da Mulher', 'filter'=>'ame saúde da mulher', 'address'=>'Rua Rodrigues de Abreu', 'number'=>'232', 'district'=>'Mauricio de Nassau', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82735212', 'longitude'=>'-359735054', 'type_establishment_id'=>36, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>2682273, 'title'=>'AME Saúde do Idoso', 'filter'=>'ame saúde do idoso', 'address'=>'Rua Coronel Limeira', 'number'=>'211', 'district'=>'Nossa Senhora das Dores', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-81693275', 'longitude'=>'-362011179', 'type_establishment_id'=>36, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>7612621, 'title'=>'CAPS AD III Mandacaru', 'filter'=>'caps ad iii mandacaru', 'address'=>'Rua Rio Formoso', 'number'=>'S/N', 'district'=>'Boa Vista II', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82706744', 'longitude'=>'-359884398', 'type_establishment_id'=>70, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>8016313, 'title'=>'CAPS Crescendo Com Dignidade', 'filter'=>'caps crescendo com dignidade', 'address'=>'Rua Rio Formoso', 'number'=>'S/N', 'district'=>'Boa Vista II', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82706646972', 'longitude'=>'-359881854057', 'type_establishment_id'=>70, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>7594658, 'title'=>'CAF - Central de Abastecimento Farmaceutico de Caruaru', 'filter'=>'caf - central de abastecimento farmaceutico de caruaru', 'address'=>'Av Vera Cruz', 'number'=>'654', 'district'=>'Sao Francisco', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82883844914', 'longitude'=>'-359802246094', 'type_establishment_id'=>43, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>2819260, 'title'=>'Regulação - Central de Regulacao de Assistencia a Saúde', 'filter'=>'central de regulacao de assistencia a saúde', 'address'=>'Rua Martin Afonso', 'number'=>'267', 'district'=>'Sao Francisco', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82916544441', 'longitude'=>'-359801387787', 'type_establishment_id'=>81, 'financial_block_id'=>1,]);

        Establishment::create([ 'code'=>3932494, 'title'=>'CEREST - Centro de Referencia de Saúde do Trabalhador', 'filter'=>'cerest - centro de referencia de saúde do trabalhador', 'address'=>'Rua Coronel Limeira', 'number'=>'211', 'district'=>'Nossa Senhora das Dores', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-81693275', 'longitude'=>'-362011179', 'type_establishment_id'=>36, 'financial_block_id'=>1,]);

        Establishment::create([ 'code'=>2345579, 'title'=>'Centro de Saúde Amelia de Pontes', 'filter'=>'centro de saúde amelia de pontes', 'address'=>'Rua dos Guararapes', 'number'=>'S/N', 'district'=>'Centro', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82872378782', 'longitude'=>'-359680366516', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345927, 'title'=>'Centro de Saúde Ana Rodrigues', 'filter'=>'centro de saúde ana rodrigues', 'address'=>'Rua Martin Afonso', 'number'=>'267', 'district'=>'Sao Francisco', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-8291590744', 'longitude'=>'-359798598289', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345587, 'title'=>'Centro de Saúde Boa Vista', 'filter'=>'centro de saúde boa vista', 'address'=>'Rua Cabo', 'number'=>'S/N', 'district'=>'Boa Vista', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-8275952053', 'longitude'=>'-359880030155', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345803, 'title'=>'Centro de Saúde Cedro', 'filter'=>'c s cedro', 'address'=>'Rua R 8', 'number'=>'24', 'district'=>'Cidade Jardim', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82763714285', 'longitude'=>'-359419548512', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345595, 'title'=>'Centro de Saúde Indianopolis', 'filter'=>'centro de saúde indianopolis', 'address'=>'Rua Alferes Jorge', 'number'=>'S/N', 'district'=>'Indianopolis', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82877755', 'longitude'=>'-359638745', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>9037306, 'title'=>'Centro de Zoonoses de Caruaru', 'filter'=>'centro de zoonoses de caruaru', 'address'=>'Rua Sao Sebastiao', 'number'=>'S/N', 'district'=>'Alto do Moura', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82850216139', 'longitude'=>'-360003277659', 'type_establishment_id'=>50, 'financial_block_id'=>4,]);

        Establishment::create([ 'code'=>7717342, 'title'=>'Centro Municipal de Especialidades Medicas II', 'filter'=>'centro municipal de especialidades medicas ii', 'address'=>'Av Dom Bosco', 'number'=>'143', 'district'=>'Mauricio de Nassau', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'', 'longitude'=>'', 'type_establishment_id'=>36, 'financial_block_id'=>2, 'status'=>false]);

        Establishment::create([ 'code'=>3083748, 'title'=>'CEO - Centro de Especialidades Odontológicas Municipal', 'filter'=>'ceo - centro de especializades odontológicas municipal', 'address'=>'Rua Martim Afonso', 'number'=>'267', 'district'=>'Sao Francisco', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82916756775', 'longitude'=>'-359800100327', 'type_establishment_id'=>36, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345986, 'title'=>'COAS/CTA Centro de Testagem e Aconselhamento', 'filter'=>'coas/cta centro de testagem e aconselhamento', 'address'=>'Rua Cel Limeira', 'number'=>'189', 'district'=>'Centro', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82843925642', 'longitude'=>'-359681224823', 'type_establishment_id'=>36, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>4059085, 'title'=>'EAP Sao Francisco I e II', 'filter'=>'eap sao francisco i e ii', 'address'=>'Rua Padre Jose Augusto', 'number'=>'225', 'district'=>'Sao Francisco', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82892863', 'longitude'=>'-359783202', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>9843434, 'title'=>'Farmacia Caruaru Sao Francisco I', 'filter'=>'farmacia caruaru sao francisco i', 'address'=>'Rua Martin Afonso', 'number'=>'267', 'district'=>'Sao Francisco', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82905184571', 'longitude'=>'-359782934189', 'type_establishment_id'=>43, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>7612389, 'title'=>'Farmacia Caruaru Sao Francisco II', 'filter'=>'farmacia caruaru sao francisco ii', 'address'=>'Av Vera Cruz', 'number'=>'654', 'district'=>'Sao Francisco', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82883632578', 'longitude'=>'-359816622734', 'type_establishment_id'=>43, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>5093619, 'title'=>'Hospital Municipal de Caruaru Dr Manoel Afonso Porto Neto', 'filter'=>'hospital municipal de caruaru dr manoel afonso porto neto', 'address'=>'Rua Quiteria Francisca Silva', 'number'=>'494', 'district'=>'Maria Auxiliadora', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82758912', 'longitude'=>'-359963379', 'type_establishment_id'=>5, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>2682435, 'title'=>'Laboratorio Central', 'filter'=>'laboratorio central', 'address'=>'Rua Djalma Dutra', 'number'=>'S/N', 'district'=>'Nossa Senhora das Dores', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82883905', 'longitude'=>'-359767625', 'type_establishment_id'=>39, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>2345897, 'title'=>'LACIAN - Laboratorio de Cito e Anatomopatologia', 'filter'=>'lacian - laboratorio de cito e anatomopatologia', 'address'=>'Rua Djalma Dutra', 'number'=>'S/N', 'district'=>'Nossa Senhora das Dores', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82883905', 'longitude'=>'-359767625', 'type_establishment_id'=>39, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>3083721, 'title'=>'Maternidade Santa Dulce dos Pobres', 'filter'=>'maternidade santa dulce dos pobres', 'address'=>'Rua 09', 'number'=>'S/N', 'district'=>'Loteamento Luiz Gonzaga', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82497375393', 'longitude'=>'-359714484215', 'type_establishment_id'=>5, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>2346052, 'title'=>'UBS Agamenon Magalhaes I e Encanto da Serra', 'filter'=>'ubs agamenon magalhaes i e encanto da serra', 'address'=>'Rua Marieta Cruz', 'number'=>'S/N', 'district'=>'Agamenon Magalhaes', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-83061141036', 'longitude'=>'-359727573395', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2797968, 'title'=>'UBS Agamenon Magalhaes II', 'filter'=>'ubs agamenon magalhaes ii', 'address'=>'Trav Zacarias', 'number'=>'27', 'district'=>'Vila Campos', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-8283', 'longitude'=>'-35976', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2682419, 'title'=>'UBS Alto do Moura', 'filter'=>'ubs alto do moura', 'address'=>'Rua Sao Sebastiao', 'number'=>'S/N', 'district'=>'Alto do Moura', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-8284753538', 'longitude'=>'-360019397736', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>7425856, 'title'=>'UBS Barra de Taquara', 'filter'=>'ubs barra de taquara', 'address'=>'Rua Expedito Antonio Da Silva', 'number'=>'49', 'district'=>'Sitio Taquara de Cima', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82844562655', 'longitude'=>'-360033774376', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>7425872, 'title'=>'UBS Cachoeira Seca', 'filter'=>'ubs cachoeira seca', 'address'=>'Rua Maria Quaresma', 'number'=>'25', 'district'=>'Vila Cachoeira Seca', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-81677595372', 'longitude'=>'-359722208977', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>7335865, 'title'=>'UBS Caic', 'filter'=>'ubs caic', 'address'=>'Rua Sao Nicolau', 'number'=>'561', 'district'=>'Joao Mota', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82806978721', 'longitude'=>'-359882712364', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2682508, 'title'=>'UBS Caiuca I', 'filter'=>'ubs caiuca i', 'address'=>'Rua Sao Joaquim do Monte', 'number'=>'684', 'district'=>'Caiuca', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82863354487', 'longitude'=>'-359875953197', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>3741273, 'title'=>'UBS Caiuca II', 'filter'=>'ubs caiuca ii', 'address'=>'Av Caiuca', 'number'=>'330', 'district'=>'Caiuca', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-8284365', 'longitude'=>'-359864958', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345862, 'title'=>'UBS Caja', 'filter'=>'ubs caja', 'address'=>'Rua Oscar Laranjeira', 'number'=>'88', 'district'=>'Vila do Aeroporto', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82868019', 'longitude'=>'-359635198', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>5481287, 'title'=>'UBS Canaa', 'filter'=>'ubs canaa', 'address'=>'Vila Canaa', 'number'=>'S/N', 'district'=>'Vila Canaa', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82531352254', 'longitude'=>'-359749245644', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2346044, 'title'=>'UBS Centenario', 'filter'=>'ubs centenario', 'address'=>'Rua Da Uniao', 'number'=>'S/N', 'district'=>'Centenario', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82831864', 'longitude'=>'-359795778', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2682478, 'title'=>'UBS Cidade Jardim I e II', 'filter'=>'ubs cidade jardim i e ii', 'address'=>'Rua Maria Bezerra de Araujo', 'number'=>'100', 'district'=>'Cidade Jardim', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82741264', 'longitude'=>'-359482806', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>7346085, 'title'=>'UBS Cipo', 'filter'=>'ubs cipo', 'address'=>'Sitio Cipo', 'number'=>'50', 'district'=>'Posto Agamenon', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-83032901591', 'longitude'=>'-359969615936', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345846, 'title'=>'UBS Gonçalves Ferreira', 'filter'=>'ubs gonçalves ferreira', 'address'=>'Rua Sao Pedro', 'number'=>'S/N', 'district'=>'Vila Goncalves Ferreira', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82578123', 'longitude'=>'-358827813', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>9954538, 'title'=>'UBS Indianopolis I e II', 'filter'=>'ubs indianopolis i e ii', 'address'=>'Rua Monteiro Lobato', 'number'=>'480', 'district'=>'Indianopolis', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82879927', 'longitude'=>'-35958204', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345544, 'title'=>'UBS Itauna', 'filter'=>'ubs itauna', 'address'=>'Rua Boa Vista', 'number'=>'S/N', 'district'=>'Povoado Itauna', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82534962278', 'longitude'=>'-35974817276', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>3741265, 'title'=>'UBS Jardim Liberdade', 'filter'=>'ubs jardim liberdade', 'address'=>'Rua Arquimedes de Oliveira', 'number'=>'222', 'district'=>'Jardim Liberdade', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82949821', 'longitude'=>'-359831367', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2682516, 'title'=>'UBS Jardim Panorama I', 'filter'=>'ubs jardim panorama i', 'address'=>'Rua Professor Esmeralda Barros', 'number'=>'38', 'district'=>'Jardim Panorama', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82729898684', 'longitude'=>'-360072183609', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345641, 'title'=>'UBS Joao Mota', 'filter'=>'ubs joao mota', 'address'=>'Sao Nicolau', 'number'=>'561', 'district'=>'Joao Mota', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82808465098', 'longitude'=>'-359875845909', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345668, 'title'=>'UBS Jose Carlos de Oliveira I', 'filter'=>'ubs jose carlos de oliveira i', 'address'=>'Av Alexandrino Boa Ventura', 'number'=>'273', 'district'=>'Jose Carlos de Oliveira', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82786381724', 'longitude'=>'-360039138794', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2797976, 'title'=>'UBS Jose Carlos de Oliveira II e III', 'filter'=>'ubs jose carlos de oliveira ii e iii', 'address'=>'Rua Cicera Ferreira', 'number'=>'S/N', 'district'=>'Jose Carlos de Oliveira', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82726076494', 'longitude'=>'-36011402607', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2682524, 'title'=>'UBS Jose Liberato I', 'filter'=>'ubs jose liberato i', 'address'=>'Rua Mestre Galdino', 'number'=>'125', 'district'=>'Loteamento Jose Liberato', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82767271012', 'longitude'=>'-360061240196', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>7474709, 'title'=>'UBS Jose Liberato II', 'filter'=>'ubs jose liberato ii', 'address'=>'Av Berna', 'number'=>'98', 'district'=>'Jose Liberato', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82666832077', 'longitude'=>'-360000300407', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345757, 'title'=>'UBS Jua', 'filter'=>'ubs jua', 'address'=>'Rua Joao Luiz Ferreira', 'number'=>'S/N', 'district'=>'Povoado Jua', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-81921210875', 'longitude'=>'-359665775299', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2682451, 'title'=>'UBS Lagoa de Pedra', 'filter'=>'ubs lagoa de pedra', 'address'=>'Sitio Maribondo', 'number'=>'S/N', 'district'=>'Sitio Maribondo', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82624574763', 'longitude'=>'-36044511795', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345625, 'title'=>'UBS Lajes', 'filter'=>'ubs lajes', 'address'=>'Povoado de Lajes', 'number'=>'S/N', 'district'=>'Povoado de Lajes', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-81004017906', 'longitude'=>'-360210371017', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345870, 'title'=>'UBS Malhada de Barreiras Queimadas', 'filter'=>'ubs malhada de barreiras queimadas', 'address'=>'Povoado Malhada de Barreiras Queimadas', 'number'=>'S/N', 'district'=>'Povoado Malhada de Barreiras Queimadas', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82460000509', 'longitude'=>'-359165811539', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>9081461, 'title'=>'UBS Maria Auxiliadora I e II', 'filter'=>'ubs maria auxiliadora i e ii', 'address'=>'Rua Leonor Galvao', 'number'=>'143', 'district'=>'Maria Auxiliadora', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82618841324', 'longitude'=>'-360068643093', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345854, 'title'=>'UBS Morro Centenario', 'filter'=>'ubs morro centenario', 'address'=>'Rua Da Se', 'number'=>'45', 'district'=>'Centenario', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82834158099', 'longitude'=>'-359782505035', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345676, 'title'=>'UBS Morro São Francisco', 'filter'=>'ubs morro são francisco', 'address'=>'Rua Joao Jose do Rego', 'number'=>'262', 'district'=>'Morro do Bom Jesus', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82872398', 'longitude'=>'-359766203', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2346036, 'title'=>'UBS Murici', 'filter'=>'ubs murici', 'address'=>'Sitio Murici', 'number'=>'S/N', 'district'=>'Vila Murici', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-83412736942', 'longitude'=>'-360389328003', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2797992, 'title'=>'UBS Nova Caruaru', 'filter'=>'ubs nova caruaru', 'address'=>'Rua Deputado Osvaldo Rabelo', 'number'=>'S/N', 'district'=>'Nova Caruaru', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82713972868', 'longitude'=>'-359850955009', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>7425880, 'title'=>'UBS Novo Mundo e Demostenes Veras', 'filter'=>'ubs novo mundo e demostenes veras', 'address'=>'Av Recife', 'number'=>'S/N', 'district'=>'Demostenes Veras', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82819082061', 'longitude'=>'-35913105011', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345633, 'title'=>'UBS Pau Santo', 'filter'=>'ubs pau santo', 'address'=>'Povoado de Pau Santo', 'number'=>'S/N', 'district'=>'Povoado de Pau Santo', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82943935394', 'longitude'=>'-358895874023', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2346060, 'title'=>'UBS Peladas', 'filter'=>'ubs peladas', 'address'=>'Povoado de Peladas', 'number'=>'S/N', 'district'=>'Povoado Peladas', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82909962091', 'longitude'=>'-358669281006', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345900, 'title'=>'UBS Rafael', 'filter'=>'ubs rafael', 'address'=>'Rua Ernesto Branco', 'number'=>'122', 'district'=>'Vila Rafael', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-81809918456', 'longitude'=>'-359698820114', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345684, 'title'=>'UBS Rendeiras I', 'filter'=>'ubs rendeiras i', 'address'=>'Rua Major Joao Coelho', 'number'=>'376', 'district'=>'Rendeiras', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-8280782808', 'longitude'=>'-359365582466', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>9598170, 'title'=>'UBS Residencial Alto do Moura', 'filter'=>'ubs residencial alto do moura', 'address'=>'Br 232', 'number'=>'S/N', 'district'=>'Agamenom Magalhaes', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-83002044', 'longitude'=>'-359880701', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2346087, 'title'=>'UBS Riacho Doce', 'filter'=>'ubs riacho doce', 'address'=>'Povoado Riacho Doce', 'number'=>'S/N', 'district'=>'Povoado Riacho Doce', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-81330730918', 'longitude'=>'-360672569275', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345730, 'title'=>'UBS Salgado I e II', 'filter'=>'ubs salgado i e ii', 'address'=>'Rua Martins Francisco', 'number'=>'S/N', 'district'=>'Salgado', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-8280782808', 'longitude'=>'-359586381912', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2682559, 'title'=>'UBS Salgado III', 'filter'=>'ubs salgado iii', 'address'=>'Rua Varzea do Cedro', 'number'=>'42', 'district'=>'Salgado', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82654515922', 'longitude'=>'-359567070007', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2682567, 'title'=>'UBS Salgado IV', 'filter'=>'ubs salgado iv', 'address'=>'Rua Alexandrino de Alencar', 'number'=>'386', 'district'=>'Salgado', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82772791894', 'longitude'=>'-359593248367', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2682486, 'title'=>'UBS Santa Rosa I', 'filter'=>'ubs santa rosa i', 'address'=>'Rua Arrojado Lisboa', 'number'=>'S/N', 'district'=>'Santa Rosa', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82917181442', 'longitude'=>'-359621357918', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345560, 'title'=>'UBS Santa Rosa II III e IV', 'filter'=>'ubs santa rosa ii iii e iv', 'address'=>'Rua Venustriano Correia', 'number'=>'S/N', 'district'=>'Santa Rosa', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82956250644', 'longitude'=>'-359626722336', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345706, 'title'=>'UBS Sao Joao da Escocia I III e IV', 'filter'=>'ubs sao joao da escocia i iii e iv', 'address'=>'Rua Genova', 'number'=>'S/N', 'district'=>'Sao Joao da Escocia', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82677609', 'longitude'=>'-359511226', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2682443, 'title'=>'UBS Serra Velha', 'filter'=>'ubs serra velha', 'address'=>'Sitio Serra Velha', 'number'=>'S/N', 'district'=>'Vila Serra Velha', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82452567962', 'longitude'=>'-359128046036', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>7425791, 'title'=>'UBS Serranopolis', 'filter'=>'ubs serranopolis', 'address'=>'Rua Francisca Ana Da Conceicao', 'number'=>'S/N', 'district'=>'Serranopolis', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82810163814', 'longitude'=>'-359143066406', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>9132821, 'title'=>'UBS Severino Afonso', 'filter'=>'ubs severino afonso', 'address'=>'Rua Florinaldo Da Silva', 'number'=>'S/N', 'district'=>'Severino Afonso', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82538466122', 'longitude'=>'-359806215763', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345994, 'title'=>'UBS Sinhazinha I E II', 'filter'=>'ubs sinhazinha i e ii', 'address'=>'Rua Neco Lira', 'number'=>'S/N', 'district'=>'Salgado', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82775663', 'longitude'=>'-359642736', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345749, 'title'=>'UBS Terra Vermelha', 'filter'=>'ubs terra vermelha', 'address'=>'Vila Terra Vermelha', 'number'=>'S/N', 'district'=>'Vila Terra Vermelha', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-8444865638', 'longitude'=>'-360035705566', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>7484763, 'title'=>'UBS Vassoural I II e III', 'filter'=>'ubs vassoural i ii e iii', 'address'=>'Rua Joao Cordeiro de Souza', 'number'=>'S/N', 'district'=>'Vassoural', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-83003794', 'longitude'=>'-359697085', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2682494, 'title'=>'UBS Vila Kennedy I', 'filter'=>'ubs vila kennedy i', 'address'=>'Rua Eugenio Cordeiro de Souza', 'number'=>'S/N', 'district'=>'Vila Kennedy', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82866858039', 'longitude'=>'-359950894117', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2345692, 'title'=>'UBS Xicuru', 'filter'=>'ubs xicuru', 'address'=>'Vila Sao Joao de Xicuru', 'number'=>'S/N', 'district'=>'Vila Xicuru', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82519248031', 'longitude'=>'-36016960144', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2864940, 'title'=>'UBS Xique Xique I e II Dr Xisto Zeno Valones', 'filter'=>'ubs xique xique i e ii dr xisto zeno valones', 'address'=>'Av 16 de Setembro', 'number'=>'S/N', 'district'=>'Xique Xique', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-8265366653', 'longitude'=>'-360012960434', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>2682281, 'title'=>'Unidade de Fisioterapia da 3 Idade', 'filter'=>'unidade de fisioterapia da 3 idade', 'address'=>'Av Lourival Jose Da Silva', 'number'=>'483', 'district'=>'Petropolis', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82891553', 'longitude'=>'-35973624', 'type_establishment_id'=>36, 'financial_block_id'=>2, 'status'=>false]);

        Establishment::create([ 'code'=>2346028, 'title'=>'Unidade de Vigilancia em Saúde', 'filter'=>'unidade de vigilancia em saúde', 'address'=>'Av Vera Cruz', 'number'=>'654', 'district'=>'Sao Francisco', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82883632578', 'longitude'=>'-359797739983', 'type_establishment_id'=>50, 'financial_block_id'=>4,]);

        Establishment::create([ 'code'=>9147616, 'title'=>'Unidade Municipal de Fisioterapia', 'filter'=>'unidade municipal de fisioterapia', 'address'=>'Rua deolindo Tavares', 'number'=>'191', 'district'=>'Mauricio de Nassau', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82763263', 'longitude'=>'-359695438', 'type_establishment_id'=>36, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>3985989, 'title'=>'Unidade Municipal de Saúde Auditiva', 'filter'=>'unidade municipal de saúde auditiva', 'address'=>'Av Dom Bosco', 'number'=>'143', 'district'=>'Mauricio de Nassau', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82760729', 'longitude'=>'-359677761', 'type_establishment_id'=>36, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>2345935, 'title'=>'Unidade Saúde Escola Dr Antonio Vieira', 'filter'=>'unidade saúde escola dr antonio vieira', 'address'=>'Rua Presidente Artur Bernardes', 'number'=>'S/N', 'district'=>'Salgado', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82720366', 'longitude'=>'-359572279', 'type_establishment_id'=>2, 'financial_block_id'=>3,]);

        Establishment::create([ 'code'=>9070427, 'title'=>'UPA Boa Vista Dr Amorim', 'filter'=>'upa boa vista dr amorim', 'address'=>'Rua Paraense', 'number'=>'S/N', 'district'=>'Divinopolis', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82754689742', 'longitude'=>'-359827029705', 'type_establishment_id'=>73, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>7819587, 'title'=>'UPA do Salgado', 'filter'=>'upa do salgado', 'address'=>'Rua Rodopiano Florencio', 'number'=>'S/N', 'district'=>'Salgado', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82710787698', 'longitude'=>'-359579730034', 'type_establishment_id'=>20, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>9070419, 'title'=>'UPA Rendeiras Dr Jose Barreto', 'filter'=>'upa rendeiras dr jose barreto', 'address'=>'Av Major Joao Coelho', 'number'=>'S/N', 'district'=>'Rendeiras', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'-82792751941', 'longitude'=>'-359317946434', 'type_establishment_id'=>73, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>6451357, 'title'=>'Secretaria de Saúde de Caruaru', 'filter'=>'secretaria de saúde de caruaru', 'address'=>'Av Vera Cruz', 'number'=>'654', 'district'=>'Sao Francisco', 'city_id'=>'2604106', 'state_id'=>'26', 'type_establishment_id'=>68, 'financial_block_id'=>1,]);

        Establishment::create([ 'code'=>6855881, 'title'=>'UPA Vassoural', 'filter'=>'upa vassoural', 'address'=>'Rua Luiz Gonzaga', 'number'=>'S/N', 'district'=>'Vassoural', 'city_id'=>'2604106', 'state_id'=>'26', 'type_establishment_id'=>73, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>2682575, 'title'=>'Unidade de Saúde Mental', 'filter'=>'unidade de saúde mental', 'address'=>'Rua Djalma Dutra', 'number'=>'S/N', 'district'=>'Nossa Senhora das Dores', 'city_id'=>'2604106', 'state_id'=>'26', 'type_establishment_id'=>36, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>3497399, 'title'=>'SAMU Caruaru - Central de Regulação das Urgências de Caruaru', 'filter'=>'samu caruaru - central de regulação de urgências de caruaru', 'address'=>'Rua Azevedo Coutinho', 'number'=>'430', 'district'=>'Petropolis', 'city_id'=>'2604106', 'state_id'=>'26', 'type_establishment_id'=>36, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>0000000, 'title'=>'Complexo da Saúde Jaqueline', 'filter'=>'complexo da saúde jaqueline', 'address'=>'Rua Djalma Dutra', 'number'=>'S/N', 'district'=>'Nossa Senhora das Dores', 'city_id'=>'2604106', 'state_id'=>'26', 'type_establishment_id'=>68, 'financial_block_id'=>1,]);        

        Establishment::create([ 'code'=>4437454, 'title'=>'AME do Salgado', 'filter'=>'ame do salgado', 'address'=>'Rua Presidente Artur Bernardes', 'number'=>'S/N', 'district'=>'Salgado', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'', 'longitude'=>'', 'type_establishment_id'=>36, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>4430565, 'title'=>'Farmácia Caruaru do Salgado', 'filter'=>'farmacia caruaru do saldago', 'address'=>'Rua Presidente Artur Bernardes', 'number'=>'S/N', 'district'=>'Salgado', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'', 'longitude'=>'', 'type_establishment_id'=>43, 'financial_block_id'=>2,]);

        Establishment::create([ 'code'=>123456789, 'title'=>'UBS Irmã Rute (UBS Vila Kennedy I e UBS Caiuca I)', 'filter'=>'ubs irmã rute (ubs vila kennedy i e ubs caiuca i)', 'address'=>'Vila Kennedy', 'number'=>'S/N', 'district'=>'Salgado', 'city_id'=>'2604106', 'state_id'=>'26', 'latitude'=>'', 'longitude'=>'', 'type_establishment_id'=>2, 'financial_block_id'=>2,]);
    }
}