<?php

/*
 * GENERATED FILE. Do not edit: run `bin/generate-vocabulary` instead.
 *
 * Derived from the IATA ONE Record ontologies (MIT License, (c) IATA), merged across
 * the editions listed in Manifest::EDITIONS at their pinned commits.
 */

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists;

/**
 * Code list CommodityCode: Restricted code list of accepted commodities in carrier bookings when no HS
 * code available.
 */
final class CommodityCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/CommodityCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Chemicals */
    public const string CHEM = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CHEM';

    /** Chemicals - Dangerous */
    public const string CHEM_CDGR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CHEM_CDGR';

    /** Cleaning products */
    public const string CHEM_CLNG = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CHEM_CLNG';

    /** Chemicals - Not dangerous */
    public const string CHEM_CNDG = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CHEM_CNDG';

    /** Chemicals - Not Medical */
    public const string CHEM_CNMD = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CHEM_CNMD';

    /** Cosmetics */
    public const string CHEM_COSM = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CHEM_COSM';

    /** Cosmetics - DGR */
    public const string CHEM_COSM_COSD = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CHEM_COSM_COSD';

    /** Perfume */
    public const string CHEM_COSM_PERF = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CHEM_COSM_PERF';

    /** Dangerous Goods */
    public const string CHEM_DGRG = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CHEM_DGRG';

    /** Explosives */
    public const string CHEM_DGRG_EXPL = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CHEM_DGRG_EXPL';

    /** Dry ice */
    public const string CHEM_DICE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CHEM_DICE';

    /** Paint */
    public const string CHEM_PAIN = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CHEM_PAIN';

    /** Petroleum derivatives */
    public const string CHEM_PETRO = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CHEM_PETRO';

    /** Radioactive materials */
    public const string CHEM_RADA = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CHEM_RADA';

    /** Reagents */
    public const string CHEM_REAG = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CHEM_REAG';

    /** Consumer goods */
    public const string CONS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CONS';

    /** Company material */
    public const string CONS_CMPY = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CONS_CMPY';

    /** Chinaware and Ceramics */
    public const string CONS_CWRE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CONS_CWRE';

    /** Diplomatic mail and goods */
    public const string CONS_DIPL = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CONS_DIPL';

    /** Exhibition goods */
    public const string CONS_EXHB = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CONS_EXHB';

    /** Furniture */
    public const string CONS_FRNT = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CONS_FRNT';

    /** Glassware */
    public const string CONS_GLAS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CONS_GLAS';

    /** Humanitarian aid */
    public const string CONS_HAID = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CONS_HAID';

    /** Household goods */
    public const string CONS_HHGD = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CONS_HHGD';

    /** Horse equipment */
    public const string CONS_HRSE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CONS_HRSE';

    /** House removal */
    public const string CONS_HSER = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CONS_HSER';

    /** Office supplies */
    public const string CONS_OFSP = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CONS_OFSP';

    /** Personal effects */
    public const string CONS_PERS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CONS_PERS';

    /** Spectacles */
    public const string CONS_SPEC = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CONS_SPEC';

    /** Sports equipment */
    public const string CONS_SPRT = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CONS_SPRT';

    /** Toys and Games */
    public const string CONS_TOYS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CONS_TOYS';

    /** Unaccompagnied baggage */
    public const string CONS_UBAG = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#CONS_UBAG';

    /** Electronic equipment */
    public const string ELEC = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#ELEC';

    /** Audio-Video-HiFi equipment */
    public const string ELEC_AVEQ = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#ELEC_AVEQ';

    /** Calculators */
    public const string ELEC_CALC = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#ELEC_CALC';

    /** Computers */
    public const string ELEC_CMPT = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#ELEC_CMPT';

    /** Computer parts */
    public const string ELEC_CPRT = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#ELEC_CPRT';

    /** Electronic components */
    public const string ELEC_ECOM = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#ELEC_ECOM';

    /** Electronic equipment */
    public const string ELEC_EEQP = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#ELEC_EEQP';

    /** Electronic goods */
    public const string ELEC_EGDS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#ELEC_EGDS';

    /** Electrical equipment */
    public const string ELEC_ELQP = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#ELEC_ELQP';

    /** Office equipment */
    public const string ELEC_OFEQ = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#ELEC_OFEQ';

    /** Quantum */
    public const string ELEC_QUAN = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#ELEC_QUAN';

    /** Telecom equipment */
    public const string ELEC_TELC = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#ELEC_TELC';

    /** Plants, Flowers, Seeds */
    public const string FLWR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FLWR';

    /** Fresh flowers */
    public const string FLWR_FLWR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FLWR_FLWR';

    /** Cut flowers */
    public const string FLWR_FLWR_CFLW = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FLWR_FLWR_CFLW';

    /** Tropical flowers */
    public const string FLWR_FLWR_TFLW = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FLWR_FLWR_TFLW';

    /** Fresh tulips */
    public const string FLWR_FLWR_TULP = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FLWR_FLWR_TULP';

    /** Fresh mint */
    public const string FLWR_FMNT = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FLWR_FMNT';

    /** Herbs, Leaves and Foliage */
    public const string FLWR_HERBS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FLWR_HERBS';

    /** Plants */
    public const string FLWR_PLNT = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FLWR_PLNT';

    /** Aquatic plants */
    public const string FLWR_PLNT_APLN = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FLWR_PLNT_APLN';

    /** Bulbs and Tubers */
    public const string FLWR_PLNT_BULB = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FLWR_PLNT_BULB';

    /** Medicinal plants */
    public const string FLWR_PLNT_MPLN = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FLWR_PLNT_MPLN';

    /** Tropical plants */
    public const string FLWR_PLNT_TPLN = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FLWR_PLNT_TPLN';

    /** Seeds */
    public const string FLWR_SEED = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FLWR_SEED';

    /** Foodstuffs, Drinks and Tobacco */
    public const string FOOD = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD';

    /** Beverages */
    public const string FOOD_BVRG = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_BVRG';

    /** Beer */
    public const string FOOD_BVRG_BEER = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_BVRG_BEER';

    /** Coffee */
    public const string FOOD_BVRG_COFY = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_BVRG_COFY';

    /** Tea */
    public const string FOOD_BVRG_TEA = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_BVRG_TEA';

    /** Wine */
    public const string FOOD_BVRG_WINE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_BVRG_WINE';

    /** Cereal foods */
    public const string FOOD_CERE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_CERE';

    /** Bread */
    public const string FOOD_CERE_BRED = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_CERE_BRED';

    /** Cakes and Pastries */
    public const string FOOD_CERE_CAKE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_CERE_CAKE';

    /** Dairy products */
    public const string FOOD_DARY = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_DARY';

    /** Cheese */
    public const string FOOD_DARY_CHSE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_DARY_CHSE';

    /** Eggs */
    public const string FOOD_DARY_EGGS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_DARY_EGGS';

    /** Ice cream */
    public const string FOOD_DARY_ICEC = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_DARY_ICEC';

    /** Fish and Seafood */
    public const string FOOD_FISH = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FISH';

    /** Albacora */
    public const string FOOD_FISH_ALBA = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FISH_ALBA';

    /** Caviar */
    public const string FOOD_FISH_CAVR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FISH_CAVR';

    /** Fresh fish */
    public const string FOOD_FISH_FFSH = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FISH_FFSH';

    /** Frozen fish */
    public const string FOOD_FISH_FRZF = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FISH_FRZF';

    /** Frozen seafood */
    public const string FOOD_FISH_FRZS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FISH_FRZS';

    /** Hake */
    public const string FOOD_FISH_HAKE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FISH_HAKE';

    /** Lobsters and Crabs */
    public const string FOOD_FISH_LOBS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FISH_LOBS';

    /** Reineta and Palometa */
    public const string FOOD_FISH_REPA = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FISH_REPA';

    /** Shark fin */
    public const string FOOD_FISH_SFIN = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FISH_SFIN';

    /** Smoked fish */
    public const string FOOD_FISH_SFSH = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FISH_SFSH';

    /** Shrimps and Prawns */
    public const string FOOD_FISH_SHRI = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FISH_SHRI';

    /** Salmon */
    public const string FOOD_FISH_SLMN = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FISH_SLMN';

    /** Tuna */
    public const string FOOD_FISH_TUNA = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FISH_TUNA';

    /** Fruits and Vegetables */
    public const string FOOD_FRTV = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV';

    /** Apples */
    public const string FOOD_FRTV_APPL = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_APPL';

    /** Asparagus */
    public const string FOOD_FRTV_ASPA = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_ASPA';

    /** Avocados */
    public const string FOOD_FRTV_AVOC = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_AVOC';

    /** Bananas */
    public const string FOOD_FRTV_BANA = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_BANA';

    /** String beans */
    public const string FOOD_FRTV_BEAN = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_BEAN';

    /** Berries */
    public const string FOOD_FRTV_BERR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_BERR';

    /** Cherries */
    public const string FOOD_FRTV_CHER = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_CHER';

    /** Cucumber */
    public const string FOOD_FRTV_CMBR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_CMBR';

    /** Durian */
    public const string FOOD_FRTV_DURI = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_DURI';

    /** Garlic */
    public const string FOOD_FRTV_GARL = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_GARL';

    /** Grapes */
    public const string FOOD_FRTV_GRAP = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_GRAP';

    /** Litchies */
    public const string FOOD_FRTV_LITC = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_LITC';

    /** Mangoes */
    public const string FOOD_FRTV_MANG = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_MANG';

    /** Melons */
    public const string FOOD_FRTV_MLNS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_MLNS';

    /** Mushrooms */
    public const string FOOD_FRTV_MUSH = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_MUSH';

    /** Peppers */
    public const string FOOD_FRTV_PEPP = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_PEPP';

    /** Pineapple */
    public const string FOOD_FRTV_PINE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_PINE';

    /** Papaya */
    public const string FOOD_FRTV_PPYA = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_PPYA';

    /** Produce */
    public const string FOOD_FRTV_PROD = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_PROD';

    /** Strawberries */
    public const string FOOD_FRTV_STRW = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_STRW';

    /** Tomatoes */
    public const string FOOD_FRTV_TOMA = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_FRTV_TOMA';

    /** Meat products */
    public const string FOOD_MEAT = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_MEAT';

    /** Beef products */
    public const string FOOD_MEAT_BEEF = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_MEAT_BEEF';

    /** Dried meat */
    public const string FOOD_MEAT_DRIM = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_MEAT_DRIM';

    /** Frozen meat */
    public const string FOOD_MEAT_FRZM = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_MEAT_FRZM';

    /** Goose liver */
    public const string FOOD_MEAT_GOSL = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_MEAT_GOSL';

    /** Guts */
    public const string FOOD_MEAT_GUTS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_MEAT_GUTS';

    /** Horse products */
    public const string FOOD_MEAT_HRSP = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_MEAT_HRSP';

    /** Meat */
    public const string FOOD_MEAT_MEAT = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_MEAT_MEAT';

    /** Pork products */
    public const string FOOD_MEAT_PORK = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_MEAT_PORK';

    /** Sausages */
    public const string FOOD_MEAT_SAUS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_MEAT_SAUS';

    /** Perhishables */
    public const string FOOD_PERI = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_PERI';

    /** Foodstuffs */
    public const string FOOD_STUF = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_STUF';

    /** Catering products */
    public const string FOOD_STUF_CATE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_STUF_CATE';

    /** Chocolate */
    public const string FOOD_STUF_CHOC = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_STUF_CHOC';

    /** Dried fruit */
    public const string FOOD_STUF_DFRU = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_STUF_DFRU';

    /** Milk powder */
    public const string FOOD_STUF_MPWD = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_STUF_MPWD';

    /** Nuts */
    public const string FOOD_STUF_NUTS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_STUF_NUTS';

    /** Olive oil */
    public const string FOOD_STUF_OOIL = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_STUF_OOIL';

    /** Spices and Roots */
    public const string FOOD_STUF_SPCE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_STUF_SPCE';

    /** Tobacco products */
    public const string FOOD_TBCO = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_TBCO';

    /** Cigarettes */
    public const string FOOD_TBCO_CGRT = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_TBCO_CGRT';

    /** Cigars */
    public const string FOOD_TBCO_CIGA = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#FOOD_TBCO_CIGA';

    /** General Cargo */
    public const string GENE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#GENE';

    /** Human Remains */
    public const string HUMR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#HUMR';

    /** Human remains not cremated */
    public const string HUMR_HUMB = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#HUMR_HUMB';

    /** Human remains cremated */
    public const string HUMR_HUMC = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#HUMR_HUMC';

    /** Live Animals */
    public const string LIVE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE';

    /** Birds & Hatching Eggs */
    public const string LIVE_BRDH = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_BRDH';

    /** Birds */
    public const string LIVE_BRDH_BIRD = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_BRDH_BIRD';

    /** Chicks */
    public const string LIVE_BRDH_CHIC = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_BRDH_CHIC';

    /** Ducks */
    public const string LIVE_BRDH_DUCK = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_BRDH_DUCK';

    /** Hatching Eggs */
    public const string LIVE_BRDH_HEGG = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_BRDH_HEGG';

    /** Ostriches */
    public const string LIVE_BRDH_OSTR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_BRDH_OSTR';

    /** Parrots */
    public const string LIVE_BRDH_PARR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_BRDH_PARR';

    /** Turkeys */
    public const string LIVE_BRDH_TRKY = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_BRDH_TRKY';

    /** Insects */
    public const string LIVE_INSC = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_INSC';

    /** Bees */
    public const string LIVE_INSC_BEES = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_INSC_BEES';

    /** Fish */
    public const string LIVE_LFSH = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_LFSH';

    /** Eels */
    public const string LIVE_LFSH_EELS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_LFSH_EELS';

    /** Koi fish */
    public const string LIVE_LFSH_KOIF = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_LFSH_KOIF';

    /** Tropical fish */
    public const string LIVE_LFSH_TRPF = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_LFSH_TRPF';

    /** Mollusks */
    public const string LIVE_MLKS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MLKS';

    /** Lugworms */
    public const string LIVE_MLKS_LUGW = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MLKS_LUGW';

    /** Snails */
    public const string LIVE_MLKS_SNAI = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MLKS_SNAI';

    /** Mammals */
    public const string LIVE_MMLS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS';

    /** Cattle */
    public const string LIVE_MMLS_CATL = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATL';

    /** Cats */
    public const string LIVE_MMLS_CATS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS';

    /** Abyssinian */
    public const string LIVE_MMLS_CATS_Abyssinian = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Abyssinian';

    /** American Bobtail */
    public const string LIVE_MMLS_CATS_American_Bobtail = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_American_Bobtail';

    /** American Curl */
    public const string LIVE_MMLS_CATS_American_Curl = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_American_Curl';

    /** American Keuda */
    public const string LIVE_MMLS_CATS_American_Keuda = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_American_Keuda';

    /** American Lynx */
    public const string LIVE_MMLS_CATS_American_Lynx = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_American_Lynx';

    /** American Polydactyl */
    public const string LIVE_MMLS_CATS_American_Polydactyl = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_American_Polydactyl';

    /** American Shorthair */
    public const string LIVE_MMLS_CATS_American_Shorthair = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_American_Shorthair';

    /** American Wirehair */
    public const string LIVE_MMLS_CATS_American_Wirehair = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_American_Wirehair';

    /** Asian */
    public const string LIVE_MMLS_CATS_Asian = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Asian';

    /** Australian Mist */
    public const string LIVE_MMLS_CATS_Australian_Mist = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Australian_Mist';

    /** Balinese */
    public const string LIVE_MMLS_CATS_Balinese = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Balinese';

    /** Bengal */
    public const string LIVE_MMLS_CATS_Bengal = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Bengal';

    /** Birman */
    public const string LIVE_MMLS_CATS_Birman = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Birman';

    /** Bombay */
    public const string LIVE_MMLS_CATS_Bombay = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Bombay';

    /** Bristol */
    public const string LIVE_MMLS_CATS_Bristol = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Bristol';

    /** British Shorthair */
    public const string LIVE_MMLS_CATS_British_Shorthair = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_British_Shorthair';

    /** Burmese */
    public const string LIVE_MMLS_CATS_Burmese = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Burmese';

    /** California Spangled */
    public const string LIVE_MMLS_CATS_California_Spangled = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_California_Spangled';

    /** Chartreux */
    public const string LIVE_MMLS_CATS_Chartreux = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Chartreux';

    /** Chausie */
    public const string LIVE_MMLS_CATS_Chausie = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Chausie';

    /** Chinese Harlequin */
    public const string LIVE_MMLS_CATS_Chinese_Harlequin = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Chinese_Harlequin';

    /** Color Point Shorthair */
    public const string LIVE_MMLS_CATS_Color_Point_Shorthair = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Color_Point_Shorthair';

    /** Copper */
    public const string LIVE_MMLS_CATS_Copper = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Copper';

    /** Cornish Rex */
    public const string LIVE_MMLS_CATS_Cornish_Rex = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Cornish_Rex';

    /** Cymric */
    public const string LIVE_MMLS_CATS_Cymric = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Cymric';

    /** Desert Lynx */
    public const string LIVE_MMLS_CATS_Desert_Lynx = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Desert_Lynx';

    /** Devon Rex */
    public const string LIVE_MMLS_CATS_Devon_Rex = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Devon_Rex';

    /** Donskoy */
    public const string LIVE_MMLS_CATS_Donskoy = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Donskoy';

    /** Egyptian Mau */
    public const string LIVE_MMLS_CATS_Egyptian_Mau = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Egyptian_Mau';

    /** Exotic Shorthair */
    public const string LIVE_MMLS_CATS_Exotic_Shorthair = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Exotic_Shorthair';

    /** Havana */
    public const string LIVE_MMLS_CATS_Havana = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Havana';

    /** Highland Lynx */
    public const string LIVE_MMLS_CATS_Highland_Lynx = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Highland_Lynx';

    /** Himalayan */
    public const string LIVE_MMLS_CATS_Himalayan = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Himalayan';

    /** Japanese Bobtail */
    public const string LIVE_MMLS_CATS_Japanese_Bobtail = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Japanese_Bobtail';

    /** Javanese */
    public const string LIVE_MMLS_CATS_Javanese = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Javanese';

    /** Korat */
    public const string LIVE_MMLS_CATS_Korat = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Korat';

    /** LaPerm */
    public const string LIVE_MMLS_CATS_LaPerm = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_LaPerm';

    /** Maine Coon */
    public const string LIVE_MMLS_CATS_Maine_Coon = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Maine_Coon';

    /** Manx */
    public const string LIVE_MMLS_CATS_Manx = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Manx';

    /** Mojave Spotted */
    public const string LIVE_MMLS_CATS_Mojave_Spotted = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Mojave_Spotted';

    /** Munchkin */
    public const string LIVE_MMLS_CATS_Munchkin = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Munchkin';

    /** Niebelung */
    public const string LIVE_MMLS_CATS_Niebelung = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Niebelung';

    /** Norwegian Forest */
    public const string LIVE_MMLS_CATS_Norwegian_Forest = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Norwegian_Forest';

    /** Ocicat */
    public const string LIVE_MMLS_CATS_Ocicat = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Ocicat';

    /** Ojos Azules */
    public const string LIVE_MMLS_CATS_Ojos_Azules = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Ojos_Azules';

    /** Oriental */
    public const string LIVE_MMLS_CATS_Oriental = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Oriental';

    /** Pantherette */
    public const string LIVE_MMLS_CATS_Pantherette = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Pantherette';

    /** Persian */
    public const string LIVE_MMLS_CATS_Persian = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Persian';

    /** Peterbald */
    public const string LIVE_MMLS_CATS_Peterbald = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Peterbald';

    /** Pixiebob */
    public const string LIVE_MMLS_CATS_Pixiebob = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Pixiebob';

    /** Ragamuffin */
    public const string LIVE_MMLS_CATS_Ragamuffin = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Ragamuffin';

    /** Ragdoll */
    public const string LIVE_MMLS_CATS_Ragdoll = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Ragdoll';

    /** Russian Blue */
    public const string LIVE_MMLS_CATS_Russian_Blue = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Russian_Blue';

    /** Safari */
    public const string LIVE_MMLS_CATS_Safari = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Safari';

    /** Savannah */
    public const string LIVE_MMLS_CATS_Savannah = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Savannah';

    /** Scottish Fold */
    public const string LIVE_MMLS_CATS_Scottish_Fold = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Scottish_Fold';

    /** Selkirk Rex */
    public const string LIVE_MMLS_CATS_Selkirk_Rex = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Selkirk_Rex';

    /** Serengeti */
    public const string LIVE_MMLS_CATS_Serengeti = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Serengeti';

    /** Siamese */
    public const string LIVE_MMLS_CATS_Siamese = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Siamese';

    /** Siberian */
    public const string LIVE_MMLS_CATS_Siberian = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Siberian';

    /** Singapura */
    public const string LIVE_MMLS_CATS_Singapura = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Singapura';

    /** Snowshoe */
    public const string LIVE_MMLS_CATS_Snowshoe = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Snowshoe';

    /** Somali */
    public const string LIVE_MMLS_CATS_Somali = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Somali';

    /** Sphynx */
    public const string LIVE_MMLS_CATS_Sphynx = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Sphynx';

    /** Tiffany */
    public const string LIVE_MMLS_CATS_Tiffany = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Tiffany';

    /** Tonkinese */
    public const string LIVE_MMLS_CATS_Tonkinese = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Tonkinese';

    /** Traditional Siamese */
    public const string LIVE_MMLS_CATS_Traditional_Siamese = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Traditional_Siamese';

    /** Turkish Angora */
    public const string LIVE_MMLS_CATS_Turkish_Angora = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Turkish_Angora';

    /** Turkish Van */
    public const string LIVE_MMLS_CATS_Turkish_Van = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Turkish_Van';

    /** Vienna Woods */
    public const string LIVE_MMLS_CATS_Vienna_Woods = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Vienna_Woods';

    /** Viverral-Hybrid Cat */
    public const string LIVE_MMLS_CATS_Viverral_Hybrid_Cat = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_Viverral_Hybrid_Cat';

    /** York Chocolate */
    public const string LIVE_MMLS_CATS_York_Chocolate = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_CATS_York_Chocolate';

    /** Dogs */
    public const string LIVE_MMLS_DOGS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS';

    /** Affenpinscher */
    public const string LIVE_MMLS_DOGS_Affenpinscher = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Affenpinscher';

    /** Afghan Hound */
    public const string LIVE_MMLS_DOGS_Afghan_Hound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Afghan_Hound';

    /** Airedale Terrier */
    public const string LIVE_MMLS_DOGS_Airedale_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Airedale_Terrier';

    /** Akita */
    public const string LIVE_MMLS_DOGS_Akita = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Akita';

    /** Alangu Mastiff */
    public const string LIVE_MMLS_DOGS_Alangu_Mastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Alangu_Mastiff';

    /** Alano */
    public const string LIVE_MMLS_DOGS_Alano = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Alano';

    /** Alaskan Malamute */
    public const string LIVE_MMLS_DOGS_Alaskan_Malamute = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Alaskan_Malamute';

    /** American Bulldog */
    public const string LIVE_MMLS_DOGS_American_Bulldog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_American_Bulldog';

    /** American Bully */
    public const string LIVE_MMLS_DOGS_American_Bully = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_American_Bully';

    /** American Cocker Spaniel */
    public const string LIVE_MMLS_DOGS_American_Cocker_Spaniel = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_American_Cocker_Spaniel';

    /** American English Coonhound */
    public const string LIVE_MMLS_DOGS_American_English_Coonhound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_American_English_Coonhound';

    /** American Eskimo Dog-Miniature */
    public const string LIVE_MMLS_DOGS_American_Eskimo_Dog_Miniature = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_American_Eskimo_Dog_Miniature';

    /** American Eskimo Dog-Standard */
    public const string LIVE_MMLS_DOGS_American_Eskimo_Dog_Standard = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_American_Eskimo_Dog_Standard';

    /** American Eskimo Dog-Toy */
    public const string LIVE_MMLS_DOGS_American_Eskimo_Dog_Toy = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_American_Eskimo_Dog_Toy';

    /** American Foxhound */
    public const string LIVE_MMLS_DOGS_American_Foxhound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_American_Foxhound';

    /** American Hairless Terrier */
    public const string LIVE_MMLS_DOGS_American_Hairless_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_American_Hairless_Terrier';

    /** American Pit Bull Terrier */
    public const string LIVE_MMLS_DOGS_American_Pit_Bull_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_American_Pit_Bull_Terrier';

    /** American Staffordshire Terrier */
    public const string LIVE_MMLS_DOGS_American_Staffordshire_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_American_Staffordshire_Terrier';

    /** American Water Spaniel */
    public const string LIVE_MMLS_DOGS_American_Water_Spaniel = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_American_Water_Spaniel';

    /** Anatolian Shepherd Dog */
    public const string LIVE_MMLS_DOGS_Anatolian_Shepherd_Dog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Anatolian_Shepherd_Dog';

    /** Argentinian Mastiff */
    public const string LIVE_MMLS_DOGS_Argentinian_Mastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Argentinian_Mastiff';

    /** Aussiedoodle */
    public const string LIVE_MMLS_DOGS_Aussiedoodle = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Aussiedoodle';

    /** Australian Cattle Dog */
    public const string LIVE_MMLS_DOGS_Australian_Cattle_Dog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Australian_Cattle_Dog';

    /** Australian Shepherd */
    public const string LIVE_MMLS_DOGS_Australian_Shepherd = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Australian_Shepherd';

    /** Australian Terrier */
    public const string LIVE_MMLS_DOGS_Australian_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Australian_Terrier';

    /** Ba Shar-Basset Hound Shar pei Mix */
    public const string LIVE_MMLS_DOGS_Ba_Shar_Basset_Hound_Shar_pei_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Ba_Shar_Basset_Hound_Shar_pei_Mix';

    /** Basenji */
    public const string LIVE_MMLS_DOGS_Basenji = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Basenji';

    /** Basset Hound */
    public const string LIVE_MMLS_DOGS_Basset_Hound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Basset_Hound';

    /** Beagle */
    public const string LIVE_MMLS_DOGS_Beagle = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Beagle';

    /** Bearded Collie */
    public const string LIVE_MMLS_DOGS_Bearded_Collie = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Bearded_Collie';

    /** Beauceron */
    public const string LIVE_MMLS_DOGS_Beauceron = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Beauceron';

    /** Bedlington Terrier */
    public const string LIVE_MMLS_DOGS_Bedlington_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Bedlington_Terrier';

    /** Belgian Malinois */
    public const string LIVE_MMLS_DOGS_Belgian_Malinois = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Belgian_Malinois';

    /** Belgian Sheepdog */
    public const string LIVE_MMLS_DOGS_Belgian_Sheepdog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Belgian_Sheepdog';

    /** Belgian Tervuren */
    public const string LIVE_MMLS_DOGS_Belgian_Tervuren = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Belgian_Tervuren';

    /** Bergamasco */
    public const string LIVE_MMLS_DOGS_Bergamasco = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Bergamasco';

    /** Berger Picard */
    public const string LIVE_MMLS_DOGS_Berger_Picard = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Berger_Picard';

    /** Bernedoodle */
    public const string LIVE_MMLS_DOGS_Bernedoodle = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Bernedoodle';

    /** Bernese Mountain Dog */
    public const string LIVE_MMLS_DOGS_Bernese_Mountain_Dog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Bernese_Mountain_Dog';

    /** Bichon Frise */
    public const string LIVE_MMLS_DOGS_Bichon_Frise = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Bichon_Frise';

    /** Black Russian Terrier */
    public const string LIVE_MMLS_DOGS_Black_Russian_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Black_Russian_Terrier';

    /** Black and Tan Coonhound */
    public const string LIVE_MMLS_DOGS_Black_and_Tan_Coonhound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Black_and_Tan_Coonhound';

    /** Bloodhound */
    public const string LIVE_MMLS_DOGS_Bloodhound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Bloodhound';

    /** Bluetick Coonhound */
    public const string LIVE_MMLS_DOGS_Bluetick_Coonhound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Bluetick_Coonhound';

    /** Boerboel */
    public const string LIVE_MMLS_DOGS_Boerboel = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Boerboel';

    /** Border Collie */
    public const string LIVE_MMLS_DOGS_Border_Collie = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Border_Collie';

    /** Border Terrier */
    public const string LIVE_MMLS_DOGS_Border_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Border_Terrier';

    /** Borzoi */
    public const string LIVE_MMLS_DOGS_Borzoi = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Borzoi';

    /** Boston Terrier */
    public const string LIVE_MMLS_DOGS_Boston_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Boston_Terrier';

    /** Bouvier des Flandres */
    public const string LIVE_MMLS_DOGS_Bouvier_des_Flandres = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Bouvier_des_Flandres';

    /** Boweimar-Boxer Weimaraner Mix */
    public const string LIVE_MMLS_DOGS_Boweimar_Boxer_Weimaraner_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Boweimar_Boxer_Weimaraner_Mix';

    /** Boxer */
    public const string LIVE_MMLS_DOGS_Boxer = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Boxer';

    /** Boykin Spaniel */
    public const string LIVE_MMLS_DOGS_Boykin_Spaniel = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Boykin_Spaniel';

    /** Brazilian Mastiff */
    public const string LIVE_MMLS_DOGS_Brazilian_Mastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Brazilian_Mastiff';

    /** Briard */
    public const string LIVE_MMLS_DOGS_Briard = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Briard';

    /** Brittany */
    public const string LIVE_MMLS_DOGS_Brittany = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Brittany';

    /** Brussels Griffon */
    public const string LIVE_MMLS_DOGS_Brussels_Griffon = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Brussels_Griffon';

    /** Bull Terrier */
    public const string LIVE_MMLS_DOGS_Bull_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Bull_Terrier';

    /** Bull Terrier-Miniature */
    public const string LIVE_MMLS_DOGS_Bull_Terrier_Miniature = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Bull_Terrier_Miniature';

    /** Bulldog */
    public const string LIVE_MMLS_DOGS_Bulldog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Bulldog';

    /** Bulli Kutta */
    public const string LIVE_MMLS_DOGS_Bulli_Kutta = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Bulli_Kutta';

    /** Bullmastiff */
    public const string LIVE_MMLS_DOGS_Bullmastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Bullmastiff';

    /** Bully Kutta-Mastiff breed */
    public const string LIVE_MMLS_DOGS_Bully_Kutta_Mastiff_breed = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Bully_Kutta_Mastiff_breed';

    /** Cairn Terrier */
    public const string LIVE_MMLS_DOGS_Cairn_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Cairn_Terrier';

    /** Campeiro Bulldog-Brazilian Bulldog */
    public const string LIVE_MMLS_DOGS_Campeiro_Bulldog_Brazilian_Bulldog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Campeiro_Bulldog_Brazilian_Bulldog';

    /** Canaan Dog */
    public const string LIVE_MMLS_DOGS_Canaan_Dog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Canaan_Dog';

    /** Canary Mastiff */
    public const string LIVE_MMLS_DOGS_Canary_Mastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Canary_Mastiff';

    /** Cane Corso */
    public const string LIVE_MMLS_DOGS_Cane_Corso = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Cane_Corso';

    /** Cardigan Welsh Corgi */
    public const string LIVE_MMLS_DOGS_Cardigan_Welsh_Corgi = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Cardigan_Welsh_Corgi';

    /** Catahoula Bulldog-Catahoula Leopard Bulldog */
    public const string LIVE_MMLS_DOGS_Catahoula_Bulldog_Catahoula_Leopard_Bulldog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Catahoula_Bulldog_Catahoula_Leopard_Bulldog';

    /** Cavachon-King Charles Spaniel Bichon Frise */
    public const string LIVE_MMLS_DOGS_Cavachon_King_Charles_Spaniel_Bichon_Frise = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Cavachon_King_Charles_Spaniel_Bichon_Frise';

    /** Cavalier King Charles Spaniel */
    public const string LIVE_MMLS_DOGS_Cavalier_King_Charles_Spaniel = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Cavalier_King_Charles_Spaniel';

    /** Cavapoo-Cavalier King Charles Spaniel Poodle */
    public const string LIVE_MMLS_DOGS_Cavapoo_Cavalier_King_Charles_Spaniel_Poodle = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Cavapoo_Cavalier_King_Charles_Spaniel_Poodle';

    /** Cesky Terrier */
    public const string LIVE_MMLS_DOGS_Cesky_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Cesky_Terrier';

    /** Chesapeake Bay Retriever */
    public const string LIVE_MMLS_DOGS_Chesapeake_Bay_Retriever = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Chesapeake_Bay_Retriever';

    /** Chihuahua */
    public const string LIVE_MMLS_DOGS_Chihuahua = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Chihuahua';

    /** Chinese Crested Dog */
    public const string LIVE_MMLS_DOGS_Chinese_Crested_Dog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Chinese_Crested_Dog';

    /** Chinese Pug */
    public const string LIVE_MMLS_DOGS_Chinese_Pug = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Chinese_Pug';

    /** Chinese Shar Pei */
    public const string LIVE_MMLS_DOGS_Chinese_Shar_Pei = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Chinese_Shar_Pei';

    /** Chinook */
    public const string LIVE_MMLS_DOGS_Chinook = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Chinook';

    /** Chipin-Chihuahua Minature Pinscher Mix */
    public const string LIVE_MMLS_DOGS_Chipin_Chihuahua_Minature_Pinscher_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Chipin_Chihuahua_Minature_Pinscher_Mix';

    /** Chiweenie-Chihuahua Dachshund Mix */
    public const string LIVE_MMLS_DOGS_Chiweenie_Chihuahua_Dachshund_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Chiweenie_Chihuahua_Dachshund_Mix';

    /** Chorkie-Chihuahua Yorkshire Terrier Mix */
    public const string LIVE_MMLS_DOGS_Chorkie_Chihuahua_Yorkshire_Terrier_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Chorkie_Chihuahua_Yorkshire_Terrier_Mix';

    /** Chow Chow */
    public const string LIVE_MMLS_DOGS_Chow_Chow = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Chow_Chow';

    /** Chow Pei-Chow Chow Shar Pei Mix */
    public const string LIVE_MMLS_DOGS_Chow_Pei_Chow_Chow_Shar_Pei_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Chow_Pei_Chow_Chow_Shar_Pei_Mix';

    /** Cirneco dell Etna */
    public const string LIVE_MMLS_DOGS_Cirneco_dell_Etna = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Cirneco_dell_Etna';

    /** Clumber Spaniel */
    public const string LIVE_MMLS_DOGS_Clumber_Spaniel = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Clumber_Spaniel';

    /** Cockapoo-Cocker Spaniel Poodle Mix */
    public const string LIVE_MMLS_DOGS_Cockapoo_Cocker_Spaniel_Poodle_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Cockapoo_Cocker_Spaniel_Poodle_Mix';

    /** Cocker Spaniel */
    public const string LIVE_MMLS_DOGS_Cocker_Spaniel = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Cocker_Spaniel';

    /** Collie */
    public const string LIVE_MMLS_DOGS_Collie = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Collie';

    /** Coton de Tulear */
    public const string LIVE_MMLS_DOGS_Coton_de_Tulear = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Coton_de_Tulear';

    /** Curly-Coated Retriever */
    public const string LIVE_MMLS_DOGS_Curly_Coated_Retriever = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Curly_Coated_Retriever';

    /** Dachshund */
    public const string LIVE_MMLS_DOGS_Dachshund = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Dachshund';

    /** Dalmatian */
    public const string LIVE_MMLS_DOGS_Dalmatian = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Dalmatian';

    /** Dandie Dinmont Terrier */
    public const string LIVE_MMLS_DOGS_Dandie_Dinmont_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Dandie_Dinmont_Terrier';

    /** Doberman Pinscher */
    public const string LIVE_MMLS_DOGS_Doberman_Pinscher = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Doberman_Pinscher';

    /** Dogo Argentino */
    public const string LIVE_MMLS_DOGS_Dogo_Argentino = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Dogo_Argentino';

    /** Dogue de Bordeaux */
    public const string LIVE_MMLS_DOGS_Dogue_de_Bordeaux = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Dogue_de_Bordeaux';

    /** Doxiepoo-Dachshund Poodle Mix */
    public const string LIVE_MMLS_DOGS_Doxiepoo_Dachshund_Poodle_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Doxiepoo_Dachshund_Poodle_Mix';

    /** Dutch Pug */
    public const string LIVE_MMLS_DOGS_Dutch_Pug = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Dutch_Pug';

    /** English Bulldog */
    public const string LIVE_MMLS_DOGS_English_Bulldog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_English_Bulldog';

    /** English Cocker Spaniel */
    public const string LIVE_MMLS_DOGS_English_Cocker_Spaniel = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_English_Cocker_Spaniel';

    /** English Foxhound */
    public const string LIVE_MMLS_DOGS_English_Foxhound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_English_Foxhound';

    /** English Mastiff */
    public const string LIVE_MMLS_DOGS_English_Mastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_English_Mastiff';

    /** English Setter */
    public const string LIVE_MMLS_DOGS_English_Setter = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_English_Setter';

    /** English Springer Spaniel */
    public const string LIVE_MMLS_DOGS_English_Springer_Spaniel = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_English_Springer_Spaniel';

    /** English Toy Spaniel */
    public const string LIVE_MMLS_DOGS_English_Toy_Spaniel = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_English_Toy_Spaniel';

    /** Entlebucher Mountain Dog */
    public const string LIVE_MMLS_DOGS_Entlebucher_Mountain_Dog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Entlebucher_Mountain_Dog';

    /** Eurasier */
    public const string LIVE_MMLS_DOGS_Eurasier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Eurasier';

    /** Field Spaniel */
    public const string LIVE_MMLS_DOGS_Field_Spaniel = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Field_Spaniel';

    /** Fila Brasileiro-Brazilian Mastiff */
    public const string LIVE_MMLS_DOGS_Fila_Brasileiro_Brazilian_Mastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Fila_Brasileiro_Brazilian_Mastiff';

    /** Finnish Lapphund */
    public const string LIVE_MMLS_DOGS_Finnish_Lapphund = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Finnish_Lapphund';

    /** Finnish Spitz */
    public const string LIVE_MMLS_DOGS_Finnish_Spitz = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Finnish_Spitz';

    /** Flat-Coated Retriever */
    public const string LIVE_MMLS_DOGS_Flat_Coated_Retriever = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Flat_Coated_Retriever';

    /** French Bulldog */
    public const string LIVE_MMLS_DOGS_French_Bulldog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_French_Bulldog';

    /** French Mastiff */
    public const string LIVE_MMLS_DOGS_French_Mastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_French_Mastiff';

    /** German Mastiff-Great Dane */
    public const string LIVE_MMLS_DOGS_German_Mastiff_Great_Dane = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_German_Mastiff_Great_Dane';

    /** German Pinscher */
    public const string LIVE_MMLS_DOGS_German_Pinscher = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_German_Pinscher';

    /** German Shepherd Dog */
    public const string LIVE_MMLS_DOGS_German_Shepherd_Dog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_German_Shepherd_Dog';

    /** German Shorthaired Pointer */
    public const string LIVE_MMLS_DOGS_German_Shorthaired_Pointer = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_German_Shorthaired_Pointer';

    /** German Wirehaired Pointer */
    public const string LIVE_MMLS_DOGS_German_Wirehaired_Pointer = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_German_Wirehaired_Pointer';

    /** Giant Schnauzer */
    public const string LIVE_MMLS_DOGS_Giant_Schnauzer = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Giant_Schnauzer';

    /** Glen of Imaal Terrier */
    public const string LIVE_MMLS_DOGS_Glen_of_Imaal_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Glen_of_Imaal_Terrier';

    /** Goldador-Golden Retriever Labrador Retriever */
    public const string LIVE_MMLS_DOGS_Goldador_Golden_Retriever_Labrador_Retriever = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Goldador_Golden_Retriever_Labrador_Retriever';

    /** Golden Retriever */
    public const string LIVE_MMLS_DOGS_Golden_Retriever = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Golden_Retriever';

    /** Goldendoodle-Golden Retriever Poodle Mix */
    public const string LIVE_MMLS_DOGS_Goldendoodle_Golden_Retriever_Poodle_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Goldendoodle_Golden_Retriever_Poodle_Mix';

    /** Gordon Setter */
    public const string LIVE_MMLS_DOGS_Gordon_Setter = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Gordon_Setter';

    /** Great Dane */
    public const string LIVE_MMLS_DOGS_Great_Dane = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Great_Dane';

    /** Great Pyrenees */
    public const string LIVE_MMLS_DOGS_Great_Pyrenees = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Great_Pyrenees';

    /** Greater Swiss Mountain Dog */
    public const string LIVE_MMLS_DOGS_Greater_Swiss_Mountain_Dog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Greater_Swiss_Mountain_Dog';

    /** Greyhound */
    public const string LIVE_MMLS_DOGS_Greyhound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Greyhound';

    /** Harrier */
    public const string LIVE_MMLS_DOGS_Harrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Harrier';

    /** Havanese */
    public const string LIVE_MMLS_DOGS_Havanese = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Havanese';

    /** Ibizan Hound */
    public const string LIVE_MMLS_DOGS_Ibizan_Hound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Ibizan_Hound';

    /** Icelandic Sheepdog */
    public const string LIVE_MMLS_DOGS_Icelandic_Sheepdog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Icelandic_Sheepdog';

    /** Irish Red and White Setter */
    public const string LIVE_MMLS_DOGS_Irish_Red_and_White_Setter = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Irish_Red_and_White_Setter';

    /** Irish Setter */
    public const string LIVE_MMLS_DOGS_Irish_Setter = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Irish_Setter';

    /** Irish Terrier */
    public const string LIVE_MMLS_DOGS_Irish_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Irish_Terrier';

    /** Irish Water Spaniel */
    public const string LIVE_MMLS_DOGS_Irish_Water_Spaniel = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Irish_Water_Spaniel';

    /** Irish Wolfhound */
    public const string LIVE_MMLS_DOGS_Irish_Wolfhound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Irish_Wolfhound';

    /** Italian Greyhound */
    public const string LIVE_MMLS_DOGS_Italian_Greyhound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Italian_Greyhound';

    /** Italian Mastiff */
    public const string LIVE_MMLS_DOGS_Italian_Mastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Italian_Mastiff';

    /** Jack Chi-Chihuahua Jack Russell Terrier Mix */
    public const string LIVE_MMLS_DOGS_Jack_Chi_Chihuahua_Jack_Russell_Terrier_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Jack_Chi_Chihuahua_Jack_Russell_Terrier_Mix';

    /** Jack Russell Terrier */
    public const string LIVE_MMLS_DOGS_Jack_Russell_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Jack_Russell_Terrier';

    /** Japanese Boxer */
    public const string LIVE_MMLS_DOGS_Japanese_Boxer = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Japanese_Boxer';

    /** Japanese Chin */
    public const string LIVE_MMLS_DOGS_Japanese_Chin = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Japanese_Chin';

    /** Japanese Mastiff */
    public const string LIVE_MMLS_DOGS_Japanese_Mastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Japanese_Mastiff';

    /** Japanese Pug */
    public const string LIVE_MMLS_DOGS_Japanese_Pug = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Japanese_Pug';

    /** Japanese Spaniel */
    public const string LIVE_MMLS_DOGS_Japanese_Spaniel = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Japanese_Spaniel';

    /** Kangal Shepherd Dog */
    public const string LIVE_MMLS_DOGS_Kangal_Shepherd_Dog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Kangal_Shepherd_Dog';

    /** Keeshond */
    public const string LIVE_MMLS_DOGS_Keeshond = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Keeshond';

    /** Kerry Blue Terrier */
    public const string LIVE_MMLS_DOGS_Kerry_Blue_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Kerry_Blue_Terrier';

    /** King Charles Spaniel */
    public const string LIVE_MMLS_DOGS_King_Charles_Spaniel = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_King_Charles_Spaniel';

    /** Komondor */
    public const string LIVE_MMLS_DOGS_Komondor = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Komondor';

    /** Kuvasz */
    public const string LIVE_MMLS_DOGS_Kuvasz = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Kuvasz';

    /** Kyi-Leo-Maltese Lhasa Apso Mix */
    public const string LIVE_MMLS_DOGS_Kyi_Leo_Maltese_Lhasa_Apso_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Kyi_Leo_Maltese_Lhasa_Apso_Mix';

    /** Labrabull-Labrador Retriever American Pit Bull */
    public const string LIVE_MMLS_DOGS_Labrabull_Labrador_Retriever_American_Pit_Bull = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Labrabull_Labrador_Retriever_American_Pit_Bull';

    /** Labradane-Labrador Retriever Great Dane Mix */
    public const string LIVE_MMLS_DOGS_Labradane_Labrador_Retriever_Great_Dane_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Labradane_Labrador_Retriever_Great_Dane_Mix';

    /** Labradoodle Labrador Retriever Poodle Mix */
    public const string LIVE_MMLS_DOGS_Labradoodle_Labrador_Retriever_Poodle_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Labradoodle_Labrador_Retriever_Poodle_Mix';

    /** Labrador Retriever */
    public const string LIVE_MMLS_DOGS_Labrador_Retriever = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Labrador_Retriever';

    /** Lagotto Romagnolo */
    public const string LIVE_MMLS_DOGS_Lagotto_Romagnolo = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Lagotto_Romagnolo';

    /** Lakeland Terrier */
    public const string LIVE_MMLS_DOGS_Lakeland_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Lakeland_Terrier';

    /** Leonberger */
    public const string LIVE_MMLS_DOGS_Leonberger = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Leonberger';

    /** Lhasa Apso */
    public const string LIVE_MMLS_DOGS_Lhasa_Apso = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Lhasa_Apso';

    /** Löwchen */
    public const string LIVE_MMLS_DOGS_Löwchen = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Löwchen';

    /** Mal-Shi-Maltese Shih Tzu Mix */
    public const string LIVE_MMLS_DOGS_Mal_Shi_Maltese_Shih_Tzu_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Mal_Shi_Maltese_Shih_Tzu_Mix';

    /** Malt-Tzu-Maltese Shih Tzu Mix */
    public const string LIVE_MMLS_DOGS_Malt_Tzu_Maltese_Shih_Tzu_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Malt_Tzu_Maltese_Shih_Tzu_Mix';

    /** Maltese */
    public const string LIVE_MMLS_DOGS_Maltese = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Maltese';

    /** Maltese Shih Tzu */
    public const string LIVE_MMLS_DOGS_Maltese_Shih_Tzu = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Maltese_Shih_Tzu';

    /** Malti Zu-Maltese Shih Tzu Mix */
    public const string LIVE_MMLS_DOGS_Malti_Zu_Maltese_Shih_Tzu_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Malti_Zu_Maltese_Shih_Tzu_Mix';

    /** Maltipoo-Maltese Poodle Mix */
    public const string LIVE_MMLS_DOGS_Maltipoo_Maltese_Poodle_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Maltipoo_Maltese_Poodle_Mix';

    /** Manchester Terrier */
    public const string LIVE_MMLS_DOGS_Manchester_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Manchester_Terrier';

    /** Mastador-Bullmastiff Labrador Retriever Mix */
    public const string LIVE_MMLS_DOGS_Mastador_Bullmastiff_Labrador_Retriever_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Mastador_Bullmastiff_Labrador_Retriever_Mix';

    /** Mastiff */
    public const string LIVE_MMLS_DOGS_Mastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Mastiff';

    /** Mastin Espanol-Spanish Mastiff */
    public const string LIVE_MMLS_DOGS_Mastin_Espanol_Spanish_Mastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Mastin_Espanol_Spanish_Mastiff';

    /** Mastino Napoletano-Neopolitan Mastiff */
    public const string LIVE_MMLS_DOGS_Mastino_Napoletano_Neopolitan_Mastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Mastino_Napoletano_Neopolitan_Mastiff';

    /** Miniature American Shepherd */
    public const string LIVE_MMLS_DOGS_Miniature_American_Shepherd = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Miniature_American_Shepherd';

    /** Miniature Bull Terrier */
    public const string LIVE_MMLS_DOGS_Miniature_Bull_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Miniature_Bull_Terrier';

    /** Miniature Pinscher */
    public const string LIVE_MMLS_DOGS_Miniature_Pinscher = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Miniature_Pinscher';

    /** Miniature Schnauzer */
    public const string LIVE_MMLS_DOGS_Miniature_Schnauzer = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Miniature_Schnauzer';

    /** Mixed-Invalid Breed Type */
    public const string LIVE_MMLS_DOGS_Mixed_Invalid_Breed_Type = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Mixed_Invalid_Breed_Type';

    /** Neapolitan Mastiff */
    public const string LIVE_MMLS_DOGS_Neapolitan_Mastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Neapolitan_Mastiff';

    /** Newfoundland */
    public const string LIVE_MMLS_DOGS_Newfoundland = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Newfoundland';

    /** Norfolk Terrier */
    public const string LIVE_MMLS_DOGS_Norfolk_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Norfolk_Terrier';

    /** Norwegian Buhund */
    public const string LIVE_MMLS_DOGS_Norwegian_Buhund = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Norwegian_Buhund';

    /** Norwegian Elkhound */
    public const string LIVE_MMLS_DOGS_Norwegian_Elkhound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Norwegian_Elkhound';

    /** Norwegian Lundehund */
    public const string LIVE_MMLS_DOGS_Norwegian_Lundehund = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Norwegian_Lundehund';

    /** Norwich Terrier */
    public const string LIVE_MMLS_DOGS_Norwich_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Norwich_Terrier';

    /** Nova Scotia Duck-Tolling Retriever */
    public const string LIVE_MMLS_DOGS_Nova_Scotia_Duck_Tolling_Retriever = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Nova_Scotia_Duck_Tolling_Retriever';

    /** Old English Bulldog */
    public const string LIVE_MMLS_DOGS_Old_English_Bulldog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Old_English_Bulldog';

    /** Old English Sheepdog */
    public const string LIVE_MMLS_DOGS_Old_English_Sheepdog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Old_English_Sheepdog';

    /** Olde English Bulldog */
    public const string LIVE_MMLS_DOGS_Olde_English_Bulldog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Olde_English_Bulldog';

    /** Otterhound */
    public const string LIVE_MMLS_DOGS_Otterhound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Otterhound';

    /** Papillon */
    public const string LIVE_MMLS_DOGS_Papillon = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Papillon';

    /** Parson Russell Terrier */
    public const string LIVE_MMLS_DOGS_Parson_Russell_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Parson_Russell_Terrier';

    /** Peekapoo-Pekingese Poodle Mix */
    public const string LIVE_MMLS_DOGS_Peekapoo_Pekingese_Poodle_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Peekapoo_Pekingese_Poodle_Mix';

    /** Pekingese */
    public const string LIVE_MMLS_DOGS_Pekingese = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Pekingese';

    /** Pembroke Welsh Corgi */
    public const string LIVE_MMLS_DOGS_Pembroke_Welsh_Corgi = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Pembroke_Welsh_Corgi';

    /** Petit Basset Griffon Vendéen */
    public const string LIVE_MMLS_DOGS_Petit_Basset_Griffon_Vendéen = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Petit_Basset_Griffon_Vendéen';

    /** Pharaoh Hound */
    public const string LIVE_MMLS_DOGS_Pharaoh_Hound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Pharaoh_Hound';

    /** Pit Bull */
    public const string LIVE_MMLS_DOGS_Pit_Bull = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Pit_Bull';

    /** Pit Plott-Pitbull Plott Hound Mix */
    public const string LIVE_MMLS_DOGS_Pit_Plott_Pitbull_Plott_Hound_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Pit_Plott_Pitbull_Plott_Hound_Mix';

    /** Plott Hound */
    public const string LIVE_MMLS_DOGS_Plott_Hound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Plott_Hound';

    /** Pointer */
    public const string LIVE_MMLS_DOGS_Pointer = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Pointer';

    /** Polish Lowland Sheepdog */
    public const string LIVE_MMLS_DOGS_Polish_Lowland_Sheepdog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Polish_Lowland_Sheepdog';

    /** Pomapoo-Pomeranian Poodle Mix */
    public const string LIVE_MMLS_DOGS_Pomapoo_Pomeranian_Poodle_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Pomapoo_Pomeranian_Poodle_Mix';

    /** Pomchi-Pomeranian Chihuahua Mix */
    public const string LIVE_MMLS_DOGS_Pomchi_Pomeranian_Chihuahua_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Pomchi_Pomeranian_Chihuahua_Mix';

    /** Pomeranian */
    public const string LIVE_MMLS_DOGS_Pomeranian = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Pomeranian';

    /** Pomsky-Pomeranian Siberian Husky Mix */
    public const string LIVE_MMLS_DOGS_Pomsky_Pomeranian_Siberian_Husky_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Pomsky_Pomeranian_Siberian_Husky_Mix';

    /** Poochon-Poodle Bichon Frise Mix */
    public const string LIVE_MMLS_DOGS_Poochon_Poodle_Bichon_Frise_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Poochon_Poodle_Bichon_Frise_Mix';

    /** Poodle */
    public const string LIVE_MMLS_DOGS_Poodle = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Poodle';

    /** Portuguese Podengo Pequeno */
    public const string LIVE_MMLS_DOGS_Portuguese_Podengo_Pequeno = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Portuguese_Podengo_Pequeno';

    /** Portuguese Water Dog */
    public const string LIVE_MMLS_DOGS_Portuguese_Water_Dog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Portuguese_Water_Dog';

    /** Presa Canario */
    public const string LIVE_MMLS_DOGS_Presa_Canario = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Presa_Canario';

    /** Pug */
    public const string LIVE_MMLS_DOGS_Pug = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Pug';

    /** Pugapoo-Pug Poodle Mix */
    public const string LIVE_MMLS_DOGS_Pugapoo_Pug_Poodle_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Pugapoo_Pug_Poodle_Mix';

    /** Puggle-Pug Beagle Mix */
    public const string LIVE_MMLS_DOGS_Puggle_Pug_Beagle_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Puggle_Pug_Beagle_Mix';

    /** Puli */
    public const string LIVE_MMLS_DOGS_Puli = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Puli';

    /** Pyrenean Mastiff */
    public const string LIVE_MMLS_DOGS_Pyrenean_Mastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Pyrenean_Mastiff';

    /** Pyrenean Shepherd */
    public const string LIVE_MMLS_DOGS_Pyrenean_Shepherd = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Pyrenean_Shepherd';

    /** Rat Terrier */
    public const string LIVE_MMLS_DOGS_Rat_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Rat_Terrier';

    /** Redbone Coonhound */
    public const string LIVE_MMLS_DOGS_Redbone_Coonhound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Redbone_Coonhound';

    /** Rhodesian Ridgeback */
    public const string LIVE_MMLS_DOGS_Rhodesian_Ridgeback = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Rhodesian_Ridgeback';

    /** Rottweiler */
    public const string LIVE_MMLS_DOGS_Rottweiler = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Rottweiler';

    /** Russell Terrier */
    public const string LIVE_MMLS_DOGS_Russell_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Russell_Terrier';

    /** Saint Bernard */
    public const string LIVE_MMLS_DOGS_Saint_Bernard = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Saint_Bernard';

    /** Saluki */
    public const string LIVE_MMLS_DOGS_Saluki = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Saluki';

    /** Samoyed */
    public const string LIVE_MMLS_DOGS_Samoyed = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Samoyed';

    /** Schipperke */
    public const string LIVE_MMLS_DOGS_Schipperke = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Schipperke';

    /** Schnoodle-Schnauzer Poodle Mix */
    public const string LIVE_MMLS_DOGS_Schnoodle_Schnauzer_Poodle_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Schnoodle_Schnauzer_Poodle_Mix';

    /** Scottish Deerhound */
    public const string LIVE_MMLS_DOGS_Scottish_Deerhound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Scottish_Deerhound';

    /** Scottish Terrier */
    public const string LIVE_MMLS_DOGS_Scottish_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Scottish_Terrier';

    /** Sealyham Terrier */
    public const string LIVE_MMLS_DOGS_Sealyham_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Sealyham_Terrier';

    /** Shar Pei */
    public const string LIVE_MMLS_DOGS_Shar_Pei = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Shar_Pei';

    /** Sheepadoodle-Old English Sheepdog Poodle Mix */
    public const string LIVE_MMLS_DOGS_Sheepadoodle_Old_English_Sheepdog_Poodle_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Sheepadoodle_Old_English_Sheepdog_Poodle_Mix';

    /** Shetland Sheepdog */
    public const string LIVE_MMLS_DOGS_Shetland_Sheepdog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Shetland_Sheepdog';

    /** Shiba Inu */
    public const string LIVE_MMLS_DOGS_Shiba_Inu = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Shiba_Inu';

    /** Shih-Poo */
    public const string LIVE_MMLS_DOGS_Shih_Poo = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Shih_Poo';

    /** Shih Tzu */
    public const string LIVE_MMLS_DOGS_Shih_Tzu = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Shih_Tzu';

    /** Shihpoo-Shih Tzu Poodle Mix */
    public const string LIVE_MMLS_DOGS_Shihpoo_Shih_Tzu_Poodle_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Shihpoo_Shih_Tzu_Poodle_Mix';

    /** Siberian Husky */
    public const string LIVE_MMLS_DOGS_Siberian_Husky = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Siberian_Husky';

    /** Silky Terrier */
    public const string LIVE_MMLS_DOGS_Silky_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Silky_Terrier';

    /** Skye Terrier */
    public const string LIVE_MMLS_DOGS_Skye_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Skye_Terrier';

    /** Sloughi-Arabian Greyhound */
    public const string LIVE_MMLS_DOGS_Sloughi_Arabian_Greyhound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Sloughi_Arabian_Greyhound';

    /** Smooth Fox Terrier */
    public const string LIVE_MMLS_DOGS_Smooth_Fox_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Smooth_Fox_Terrier';

    /** Soft-Coated Wheaten Terrier */
    public const string LIVE_MMLS_DOGS_Soft_Coated_Wheaten_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Soft_Coated_Wheaten_Terrier';

    /** South African Mastiff */
    public const string LIVE_MMLS_DOGS_South_African_Mastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_South_African_Mastiff';

    /** Spanish Mastiff */
    public const string LIVE_MMLS_DOGS_Spanish_Mastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Spanish_Mastiff';

    /** Spanish Water Dog */
    public const string LIVE_MMLS_DOGS_Spanish_Water_Dog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Spanish_Water_Dog';

    /** Spinone Italiano */
    public const string LIVE_MMLS_DOGS_Spinone_Italiano = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Spinone_Italiano';

    /** Staffordshire Bull Terrier */
    public const string LIVE_MMLS_DOGS_Staffordshire_Bull_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Staffordshire_Bull_Terrier';

    /** Standard Schnauzer */
    public const string LIVE_MMLS_DOGS_Standard_Schnauzer = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Standard_Schnauzer';

    /** Sussex Spaniel */
    public const string LIVE_MMLS_DOGS_Sussex_Spaniel = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Sussex_Spaniel';

    /** Swedish Vallhund */
    public const string LIVE_MMLS_DOGS_Swedish_Vallhund = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Swedish_Vallhund';

    /** Tibetan Mastiff */
    public const string LIVE_MMLS_DOGS_Tibetan_Mastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Tibetan_Mastiff';

    /** Tibetan Spaniel */
    public const string LIVE_MMLS_DOGS_Tibetan_Spaniel = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Tibetan_Spaniel';

    /** Tibetan Terrier */
    public const string LIVE_MMLS_DOGS_Tibetan_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Tibetan_Terrier';

    /** Tosa-Japanese Mastiff */
    public const string LIVE_MMLS_DOGS_Tosa_Japanese_Mastiff = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Tosa_Japanese_Mastiff';

    /** Toy Fox Terrier */
    public const string LIVE_MMLS_DOGS_Toy_Fox_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Toy_Fox_Terrier';

    /** Treeing Walker Coonhound */
    public const string LIVE_MMLS_DOGS_Treeing_Walker_Coonhound = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Treeing_Walker_Coonhound';

    /** Utonagan-Northern Inuit Dog */
    public const string LIVE_MMLS_DOGS_Utonagan_Northern_Inuit_Dog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Utonagan_Northern_Inuit_Dog';

    /** Valley Bulldog */
    public const string LIVE_MMLS_DOGS_Valley_Bulldog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Valley_Bulldog';

    /** Vizsla */
    public const string LIVE_MMLS_DOGS_Vizsla = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Vizsla';

    /** Weimaraner */
    public const string LIVE_MMLS_DOGS_Weimaraner = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Weimaraner';

    /** Welsh Springer Spaniel */
    public const string LIVE_MMLS_DOGS_Welsh_Springer_Spaniel = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Welsh_Springer_Spaniel';

    /** Welsh Terrier */
    public const string LIVE_MMLS_DOGS_Welsh_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Welsh_Terrier';

    /** West Highland White Terrier */
    public const string LIVE_MMLS_DOGS_West_Highland_White_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_West_Highland_White_Terrier';

    /** Whippet */
    public const string LIVE_MMLS_DOGS_Whippet = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Whippet';

    /** Wire Fox Terrier */
    public const string LIVE_MMLS_DOGS_Wire_Fox_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Wire_Fox_Terrier';

    /** Wirehaired Pointing Griffon */
    public const string LIVE_MMLS_DOGS_Wirehaired_Pointing_Griffon = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Wirehaired_Pointing_Griffon';

    /** Wirehaired Vizsla */
    public const string LIVE_MMLS_DOGS_Wirehaired_Vizsla = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Wirehaired_Vizsla';

    /** Xoloitzcuintli-Mexican Hairless Dog */
    public const string LIVE_MMLS_DOGS_Xoloitzcuintli_Mexican_Hairless_Dog = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Xoloitzcuintli_Mexican_Hairless_Dog';

    /** Yorkipoo-Yorkshire Terrier Poodle Mix */
    public const string LIVE_MMLS_DOGS_Yorkipoo_Yorkshire_Terrier_Poodle_Mix = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Yorkipoo_Yorkshire_Terrier_Poodle_Mix';

    /** Yorkshire Terrier */
    public const string LIVE_MMLS_DOGS_Yorkshire_Terrier = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_DOGS_Yorkshire_Terrier';

    /** Ferrets */
    public const string LIVE_MMLS_FERR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_FERR';

    /** Goats */
    public const string LIVE_MMLS_GOAT = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_GOAT';

    /** Horses */
    public const string LIVE_MMLS_HORS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_HORS';

    /** Monkeys */
    public const string LIVE_MMLS_MNKY = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_MNKY';

    /** Pigs */
    public const string LIVE_MMLS_PIGS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_PIGS';

    /** Rodents */
    public const string LIVE_MMLS_RDNT = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_RDNT';

    /** Sheep */
    public const string LIVE_MMLS_SHEP = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_MMLS_SHEP';

    /** Reptiles */
    public const string LIVE_REPT = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_REPT';

    /** Venomous animals */
    public const string LIVE_VANI = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_VANI';

    /** Zoo animals */
    public const string LIVE_ZOOA = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#LIVE_ZOOA';

    /** Mail */
    public const string MAIL = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#MAIL';

    /** Musical Instruments & Art */
    public const string MART = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#MART';

    /** Art */
    public const string MART_ART = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#MART_ART';

    /** Engraving */
    public const string MART_ENGR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#MART_ENGR';

    /** Handicraft products */
    public const string MART_HAND = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#MART_HAND';

    /** Musical equipment */
    public const string MART_MUEQ = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#MART_MUEQ';

    /** Musical instruments */
    public const string MART_MUSI = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#MART_MUSI';

    /** Painting */
    public const string MART_PNTG = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#MART_PNTG';

    /** Military, Weapons and Ammunition */
    public const string MLTY = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#MLTY';

    /** Munitions */
    public const string MLTY_AMUN = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#MLTY_AMUN';

    /** Military supplies */
    public const string MLTY_MSUP = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#MLTY_MSUP';

    /** Sporting weapons */
    public const string MLTY_SPWE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#MLTY_SPWE';

    /** Weapons */
    public const string MLTY_WPNS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#MLTY_WPNS';

    /** Pharmaceutical, Medical And Biological */
    public const string PHAR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PHAR';

    /** Biological products */
    public const string PHAR_BIOP = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PHAR_BIOP';

    /** Biochemicals */
    public const string PHAR_BIOP_BIOC = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PHAR_BIOP_BIOC';

    /** Hemoderivatives */
    public const string PHAR_BIOP_HEMO = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PHAR_BIOP_HEMO';

    /** Human blood */
    public const string PHAR_BIOP_HUBL = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PHAR_BIOP_HUBL';

    /** Human serum */
    public const string PHAR_BIOP_HUSR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PHAR_BIOP_HUSR';

    /** Live human organs */
    public const string PHAR_BIOP_LHOR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PHAR_BIOP_LHOR';

    /** Semen */
    public const string PHAR_BIOP_SEME = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PHAR_BIOP_SEME';

    /** Samples */
    public const string PHAR_BIOP_SMPL = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PHAR_BIOP_SMPL';

    /** Medicines */
    public const string PHAR_MDCN = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PHAR_MDCN';

    /** Antibiotics and Vitamins */
    public const string PHAR_MDCN_ANTB = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PHAR_MDCN_ANTB';

    /** Vaccines */
    public const string PHAR_MDCN_VACC = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PHAR_MDCN_VACC';

    /** Vetenary products */
    public const string PHAR_MDCN_VETE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PHAR_MDCN_VETE';

    /** Medical */
    public const string PHAR_MEDI = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PHAR_MEDI';

    /** Pharmaceutical products */
    public const string PHAR_PHAR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PHAR_PHAR';

    /** Surgical equipment */
    public const string PHAR_PHAR_SUEQ = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PHAR_PHAR_SUEQ';

    /** Printed Matter */
    public const string PRIN = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PRIN';

    /** Advertising materials */
    public const string PRIN_ADVM = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PRIN_ADVM';

    /** Books */
    public const string PRIN_BOOK = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PRIN_BOOK';

    /** Documents and Tickets */
    public const string PRIN_DOCU = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PRIN_DOCU';

    /** Educational materials */
    public const string PRIN_EDUM = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PRIN_EDUM';

    /** Newspapers and Magazines */
    public const string PRIN_NEWS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PRIN_NEWS';

    /** Paper products */
    public const string PRIN_PPRP = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#PRIN_PPRP';

    /** Raw materials (Construction, Metals, Wood, Minerals, Plastic) */
    public const string RAWM = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM';

    /** Building material */
    public const string RAWM_BLDM = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM_BLDM';

    /** Clay products */
    public const string RAWM_CLAY = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM_CLAY';

    /** Glass products */
    public const string RAWM_GLAS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM_GLAS';

    /** Granite slabs */
    public const string RAWM_GRAN = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM_GRAN';

    /** Gums-Resines */
    public const string RAWM_GUMS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM_GUMS';

    /** Marble */
    public const string RAWM_MARB = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM_MARB';

    /** Metals */
    public const string RAWM_METL = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM_METL';

    /** Metal products */
    public const string RAWM_METP = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM_METP';

    /** Mica products */
    public const string RAWM_MICA = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM_MICA';

    /** Minerals */
    public const string RAWM_MINE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM_MINE';

    /** Mirre */
    public const string RAWM_MIRR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM_MIRR';

    /** Oils */
    public const string RAWM_OILS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM_OILS';

    /** Plastic products */
    public const string RAWM_PLST = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM_PLST';

    /** Quartz */
    public const string RAWM_QRTZ = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM_QRTZ';

    /** Rubber products */
    public const string RAWM_RUBR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM_RUBR';

    /** Rubber tyres */
    public const string RAWM_RUBR_RTYR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM_RUBR_RTYR';

    /** Stones */
    public const string RAWM_STNS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM_STNS';

    /** Wood products */
    public const string RAWM_WOOD = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#RAWM_WOOD';

    /** Scientific Instruments */
    public const string SCIN = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#SCIN';

    /** Densist equipment */
    public const string SCIN_DEEQ = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#SCIN_DEEQ';

    /** Diagnostics equipment */
    public const string SCIN_DIAG = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#SCIN_DIAG';

    /** Hearing aids */
    public const string SCIN_HEAR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#SCIN_HEAR';

    /** Laboratory equipment */
    public const string SCIN_LBEQ = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#SCIN_LBEQ';

    /** Medical equipment */
    public const string SCIN_MEEQ = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#SCIN_MEEQ';

    /** Optical instruments */
    public const string SCIN_OPTI = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#SCIN_OPTI';

    /** Precision instruments */
    public const string SCIN_PRCI = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#SCIN_PRCI';

    /** Trophies */
    public const string TRPH = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TRPH';

    /** Hunting Trophies */
    public const string TRPH_HTRH = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TRPH_HTRH';

    /** Trophies (not hunting) */
    public const string TRPH_OTRH = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TRPH_OTRH';

    /** Textiles, Leather and Furs */
    public const string TXTL = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL';

    /** Furs excluding Wear */
    public const string TXTL_FREW = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_FREW';

    /** Fur */
    public const string TXTL_FUR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_FUR';

    /** Furs wear */
    public const string TXTL_FURW = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_FURW';

    /** Leather excluding Wear */
    public const string TXTL_LEXW = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_LEXW';

    /** Leather */
    public const string TXTL_LTHR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_LTHR';

    /** Leather wear */
    public const string TXTL_LTWR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_LTWR';

    /** Textiles excluding Wear */
    public const string TXTL_TXEW = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_TXEW';

    /** Carpets and Rugs */
    public const string TXTL_TXEW_CARP = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_TXEW_CARP';

    /** Curtains and Drapery */
    public const string TXTL_TXEW_CURT = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_TXEW_CURT';

    /** Textile fabric */
    public const string TXTL_TXEW_FABR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_TXEW_FABR';

    /** Textile furnish */
    public const string TXTL_TXEW_FURN = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_TXEW_FURN';

    /** Hide */
    public const string TXTL_TXEW_HIDE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_TXEW_HIDE';

    /** Needlework */
    public const string TXTL_TXEW_NDLE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_TXEW_NDLE';

    /** Skins */
    public const string TXTL_TXEW_SKIN = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_TXEW_SKIN';

    /** Textile rolls */
    public const string TXTL_TXEW_TRLS = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_TXEW_TRLS';

    /** Yarns */
    public const string TXTL_TXEW_YARN = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_TXEW_YARN';

    /** Textile wear */
    public const string TXTL_TXLW = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_TXLW';

    /** Wearing appareil */
    public const string TXTL_TXLW_APPR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_TXLW_APPR';

    /** Clothing */
    public const string TXTL_TXLW_CLTH = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_TXLW_CLTH';

    /** Footwear */
    public const string TXTL_TXLW_FOOT = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_TXLW_FOOT';

    /** Garments */
    public const string TXTL_TXLW_GARM = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_TXLW_GARM';

    /** Textiles */
    public const string TXTL_TXTL = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#TXTL_TXTL';

    /** Valuables */
    public const string VALU = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VALU';

    /** Bank notes and Coins */
    public const string VALU_BANK = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VALU_BANK';

    /** Diamonds */
    public const string VALU_DIAM = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VALU_DIAM';

    /** Gold */
    public const string VALU_GOLD = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VALU_GOLD';

    /** Jewelery */
    public const string VALU_JWRY = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VALU_JWRY';

    /** Platinum */
    public const string VALU_PLAT = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VALU_PLAT';

    /** Precious metal */
    public const string VALU_PMET = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VALU_PMET';

    /** Precious stones */
    public const string VALU_PSTN = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VALU_PSTN';

    /** Silver */
    public const string VALU_SLVR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VALU_SLVR';

    /** Watches */
    public const string VALU_WTCH = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VALU_WTCH';

    /** Vehicle / Machinary, Parts, Spares */
    public const string VHCL = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL';

    /** Aircraft */
    public const string VHCL_AIRC = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_AIRC';

    /** Aircraft accessories */
    public const string VHCL_AIRC_AACC = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_AIRC_AACC';

    /** Aicraft engines */
    public const string VHCL_AIRC_AENG = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_AIRC_AENG';

    /** Aircraft motors */
    public const string VHCL_AIRC_AMTR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_AIRC_AMTR';

    /** Aircraft parts */
    public const string VHCL_AIRC_APRT = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_AIRC_APRT';

    /** Aircraft supplies */
    public const string VHCL_AIRC_ASUP = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_AIRC_ASUP';

    /** Aircraft wheels */
    public const string VHCL_AIRC_AWHL = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_AIRC_AWHL';

    /** Helicopter */
    public const string VHCL_AIRC_HELI = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_AIRC_HELI';

    /** Helicopter parts */
    public const string VHCL_AIRC_HPRT = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_AIRC_HPRT';

    /** Machinery and Tools */
    public const string VHCL_MACH = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_MACH';

    /** Cable coil */
    public const string VHCL_MACH_COIL = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_MACH_COIL';

    /** Comperssors */
    public const string VHCL_MACH_COMP = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_MACH_COMP';

    /** Hardware and Equipment */
    public const string VHCL_MACH_HRDW = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_MACH_HRDW';

    /** Mechanic products */
    public const string VHCL_MACH_MECH = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_MACH_MECH';

    /** Machinery supplies and Parts */
    public const string VHCL_MACH_MTSP = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_MACH_MTSP';

    /** Oil drilling equipment */
    public const string VHCL_MACH_OILD = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_MACH_OILD';

    /** Spare parts */
    public const string VHCL_MACH_PART = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_MACH_PART';

    /** Pumping equipment */
    public const string VHCL_MACH_PUEQ = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_MACH_PUEQ';

    /** Ships */
    public const string VHCL_SHIP = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_SHIP';

    /** Engines and Turbines */
    public const string VHCL_SHIP_SENG = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_SHIP_SENG';

    /** Motor and Generator */
    public const string VHCL_SHIP_SMTR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_SHIP_SMTR';

    /** Ship parts */
    public const string VHCL_SHIP_SPAR = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_SHIP_SPAR';

    /** Ship spares */
    public const string VHCL_SHIP_SSPA = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_SHIP_SSPA';

    /** Surface vehicles */
    public const string VHCL_SVCL = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_SVCL';

    /** Automobiles */
    public const string VHCL_SVCL_AUTO = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_SVCL_AUTO';

    /** Bicycles */
    public const string VHCL_SVCL_BICY = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_SVCL_BICY';

    /** Cartainer */
    public const string VHCL_SVCL_CRTA = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_SVCL_CRTA';

    /** Motorcycles */
    public const string VHCL_SVCL_MOTO = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_SVCL_MOTO';

    /** Automobile parts */
    public const string VHCL_SVCL_PART = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_SVCL_PART';

    /** Tires */
    public const string VHCL_SVCL_TIRE = 'https://onerecord.iata.org/ns/code-lists/CommodityCode#VHCL_SVCL_TIRE';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'CHEM' => self::CHEM,
        'CHEM_CDGR' => self::CHEM_CDGR,
        'CHEM_CLNG' => self::CHEM_CLNG,
        'CHEM_CNDG' => self::CHEM_CNDG,
        'CHEM_CNMD' => self::CHEM_CNMD,
        'CHEM_COSM' => self::CHEM_COSM,
        'CHEM_COSM_COSD' => self::CHEM_COSM_COSD,
        'CHEM_COSM_PERF' => self::CHEM_COSM_PERF,
        'CHEM_DGRG' => self::CHEM_DGRG,
        'CHEM_DGRG_EXPL' => self::CHEM_DGRG_EXPL,
        'CHEM_DICE' => self::CHEM_DICE,
        'CHEM_PAIN' => self::CHEM_PAIN,
        'CHEM_PETRO' => self::CHEM_PETRO,
        'CHEM_RADA' => self::CHEM_RADA,
        'CHEM_REAG' => self::CHEM_REAG,
        'CONS' => self::CONS,
        'CONS_CMPY' => self::CONS_CMPY,
        'CONS_CWRE' => self::CONS_CWRE,
        'CONS_DIPL' => self::CONS_DIPL,
        'CONS_EXHB' => self::CONS_EXHB,
        'CONS_FRNT' => self::CONS_FRNT,
        'CONS_GLAS' => self::CONS_GLAS,
        'CONS_HAID' => self::CONS_HAID,
        'CONS_HHGD' => self::CONS_HHGD,
        'CONS_HRSE' => self::CONS_HRSE,
        'CONS_HSER' => self::CONS_HSER,
        'CONS_OFSP' => self::CONS_OFSP,
        'CONS_PERS' => self::CONS_PERS,
        'CONS_SPEC' => self::CONS_SPEC,
        'CONS_SPRT' => self::CONS_SPRT,
        'CONS_TOYS' => self::CONS_TOYS,
        'CONS_UBAG' => self::CONS_UBAG,
        'ELEC' => self::ELEC,
        'ELEC_AVEQ' => self::ELEC_AVEQ,
        'ELEC_CALC' => self::ELEC_CALC,
        'ELEC_CMPT' => self::ELEC_CMPT,
        'ELEC_CPRT' => self::ELEC_CPRT,
        'ELEC_ECOM' => self::ELEC_ECOM,
        'ELEC_EEQP' => self::ELEC_EEQP,
        'ELEC_EGDS' => self::ELEC_EGDS,
        'ELEC_ELQP' => self::ELEC_ELQP,
        'ELEC_OFEQ' => self::ELEC_OFEQ,
        'ELEC_QUAN' => self::ELEC_QUAN,
        'ELEC_TELC' => self::ELEC_TELC,
        'FLWR' => self::FLWR,
        'FLWR_FLWR' => self::FLWR_FLWR,
        'FLWR_FLWR_CFLW' => self::FLWR_FLWR_CFLW,
        'FLWR_FLWR_TFLW' => self::FLWR_FLWR_TFLW,
        'FLWR_FLWR_TULP' => self::FLWR_FLWR_TULP,
        'FLWR_FMNT' => self::FLWR_FMNT,
        'FLWR_HERBS' => self::FLWR_HERBS,
        'FLWR_PLNT' => self::FLWR_PLNT,
        'FLWR_PLNT_APLN' => self::FLWR_PLNT_APLN,
        'FLWR_PLNT_BULB' => self::FLWR_PLNT_BULB,
        'FLWR_PLNT_MPLN' => self::FLWR_PLNT_MPLN,
        'FLWR_PLNT_TPLN' => self::FLWR_PLNT_TPLN,
        'FLWR_SEED' => self::FLWR_SEED,
        'FOOD' => self::FOOD,
        'FOOD_BVRG' => self::FOOD_BVRG,
        'FOOD_BVRG_BEER' => self::FOOD_BVRG_BEER,
        'FOOD_BVRG_COFY' => self::FOOD_BVRG_COFY,
        'FOOD_BVRG_TEA' => self::FOOD_BVRG_TEA,
        'FOOD_BVRG_WINE' => self::FOOD_BVRG_WINE,
        'FOOD_CERE' => self::FOOD_CERE,
        'FOOD_CERE_BRED' => self::FOOD_CERE_BRED,
        'FOOD_CERE_CAKE' => self::FOOD_CERE_CAKE,
        'FOOD_DARY' => self::FOOD_DARY,
        'FOOD_DARY_CHSE' => self::FOOD_DARY_CHSE,
        'FOOD_DARY_EGGS' => self::FOOD_DARY_EGGS,
        'FOOD_DARY_ICEC' => self::FOOD_DARY_ICEC,
        'FOOD_FISH' => self::FOOD_FISH,
        'FOOD_FISH_ALBA' => self::FOOD_FISH_ALBA,
        'FOOD_FISH_CAVR' => self::FOOD_FISH_CAVR,
        'FOOD_FISH_FFSH' => self::FOOD_FISH_FFSH,
        'FOOD_FISH_FRZF' => self::FOOD_FISH_FRZF,
        'FOOD_FISH_FRZS' => self::FOOD_FISH_FRZS,
        'FOOD_FISH_HAKE' => self::FOOD_FISH_HAKE,
        'FOOD_FISH_LOBS' => self::FOOD_FISH_LOBS,
        'FOOD_FISH_REPA' => self::FOOD_FISH_REPA,
        'FOOD_FISH_SFIN' => self::FOOD_FISH_SFIN,
        'FOOD_FISH_SFSH' => self::FOOD_FISH_SFSH,
        'FOOD_FISH_SHRI' => self::FOOD_FISH_SHRI,
        'FOOD_FISH_SLMN' => self::FOOD_FISH_SLMN,
        'FOOD_FISH_TUNA' => self::FOOD_FISH_TUNA,
        'FOOD_FRTV' => self::FOOD_FRTV,
        'FOOD_FRTV_APPL' => self::FOOD_FRTV_APPL,
        'FOOD_FRTV_ASPA' => self::FOOD_FRTV_ASPA,
        'FOOD_FRTV_AVOC' => self::FOOD_FRTV_AVOC,
        'FOOD_FRTV_BANA' => self::FOOD_FRTV_BANA,
        'FOOD_FRTV_BEAN' => self::FOOD_FRTV_BEAN,
        'FOOD_FRTV_BERR' => self::FOOD_FRTV_BERR,
        'FOOD_FRTV_CHER' => self::FOOD_FRTV_CHER,
        'FOOD_FRTV_CMBR' => self::FOOD_FRTV_CMBR,
        'FOOD_FRTV_DURI' => self::FOOD_FRTV_DURI,
        'FOOD_FRTV_GARL' => self::FOOD_FRTV_GARL,
        'FOOD_FRTV_GRAP' => self::FOOD_FRTV_GRAP,
        'FOOD_FRTV_LITC' => self::FOOD_FRTV_LITC,
        'FOOD_FRTV_MANG' => self::FOOD_FRTV_MANG,
        'FOOD_FRTV_MLNS' => self::FOOD_FRTV_MLNS,
        'FOOD_FRTV_MUSH' => self::FOOD_FRTV_MUSH,
        'FOOD_FRTV_PEPP' => self::FOOD_FRTV_PEPP,
        'FOOD_FRTV_PINE' => self::FOOD_FRTV_PINE,
        'FOOD_FRTV_PPYA' => self::FOOD_FRTV_PPYA,
        'FOOD_FRTV_PROD' => self::FOOD_FRTV_PROD,
        'FOOD_FRTV_STRW' => self::FOOD_FRTV_STRW,
        'FOOD_FRTV_TOMA' => self::FOOD_FRTV_TOMA,
        'FOOD_MEAT' => self::FOOD_MEAT,
        'FOOD_MEAT_BEEF' => self::FOOD_MEAT_BEEF,
        'FOOD_MEAT_DRIM' => self::FOOD_MEAT_DRIM,
        'FOOD_MEAT_FRZM' => self::FOOD_MEAT_FRZM,
        'FOOD_MEAT_GOSL' => self::FOOD_MEAT_GOSL,
        'FOOD_MEAT_GUTS' => self::FOOD_MEAT_GUTS,
        'FOOD_MEAT_HRSP' => self::FOOD_MEAT_HRSP,
        'FOOD_MEAT_MEAT' => self::FOOD_MEAT_MEAT,
        'FOOD_MEAT_PORK' => self::FOOD_MEAT_PORK,
        'FOOD_MEAT_SAUS' => self::FOOD_MEAT_SAUS,
        'FOOD_PERI' => self::FOOD_PERI,
        'FOOD_STUF' => self::FOOD_STUF,
        'FOOD_STUF_CATE' => self::FOOD_STUF_CATE,
        'FOOD_STUF_CHOC' => self::FOOD_STUF_CHOC,
        'FOOD_STUF_DFRU' => self::FOOD_STUF_DFRU,
        'FOOD_STUF_MPWD' => self::FOOD_STUF_MPWD,
        'FOOD_STUF_NUTS' => self::FOOD_STUF_NUTS,
        'FOOD_STUF_OOIL' => self::FOOD_STUF_OOIL,
        'FOOD_STUF_SPCE' => self::FOOD_STUF_SPCE,
        'FOOD_TBCO' => self::FOOD_TBCO,
        'FOOD_TBCO_CGRT' => self::FOOD_TBCO_CGRT,
        'FOOD_TBCO_CIGA' => self::FOOD_TBCO_CIGA,
        'GENE' => self::GENE,
        'HUMR' => self::HUMR,
        'HUMR_HUMB' => self::HUMR_HUMB,
        'HUMR_HUMC' => self::HUMR_HUMC,
        'LIVE' => self::LIVE,
        'LIVE_BRDH' => self::LIVE_BRDH,
        'LIVE_BRDH_BIRD' => self::LIVE_BRDH_BIRD,
        'LIVE_BRDH_CHIC' => self::LIVE_BRDH_CHIC,
        'LIVE_BRDH_DUCK' => self::LIVE_BRDH_DUCK,
        'LIVE_BRDH_HEGG' => self::LIVE_BRDH_HEGG,
        'LIVE_BRDH_OSTR' => self::LIVE_BRDH_OSTR,
        'LIVE_BRDH_PARR' => self::LIVE_BRDH_PARR,
        'LIVE_BRDH_TRKY' => self::LIVE_BRDH_TRKY,
        'LIVE_INSC' => self::LIVE_INSC,
        'LIVE_INSC_BEES' => self::LIVE_INSC_BEES,
        'LIVE_LFSH' => self::LIVE_LFSH,
        'LIVE_LFSH_EELS' => self::LIVE_LFSH_EELS,
        'LIVE_LFSH_KOIF' => self::LIVE_LFSH_KOIF,
        'LIVE_LFSH_TRPF' => self::LIVE_LFSH_TRPF,
        'LIVE_MLKS' => self::LIVE_MLKS,
        'LIVE_MLKS_LUGW' => self::LIVE_MLKS_LUGW,
        'LIVE_MLKS_SNAI' => self::LIVE_MLKS_SNAI,
        'LIVE_MMLS' => self::LIVE_MMLS,
        'LIVE_MMLS_CATL' => self::LIVE_MMLS_CATL,
        'LIVE_MMLS_CATS' => self::LIVE_MMLS_CATS,
        'LIVE_MMLS_CATS_Abyssinian' => self::LIVE_MMLS_CATS_Abyssinian,
        'LIVE_MMLS_CATS_American_Bobtail' => self::LIVE_MMLS_CATS_American_Bobtail,
        'LIVE_MMLS_CATS_American_Curl' => self::LIVE_MMLS_CATS_American_Curl,
        'LIVE_MMLS_CATS_American_Keuda' => self::LIVE_MMLS_CATS_American_Keuda,
        'LIVE_MMLS_CATS_American_Lynx' => self::LIVE_MMLS_CATS_American_Lynx,
        'LIVE_MMLS_CATS_American_Polydactyl' => self::LIVE_MMLS_CATS_American_Polydactyl,
        'LIVE_MMLS_CATS_American_Shorthair' => self::LIVE_MMLS_CATS_American_Shorthair,
        'LIVE_MMLS_CATS_American_Wirehair' => self::LIVE_MMLS_CATS_American_Wirehair,
        'LIVE_MMLS_CATS_Asian' => self::LIVE_MMLS_CATS_Asian,
        'LIVE_MMLS_CATS_Australian_Mist' => self::LIVE_MMLS_CATS_Australian_Mist,
        'LIVE_MMLS_CATS_Balinese' => self::LIVE_MMLS_CATS_Balinese,
        'LIVE_MMLS_CATS_Bengal' => self::LIVE_MMLS_CATS_Bengal,
        'LIVE_MMLS_CATS_Birman' => self::LIVE_MMLS_CATS_Birman,
        'LIVE_MMLS_CATS_Bombay' => self::LIVE_MMLS_CATS_Bombay,
        'LIVE_MMLS_CATS_Bristol' => self::LIVE_MMLS_CATS_Bristol,
        'LIVE_MMLS_CATS_British_Shorthair' => self::LIVE_MMLS_CATS_British_Shorthair,
        'LIVE_MMLS_CATS_Burmese' => self::LIVE_MMLS_CATS_Burmese,
        'LIVE_MMLS_CATS_California_Spangled' => self::LIVE_MMLS_CATS_California_Spangled,
        'LIVE_MMLS_CATS_Chartreux' => self::LIVE_MMLS_CATS_Chartreux,
        'LIVE_MMLS_CATS_Chausie' => self::LIVE_MMLS_CATS_Chausie,
        'LIVE_MMLS_CATS_Chinese_Harlequin' => self::LIVE_MMLS_CATS_Chinese_Harlequin,
        'LIVE_MMLS_CATS_Color_Point_Shorthair' => self::LIVE_MMLS_CATS_Color_Point_Shorthair,
        'LIVE_MMLS_CATS_Copper' => self::LIVE_MMLS_CATS_Copper,
        'LIVE_MMLS_CATS_Cornish_Rex' => self::LIVE_MMLS_CATS_Cornish_Rex,
        'LIVE_MMLS_CATS_Cymric' => self::LIVE_MMLS_CATS_Cymric,
        'LIVE_MMLS_CATS_Desert_Lynx' => self::LIVE_MMLS_CATS_Desert_Lynx,
        'LIVE_MMLS_CATS_Devon_Rex' => self::LIVE_MMLS_CATS_Devon_Rex,
        'LIVE_MMLS_CATS_Donskoy' => self::LIVE_MMLS_CATS_Donskoy,
        'LIVE_MMLS_CATS_Egyptian_Mau' => self::LIVE_MMLS_CATS_Egyptian_Mau,
        'LIVE_MMLS_CATS_Exotic_Shorthair' => self::LIVE_MMLS_CATS_Exotic_Shorthair,
        'LIVE_MMLS_CATS_Havana' => self::LIVE_MMLS_CATS_Havana,
        'LIVE_MMLS_CATS_Highland_Lynx' => self::LIVE_MMLS_CATS_Highland_Lynx,
        'LIVE_MMLS_CATS_Himalayan' => self::LIVE_MMLS_CATS_Himalayan,
        'LIVE_MMLS_CATS_Japanese_Bobtail' => self::LIVE_MMLS_CATS_Japanese_Bobtail,
        'LIVE_MMLS_CATS_Javanese' => self::LIVE_MMLS_CATS_Javanese,
        'LIVE_MMLS_CATS_Korat' => self::LIVE_MMLS_CATS_Korat,
        'LIVE_MMLS_CATS_LaPerm' => self::LIVE_MMLS_CATS_LaPerm,
        'LIVE_MMLS_CATS_Maine_Coon' => self::LIVE_MMLS_CATS_Maine_Coon,
        'LIVE_MMLS_CATS_Manx' => self::LIVE_MMLS_CATS_Manx,
        'LIVE_MMLS_CATS_Mojave_Spotted' => self::LIVE_MMLS_CATS_Mojave_Spotted,
        'LIVE_MMLS_CATS_Munchkin' => self::LIVE_MMLS_CATS_Munchkin,
        'LIVE_MMLS_CATS_Niebelung' => self::LIVE_MMLS_CATS_Niebelung,
        'LIVE_MMLS_CATS_Norwegian_Forest' => self::LIVE_MMLS_CATS_Norwegian_Forest,
        'LIVE_MMLS_CATS_Ocicat' => self::LIVE_MMLS_CATS_Ocicat,
        'LIVE_MMLS_CATS_Ojos_Azules' => self::LIVE_MMLS_CATS_Ojos_Azules,
        'LIVE_MMLS_CATS_Oriental' => self::LIVE_MMLS_CATS_Oriental,
        'LIVE_MMLS_CATS_Pantherette' => self::LIVE_MMLS_CATS_Pantherette,
        'LIVE_MMLS_CATS_Persian' => self::LIVE_MMLS_CATS_Persian,
        'LIVE_MMLS_CATS_Peterbald' => self::LIVE_MMLS_CATS_Peterbald,
        'LIVE_MMLS_CATS_Pixiebob' => self::LIVE_MMLS_CATS_Pixiebob,
        'LIVE_MMLS_CATS_Ragamuffin' => self::LIVE_MMLS_CATS_Ragamuffin,
        'LIVE_MMLS_CATS_Ragdoll' => self::LIVE_MMLS_CATS_Ragdoll,
        'LIVE_MMLS_CATS_Russian_Blue' => self::LIVE_MMLS_CATS_Russian_Blue,
        'LIVE_MMLS_CATS_Safari' => self::LIVE_MMLS_CATS_Safari,
        'LIVE_MMLS_CATS_Savannah' => self::LIVE_MMLS_CATS_Savannah,
        'LIVE_MMLS_CATS_Scottish_Fold' => self::LIVE_MMLS_CATS_Scottish_Fold,
        'LIVE_MMLS_CATS_Selkirk_Rex' => self::LIVE_MMLS_CATS_Selkirk_Rex,
        'LIVE_MMLS_CATS_Serengeti' => self::LIVE_MMLS_CATS_Serengeti,
        'LIVE_MMLS_CATS_Siamese' => self::LIVE_MMLS_CATS_Siamese,
        'LIVE_MMLS_CATS_Siberian' => self::LIVE_MMLS_CATS_Siberian,
        'LIVE_MMLS_CATS_Singapura' => self::LIVE_MMLS_CATS_Singapura,
        'LIVE_MMLS_CATS_Snowshoe' => self::LIVE_MMLS_CATS_Snowshoe,
        'LIVE_MMLS_CATS_Somali' => self::LIVE_MMLS_CATS_Somali,
        'LIVE_MMLS_CATS_Sphynx' => self::LIVE_MMLS_CATS_Sphynx,
        'LIVE_MMLS_CATS_Tiffany' => self::LIVE_MMLS_CATS_Tiffany,
        'LIVE_MMLS_CATS_Tonkinese' => self::LIVE_MMLS_CATS_Tonkinese,
        'LIVE_MMLS_CATS_Traditional_Siamese' => self::LIVE_MMLS_CATS_Traditional_Siamese,
        'LIVE_MMLS_CATS_Turkish_Angora' => self::LIVE_MMLS_CATS_Turkish_Angora,
        'LIVE_MMLS_CATS_Turkish_Van' => self::LIVE_MMLS_CATS_Turkish_Van,
        'LIVE_MMLS_CATS_Vienna_Woods' => self::LIVE_MMLS_CATS_Vienna_Woods,
        'LIVE_MMLS_CATS_Viverral_Hybrid_Cat' => self::LIVE_MMLS_CATS_Viverral_Hybrid_Cat,
        'LIVE_MMLS_CATS_York_Chocolate' => self::LIVE_MMLS_CATS_York_Chocolate,
        'LIVE_MMLS_DOGS' => self::LIVE_MMLS_DOGS,
        'LIVE_MMLS_DOGS_Affenpinscher' => self::LIVE_MMLS_DOGS_Affenpinscher,
        'LIVE_MMLS_DOGS_Afghan_Hound' => self::LIVE_MMLS_DOGS_Afghan_Hound,
        'LIVE_MMLS_DOGS_Airedale_Terrier' => self::LIVE_MMLS_DOGS_Airedale_Terrier,
        'LIVE_MMLS_DOGS_Akita' => self::LIVE_MMLS_DOGS_Akita,
        'LIVE_MMLS_DOGS_Alangu_Mastiff' => self::LIVE_MMLS_DOGS_Alangu_Mastiff,
        'LIVE_MMLS_DOGS_Alano' => self::LIVE_MMLS_DOGS_Alano,
        'LIVE_MMLS_DOGS_Alaskan_Malamute' => self::LIVE_MMLS_DOGS_Alaskan_Malamute,
        'LIVE_MMLS_DOGS_American_Bulldog' => self::LIVE_MMLS_DOGS_American_Bulldog,
        'LIVE_MMLS_DOGS_American_Bully' => self::LIVE_MMLS_DOGS_American_Bully,
        'LIVE_MMLS_DOGS_American_Cocker_Spaniel' => self::LIVE_MMLS_DOGS_American_Cocker_Spaniel,
        'LIVE_MMLS_DOGS_American_English_Coonhound' => self::LIVE_MMLS_DOGS_American_English_Coonhound,
        'LIVE_MMLS_DOGS_American_Eskimo_Dog_Miniature' => self::LIVE_MMLS_DOGS_American_Eskimo_Dog_Miniature,
        'LIVE_MMLS_DOGS_American_Eskimo_Dog_Standard' => self::LIVE_MMLS_DOGS_American_Eskimo_Dog_Standard,
        'LIVE_MMLS_DOGS_American_Eskimo_Dog_Toy' => self::LIVE_MMLS_DOGS_American_Eskimo_Dog_Toy,
        'LIVE_MMLS_DOGS_American_Foxhound' => self::LIVE_MMLS_DOGS_American_Foxhound,
        'LIVE_MMLS_DOGS_American_Hairless_Terrier' => self::LIVE_MMLS_DOGS_American_Hairless_Terrier,
        'LIVE_MMLS_DOGS_American_Pit_Bull_Terrier' => self::LIVE_MMLS_DOGS_American_Pit_Bull_Terrier,
        'LIVE_MMLS_DOGS_American_Staffordshire_Terrier' => self::LIVE_MMLS_DOGS_American_Staffordshire_Terrier,
        'LIVE_MMLS_DOGS_American_Water_Spaniel' => self::LIVE_MMLS_DOGS_American_Water_Spaniel,
        'LIVE_MMLS_DOGS_Anatolian_Shepherd_Dog' => self::LIVE_MMLS_DOGS_Anatolian_Shepherd_Dog,
        'LIVE_MMLS_DOGS_Argentinian_Mastiff' => self::LIVE_MMLS_DOGS_Argentinian_Mastiff,
        'LIVE_MMLS_DOGS_Aussiedoodle' => self::LIVE_MMLS_DOGS_Aussiedoodle,
        'LIVE_MMLS_DOGS_Australian_Cattle_Dog' => self::LIVE_MMLS_DOGS_Australian_Cattle_Dog,
        'LIVE_MMLS_DOGS_Australian_Shepherd' => self::LIVE_MMLS_DOGS_Australian_Shepherd,
        'LIVE_MMLS_DOGS_Australian_Terrier' => self::LIVE_MMLS_DOGS_Australian_Terrier,
        'LIVE_MMLS_DOGS_Ba_Shar_Basset_Hound_Shar_pei_Mix' => self::LIVE_MMLS_DOGS_Ba_Shar_Basset_Hound_Shar_pei_Mix,
        'LIVE_MMLS_DOGS_Basenji' => self::LIVE_MMLS_DOGS_Basenji,
        'LIVE_MMLS_DOGS_Basset_Hound' => self::LIVE_MMLS_DOGS_Basset_Hound,
        'LIVE_MMLS_DOGS_Beagle' => self::LIVE_MMLS_DOGS_Beagle,
        'LIVE_MMLS_DOGS_Bearded_Collie' => self::LIVE_MMLS_DOGS_Bearded_Collie,
        'LIVE_MMLS_DOGS_Beauceron' => self::LIVE_MMLS_DOGS_Beauceron,
        'LIVE_MMLS_DOGS_Bedlington_Terrier' => self::LIVE_MMLS_DOGS_Bedlington_Terrier,
        'LIVE_MMLS_DOGS_Belgian_Malinois' => self::LIVE_MMLS_DOGS_Belgian_Malinois,
        'LIVE_MMLS_DOGS_Belgian_Sheepdog' => self::LIVE_MMLS_DOGS_Belgian_Sheepdog,
        'LIVE_MMLS_DOGS_Belgian_Tervuren' => self::LIVE_MMLS_DOGS_Belgian_Tervuren,
        'LIVE_MMLS_DOGS_Bergamasco' => self::LIVE_MMLS_DOGS_Bergamasco,
        'LIVE_MMLS_DOGS_Berger_Picard' => self::LIVE_MMLS_DOGS_Berger_Picard,
        'LIVE_MMLS_DOGS_Bernedoodle' => self::LIVE_MMLS_DOGS_Bernedoodle,
        'LIVE_MMLS_DOGS_Bernese_Mountain_Dog' => self::LIVE_MMLS_DOGS_Bernese_Mountain_Dog,
        'LIVE_MMLS_DOGS_Bichon_Frise' => self::LIVE_MMLS_DOGS_Bichon_Frise,
        'LIVE_MMLS_DOGS_Black_Russian_Terrier' => self::LIVE_MMLS_DOGS_Black_Russian_Terrier,
        'LIVE_MMLS_DOGS_Black_and_Tan_Coonhound' => self::LIVE_MMLS_DOGS_Black_and_Tan_Coonhound,
        'LIVE_MMLS_DOGS_Bloodhound' => self::LIVE_MMLS_DOGS_Bloodhound,
        'LIVE_MMLS_DOGS_Bluetick_Coonhound' => self::LIVE_MMLS_DOGS_Bluetick_Coonhound,
        'LIVE_MMLS_DOGS_Boerboel' => self::LIVE_MMLS_DOGS_Boerboel,
        'LIVE_MMLS_DOGS_Border_Collie' => self::LIVE_MMLS_DOGS_Border_Collie,
        'LIVE_MMLS_DOGS_Border_Terrier' => self::LIVE_MMLS_DOGS_Border_Terrier,
        'LIVE_MMLS_DOGS_Borzoi' => self::LIVE_MMLS_DOGS_Borzoi,
        'LIVE_MMLS_DOGS_Boston_Terrier' => self::LIVE_MMLS_DOGS_Boston_Terrier,
        'LIVE_MMLS_DOGS_Bouvier_des_Flandres' => self::LIVE_MMLS_DOGS_Bouvier_des_Flandres,
        'LIVE_MMLS_DOGS_Boweimar_Boxer_Weimaraner_Mix' => self::LIVE_MMLS_DOGS_Boweimar_Boxer_Weimaraner_Mix,
        'LIVE_MMLS_DOGS_Boxer' => self::LIVE_MMLS_DOGS_Boxer,
        'LIVE_MMLS_DOGS_Boykin_Spaniel' => self::LIVE_MMLS_DOGS_Boykin_Spaniel,
        'LIVE_MMLS_DOGS_Brazilian_Mastiff' => self::LIVE_MMLS_DOGS_Brazilian_Mastiff,
        'LIVE_MMLS_DOGS_Briard' => self::LIVE_MMLS_DOGS_Briard,
        'LIVE_MMLS_DOGS_Brittany' => self::LIVE_MMLS_DOGS_Brittany,
        'LIVE_MMLS_DOGS_Brussels_Griffon' => self::LIVE_MMLS_DOGS_Brussels_Griffon,
        'LIVE_MMLS_DOGS_Bull_Terrier' => self::LIVE_MMLS_DOGS_Bull_Terrier,
        'LIVE_MMLS_DOGS_Bull_Terrier_Miniature' => self::LIVE_MMLS_DOGS_Bull_Terrier_Miniature,
        'LIVE_MMLS_DOGS_Bulldog' => self::LIVE_MMLS_DOGS_Bulldog,
        'LIVE_MMLS_DOGS_Bulli_Kutta' => self::LIVE_MMLS_DOGS_Bulli_Kutta,
        'LIVE_MMLS_DOGS_Bullmastiff' => self::LIVE_MMLS_DOGS_Bullmastiff,
        'LIVE_MMLS_DOGS_Bully_Kutta_Mastiff_breed' => self::LIVE_MMLS_DOGS_Bully_Kutta_Mastiff_breed,
        'LIVE_MMLS_DOGS_Cairn_Terrier' => self::LIVE_MMLS_DOGS_Cairn_Terrier,
        'LIVE_MMLS_DOGS_Campeiro_Bulldog_Brazilian_Bulldog' => self::LIVE_MMLS_DOGS_Campeiro_Bulldog_Brazilian_Bulldog,
        'LIVE_MMLS_DOGS_Canaan_Dog' => self::LIVE_MMLS_DOGS_Canaan_Dog,
        'LIVE_MMLS_DOGS_Canary_Mastiff' => self::LIVE_MMLS_DOGS_Canary_Mastiff,
        'LIVE_MMLS_DOGS_Cane_Corso' => self::LIVE_MMLS_DOGS_Cane_Corso,
        'LIVE_MMLS_DOGS_Cardigan_Welsh_Corgi' => self::LIVE_MMLS_DOGS_Cardigan_Welsh_Corgi,
        'LIVE_MMLS_DOGS_Catahoula_Bulldog_Catahoula_Leopard_Bulldog' => self::LIVE_MMLS_DOGS_Catahoula_Bulldog_Catahoula_Leopard_Bulldog,
        'LIVE_MMLS_DOGS_Cavachon_King_Charles_Spaniel_Bichon_Frise' => self::LIVE_MMLS_DOGS_Cavachon_King_Charles_Spaniel_Bichon_Frise,
        'LIVE_MMLS_DOGS_Cavalier_King_Charles_Spaniel' => self::LIVE_MMLS_DOGS_Cavalier_King_Charles_Spaniel,
        'LIVE_MMLS_DOGS_Cavapoo_Cavalier_King_Charles_Spaniel_Poodle' => self::LIVE_MMLS_DOGS_Cavapoo_Cavalier_King_Charles_Spaniel_Poodle,
        'LIVE_MMLS_DOGS_Cesky_Terrier' => self::LIVE_MMLS_DOGS_Cesky_Terrier,
        'LIVE_MMLS_DOGS_Chesapeake_Bay_Retriever' => self::LIVE_MMLS_DOGS_Chesapeake_Bay_Retriever,
        'LIVE_MMLS_DOGS_Chihuahua' => self::LIVE_MMLS_DOGS_Chihuahua,
        'LIVE_MMLS_DOGS_Chinese_Crested_Dog' => self::LIVE_MMLS_DOGS_Chinese_Crested_Dog,
        'LIVE_MMLS_DOGS_Chinese_Pug' => self::LIVE_MMLS_DOGS_Chinese_Pug,
        'LIVE_MMLS_DOGS_Chinese_Shar_Pei' => self::LIVE_MMLS_DOGS_Chinese_Shar_Pei,
        'LIVE_MMLS_DOGS_Chinook' => self::LIVE_MMLS_DOGS_Chinook,
        'LIVE_MMLS_DOGS_Chipin_Chihuahua_Minature_Pinscher_Mix' => self::LIVE_MMLS_DOGS_Chipin_Chihuahua_Minature_Pinscher_Mix,
        'LIVE_MMLS_DOGS_Chiweenie_Chihuahua_Dachshund_Mix' => self::LIVE_MMLS_DOGS_Chiweenie_Chihuahua_Dachshund_Mix,
        'LIVE_MMLS_DOGS_Chorkie_Chihuahua_Yorkshire_Terrier_Mix' => self::LIVE_MMLS_DOGS_Chorkie_Chihuahua_Yorkshire_Terrier_Mix,
        'LIVE_MMLS_DOGS_Chow_Chow' => self::LIVE_MMLS_DOGS_Chow_Chow,
        'LIVE_MMLS_DOGS_Chow_Pei_Chow_Chow_Shar_Pei_Mix' => self::LIVE_MMLS_DOGS_Chow_Pei_Chow_Chow_Shar_Pei_Mix,
        'LIVE_MMLS_DOGS_Cirneco_dell_Etna' => self::LIVE_MMLS_DOGS_Cirneco_dell_Etna,
        'LIVE_MMLS_DOGS_Clumber_Spaniel' => self::LIVE_MMLS_DOGS_Clumber_Spaniel,
        'LIVE_MMLS_DOGS_Cockapoo_Cocker_Spaniel_Poodle_Mix' => self::LIVE_MMLS_DOGS_Cockapoo_Cocker_Spaniel_Poodle_Mix,
        'LIVE_MMLS_DOGS_Cocker_Spaniel' => self::LIVE_MMLS_DOGS_Cocker_Spaniel,
        'LIVE_MMLS_DOGS_Collie' => self::LIVE_MMLS_DOGS_Collie,
        'LIVE_MMLS_DOGS_Coton_de_Tulear' => self::LIVE_MMLS_DOGS_Coton_de_Tulear,
        'LIVE_MMLS_DOGS_Curly_Coated_Retriever' => self::LIVE_MMLS_DOGS_Curly_Coated_Retriever,
        'LIVE_MMLS_DOGS_Dachshund' => self::LIVE_MMLS_DOGS_Dachshund,
        'LIVE_MMLS_DOGS_Dalmatian' => self::LIVE_MMLS_DOGS_Dalmatian,
        'LIVE_MMLS_DOGS_Dandie_Dinmont_Terrier' => self::LIVE_MMLS_DOGS_Dandie_Dinmont_Terrier,
        'LIVE_MMLS_DOGS_Doberman_Pinscher' => self::LIVE_MMLS_DOGS_Doberman_Pinscher,
        'LIVE_MMLS_DOGS_Dogo_Argentino' => self::LIVE_MMLS_DOGS_Dogo_Argentino,
        'LIVE_MMLS_DOGS_Dogue_de_Bordeaux' => self::LIVE_MMLS_DOGS_Dogue_de_Bordeaux,
        'LIVE_MMLS_DOGS_Doxiepoo_Dachshund_Poodle_Mix' => self::LIVE_MMLS_DOGS_Doxiepoo_Dachshund_Poodle_Mix,
        'LIVE_MMLS_DOGS_Dutch_Pug' => self::LIVE_MMLS_DOGS_Dutch_Pug,
        'LIVE_MMLS_DOGS_English_Bulldog' => self::LIVE_MMLS_DOGS_English_Bulldog,
        'LIVE_MMLS_DOGS_English_Cocker_Spaniel' => self::LIVE_MMLS_DOGS_English_Cocker_Spaniel,
        'LIVE_MMLS_DOGS_English_Foxhound' => self::LIVE_MMLS_DOGS_English_Foxhound,
        'LIVE_MMLS_DOGS_English_Mastiff' => self::LIVE_MMLS_DOGS_English_Mastiff,
        'LIVE_MMLS_DOGS_English_Setter' => self::LIVE_MMLS_DOGS_English_Setter,
        'LIVE_MMLS_DOGS_English_Springer_Spaniel' => self::LIVE_MMLS_DOGS_English_Springer_Spaniel,
        'LIVE_MMLS_DOGS_English_Toy_Spaniel' => self::LIVE_MMLS_DOGS_English_Toy_Spaniel,
        'LIVE_MMLS_DOGS_Entlebucher_Mountain_Dog' => self::LIVE_MMLS_DOGS_Entlebucher_Mountain_Dog,
        'LIVE_MMLS_DOGS_Eurasier' => self::LIVE_MMLS_DOGS_Eurasier,
        'LIVE_MMLS_DOGS_Field_Spaniel' => self::LIVE_MMLS_DOGS_Field_Spaniel,
        'LIVE_MMLS_DOGS_Fila_Brasileiro_Brazilian_Mastiff' => self::LIVE_MMLS_DOGS_Fila_Brasileiro_Brazilian_Mastiff,
        'LIVE_MMLS_DOGS_Finnish_Lapphund' => self::LIVE_MMLS_DOGS_Finnish_Lapphund,
        'LIVE_MMLS_DOGS_Finnish_Spitz' => self::LIVE_MMLS_DOGS_Finnish_Spitz,
        'LIVE_MMLS_DOGS_Flat_Coated_Retriever' => self::LIVE_MMLS_DOGS_Flat_Coated_Retriever,
        'LIVE_MMLS_DOGS_French_Bulldog' => self::LIVE_MMLS_DOGS_French_Bulldog,
        'LIVE_MMLS_DOGS_French_Mastiff' => self::LIVE_MMLS_DOGS_French_Mastiff,
        'LIVE_MMLS_DOGS_German_Mastiff_Great_Dane' => self::LIVE_MMLS_DOGS_German_Mastiff_Great_Dane,
        'LIVE_MMLS_DOGS_German_Pinscher' => self::LIVE_MMLS_DOGS_German_Pinscher,
        'LIVE_MMLS_DOGS_German_Shepherd_Dog' => self::LIVE_MMLS_DOGS_German_Shepherd_Dog,
        'LIVE_MMLS_DOGS_German_Shorthaired_Pointer' => self::LIVE_MMLS_DOGS_German_Shorthaired_Pointer,
        'LIVE_MMLS_DOGS_German_Wirehaired_Pointer' => self::LIVE_MMLS_DOGS_German_Wirehaired_Pointer,
        'LIVE_MMLS_DOGS_Giant_Schnauzer' => self::LIVE_MMLS_DOGS_Giant_Schnauzer,
        'LIVE_MMLS_DOGS_Glen_of_Imaal_Terrier' => self::LIVE_MMLS_DOGS_Glen_of_Imaal_Terrier,
        'LIVE_MMLS_DOGS_Goldador_Golden_Retriever_Labrador_Retriever' => self::LIVE_MMLS_DOGS_Goldador_Golden_Retriever_Labrador_Retriever,
        'LIVE_MMLS_DOGS_Golden_Retriever' => self::LIVE_MMLS_DOGS_Golden_Retriever,
        'LIVE_MMLS_DOGS_Goldendoodle_Golden_Retriever_Poodle_Mix' => self::LIVE_MMLS_DOGS_Goldendoodle_Golden_Retriever_Poodle_Mix,
        'LIVE_MMLS_DOGS_Gordon_Setter' => self::LIVE_MMLS_DOGS_Gordon_Setter,
        'LIVE_MMLS_DOGS_Great_Dane' => self::LIVE_MMLS_DOGS_Great_Dane,
        'LIVE_MMLS_DOGS_Great_Pyrenees' => self::LIVE_MMLS_DOGS_Great_Pyrenees,
        'LIVE_MMLS_DOGS_Greater_Swiss_Mountain_Dog' => self::LIVE_MMLS_DOGS_Greater_Swiss_Mountain_Dog,
        'LIVE_MMLS_DOGS_Greyhound' => self::LIVE_MMLS_DOGS_Greyhound,
        'LIVE_MMLS_DOGS_Harrier' => self::LIVE_MMLS_DOGS_Harrier,
        'LIVE_MMLS_DOGS_Havanese' => self::LIVE_MMLS_DOGS_Havanese,
        'LIVE_MMLS_DOGS_Ibizan_Hound' => self::LIVE_MMLS_DOGS_Ibizan_Hound,
        'LIVE_MMLS_DOGS_Icelandic_Sheepdog' => self::LIVE_MMLS_DOGS_Icelandic_Sheepdog,
        'LIVE_MMLS_DOGS_Irish_Red_and_White_Setter' => self::LIVE_MMLS_DOGS_Irish_Red_and_White_Setter,
        'LIVE_MMLS_DOGS_Irish_Setter' => self::LIVE_MMLS_DOGS_Irish_Setter,
        'LIVE_MMLS_DOGS_Irish_Terrier' => self::LIVE_MMLS_DOGS_Irish_Terrier,
        'LIVE_MMLS_DOGS_Irish_Water_Spaniel' => self::LIVE_MMLS_DOGS_Irish_Water_Spaniel,
        'LIVE_MMLS_DOGS_Irish_Wolfhound' => self::LIVE_MMLS_DOGS_Irish_Wolfhound,
        'LIVE_MMLS_DOGS_Italian_Greyhound' => self::LIVE_MMLS_DOGS_Italian_Greyhound,
        'LIVE_MMLS_DOGS_Italian_Mastiff' => self::LIVE_MMLS_DOGS_Italian_Mastiff,
        'LIVE_MMLS_DOGS_Jack_Chi_Chihuahua_Jack_Russell_Terrier_Mix' => self::LIVE_MMLS_DOGS_Jack_Chi_Chihuahua_Jack_Russell_Terrier_Mix,
        'LIVE_MMLS_DOGS_Jack_Russell_Terrier' => self::LIVE_MMLS_DOGS_Jack_Russell_Terrier,
        'LIVE_MMLS_DOGS_Japanese_Boxer' => self::LIVE_MMLS_DOGS_Japanese_Boxer,
        'LIVE_MMLS_DOGS_Japanese_Chin' => self::LIVE_MMLS_DOGS_Japanese_Chin,
        'LIVE_MMLS_DOGS_Japanese_Mastiff' => self::LIVE_MMLS_DOGS_Japanese_Mastiff,
        'LIVE_MMLS_DOGS_Japanese_Pug' => self::LIVE_MMLS_DOGS_Japanese_Pug,
        'LIVE_MMLS_DOGS_Japanese_Spaniel' => self::LIVE_MMLS_DOGS_Japanese_Spaniel,
        'LIVE_MMLS_DOGS_Kangal_Shepherd_Dog' => self::LIVE_MMLS_DOGS_Kangal_Shepherd_Dog,
        'LIVE_MMLS_DOGS_Keeshond' => self::LIVE_MMLS_DOGS_Keeshond,
        'LIVE_MMLS_DOGS_Kerry_Blue_Terrier' => self::LIVE_MMLS_DOGS_Kerry_Blue_Terrier,
        'LIVE_MMLS_DOGS_King_Charles_Spaniel' => self::LIVE_MMLS_DOGS_King_Charles_Spaniel,
        'LIVE_MMLS_DOGS_Komondor' => self::LIVE_MMLS_DOGS_Komondor,
        'LIVE_MMLS_DOGS_Kuvasz' => self::LIVE_MMLS_DOGS_Kuvasz,
        'LIVE_MMLS_DOGS_Kyi_Leo_Maltese_Lhasa_Apso_Mix' => self::LIVE_MMLS_DOGS_Kyi_Leo_Maltese_Lhasa_Apso_Mix,
        'LIVE_MMLS_DOGS_Labrabull_Labrador_Retriever_American_Pit_Bull' => self::LIVE_MMLS_DOGS_Labrabull_Labrador_Retriever_American_Pit_Bull,
        'LIVE_MMLS_DOGS_Labradane_Labrador_Retriever_Great_Dane_Mix' => self::LIVE_MMLS_DOGS_Labradane_Labrador_Retriever_Great_Dane_Mix,
        'LIVE_MMLS_DOGS_Labradoodle_Labrador_Retriever_Poodle_Mix' => self::LIVE_MMLS_DOGS_Labradoodle_Labrador_Retriever_Poodle_Mix,
        'LIVE_MMLS_DOGS_Labrador_Retriever' => self::LIVE_MMLS_DOGS_Labrador_Retriever,
        'LIVE_MMLS_DOGS_Lagotto_Romagnolo' => self::LIVE_MMLS_DOGS_Lagotto_Romagnolo,
        'LIVE_MMLS_DOGS_Lakeland_Terrier' => self::LIVE_MMLS_DOGS_Lakeland_Terrier,
        'LIVE_MMLS_DOGS_Leonberger' => self::LIVE_MMLS_DOGS_Leonberger,
        'LIVE_MMLS_DOGS_Lhasa_Apso' => self::LIVE_MMLS_DOGS_Lhasa_Apso,
        'LIVE_MMLS_DOGS_Löwchen' => self::LIVE_MMLS_DOGS_Löwchen,
        'LIVE_MMLS_DOGS_Mal_Shi_Maltese_Shih_Tzu_Mix' => self::LIVE_MMLS_DOGS_Mal_Shi_Maltese_Shih_Tzu_Mix,
        'LIVE_MMLS_DOGS_Malt_Tzu_Maltese_Shih_Tzu_Mix' => self::LIVE_MMLS_DOGS_Malt_Tzu_Maltese_Shih_Tzu_Mix,
        'LIVE_MMLS_DOGS_Maltese' => self::LIVE_MMLS_DOGS_Maltese,
        'LIVE_MMLS_DOGS_Maltese_Shih_Tzu' => self::LIVE_MMLS_DOGS_Maltese_Shih_Tzu,
        'LIVE_MMLS_DOGS_Malti_Zu_Maltese_Shih_Tzu_Mix' => self::LIVE_MMLS_DOGS_Malti_Zu_Maltese_Shih_Tzu_Mix,
        'LIVE_MMLS_DOGS_Maltipoo_Maltese_Poodle_Mix' => self::LIVE_MMLS_DOGS_Maltipoo_Maltese_Poodle_Mix,
        'LIVE_MMLS_DOGS_Manchester_Terrier' => self::LIVE_MMLS_DOGS_Manchester_Terrier,
        'LIVE_MMLS_DOGS_Mastador_Bullmastiff_Labrador_Retriever_Mix' => self::LIVE_MMLS_DOGS_Mastador_Bullmastiff_Labrador_Retriever_Mix,
        'LIVE_MMLS_DOGS_Mastiff' => self::LIVE_MMLS_DOGS_Mastiff,
        'LIVE_MMLS_DOGS_Mastin_Espanol_Spanish_Mastiff' => self::LIVE_MMLS_DOGS_Mastin_Espanol_Spanish_Mastiff,
        'LIVE_MMLS_DOGS_Mastino_Napoletano_Neopolitan_Mastiff' => self::LIVE_MMLS_DOGS_Mastino_Napoletano_Neopolitan_Mastiff,
        'LIVE_MMLS_DOGS_Miniature_American_Shepherd' => self::LIVE_MMLS_DOGS_Miniature_American_Shepherd,
        'LIVE_MMLS_DOGS_Miniature_Bull_Terrier' => self::LIVE_MMLS_DOGS_Miniature_Bull_Terrier,
        'LIVE_MMLS_DOGS_Miniature_Pinscher' => self::LIVE_MMLS_DOGS_Miniature_Pinscher,
        'LIVE_MMLS_DOGS_Miniature_Schnauzer' => self::LIVE_MMLS_DOGS_Miniature_Schnauzer,
        'LIVE_MMLS_DOGS_Mixed_Invalid_Breed_Type' => self::LIVE_MMLS_DOGS_Mixed_Invalid_Breed_Type,
        'LIVE_MMLS_DOGS_Neapolitan_Mastiff' => self::LIVE_MMLS_DOGS_Neapolitan_Mastiff,
        'LIVE_MMLS_DOGS_Newfoundland' => self::LIVE_MMLS_DOGS_Newfoundland,
        'LIVE_MMLS_DOGS_Norfolk_Terrier' => self::LIVE_MMLS_DOGS_Norfolk_Terrier,
        'LIVE_MMLS_DOGS_Norwegian_Buhund' => self::LIVE_MMLS_DOGS_Norwegian_Buhund,
        'LIVE_MMLS_DOGS_Norwegian_Elkhound' => self::LIVE_MMLS_DOGS_Norwegian_Elkhound,
        'LIVE_MMLS_DOGS_Norwegian_Lundehund' => self::LIVE_MMLS_DOGS_Norwegian_Lundehund,
        'LIVE_MMLS_DOGS_Norwich_Terrier' => self::LIVE_MMLS_DOGS_Norwich_Terrier,
        'LIVE_MMLS_DOGS_Nova_Scotia_Duck_Tolling_Retriever' => self::LIVE_MMLS_DOGS_Nova_Scotia_Duck_Tolling_Retriever,
        'LIVE_MMLS_DOGS_Old_English_Bulldog' => self::LIVE_MMLS_DOGS_Old_English_Bulldog,
        'LIVE_MMLS_DOGS_Old_English_Sheepdog' => self::LIVE_MMLS_DOGS_Old_English_Sheepdog,
        'LIVE_MMLS_DOGS_Olde_English_Bulldog' => self::LIVE_MMLS_DOGS_Olde_English_Bulldog,
        'LIVE_MMLS_DOGS_Otterhound' => self::LIVE_MMLS_DOGS_Otterhound,
        'LIVE_MMLS_DOGS_Papillon' => self::LIVE_MMLS_DOGS_Papillon,
        'LIVE_MMLS_DOGS_Parson_Russell_Terrier' => self::LIVE_MMLS_DOGS_Parson_Russell_Terrier,
        'LIVE_MMLS_DOGS_Peekapoo_Pekingese_Poodle_Mix' => self::LIVE_MMLS_DOGS_Peekapoo_Pekingese_Poodle_Mix,
        'LIVE_MMLS_DOGS_Pekingese' => self::LIVE_MMLS_DOGS_Pekingese,
        'LIVE_MMLS_DOGS_Pembroke_Welsh_Corgi' => self::LIVE_MMLS_DOGS_Pembroke_Welsh_Corgi,
        'LIVE_MMLS_DOGS_Petit_Basset_Griffon_Vendéen' => self::LIVE_MMLS_DOGS_Petit_Basset_Griffon_Vendéen,
        'LIVE_MMLS_DOGS_Pharaoh_Hound' => self::LIVE_MMLS_DOGS_Pharaoh_Hound,
        'LIVE_MMLS_DOGS_Pit_Bull' => self::LIVE_MMLS_DOGS_Pit_Bull,
        'LIVE_MMLS_DOGS_Pit_Plott_Pitbull_Plott_Hound_Mix' => self::LIVE_MMLS_DOGS_Pit_Plott_Pitbull_Plott_Hound_Mix,
        'LIVE_MMLS_DOGS_Plott_Hound' => self::LIVE_MMLS_DOGS_Plott_Hound,
        'LIVE_MMLS_DOGS_Pointer' => self::LIVE_MMLS_DOGS_Pointer,
        'LIVE_MMLS_DOGS_Polish_Lowland_Sheepdog' => self::LIVE_MMLS_DOGS_Polish_Lowland_Sheepdog,
        'LIVE_MMLS_DOGS_Pomapoo_Pomeranian_Poodle_Mix' => self::LIVE_MMLS_DOGS_Pomapoo_Pomeranian_Poodle_Mix,
        'LIVE_MMLS_DOGS_Pomchi_Pomeranian_Chihuahua_Mix' => self::LIVE_MMLS_DOGS_Pomchi_Pomeranian_Chihuahua_Mix,
        'LIVE_MMLS_DOGS_Pomeranian' => self::LIVE_MMLS_DOGS_Pomeranian,
        'LIVE_MMLS_DOGS_Pomsky_Pomeranian_Siberian_Husky_Mix' => self::LIVE_MMLS_DOGS_Pomsky_Pomeranian_Siberian_Husky_Mix,
        'LIVE_MMLS_DOGS_Poochon_Poodle_Bichon_Frise_Mix' => self::LIVE_MMLS_DOGS_Poochon_Poodle_Bichon_Frise_Mix,
        'LIVE_MMLS_DOGS_Poodle' => self::LIVE_MMLS_DOGS_Poodle,
        'LIVE_MMLS_DOGS_Portuguese_Podengo_Pequeno' => self::LIVE_MMLS_DOGS_Portuguese_Podengo_Pequeno,
        'LIVE_MMLS_DOGS_Portuguese_Water_Dog' => self::LIVE_MMLS_DOGS_Portuguese_Water_Dog,
        'LIVE_MMLS_DOGS_Presa_Canario' => self::LIVE_MMLS_DOGS_Presa_Canario,
        'LIVE_MMLS_DOGS_Pug' => self::LIVE_MMLS_DOGS_Pug,
        'LIVE_MMLS_DOGS_Pugapoo_Pug_Poodle_Mix' => self::LIVE_MMLS_DOGS_Pugapoo_Pug_Poodle_Mix,
        'LIVE_MMLS_DOGS_Puggle_Pug_Beagle_Mix' => self::LIVE_MMLS_DOGS_Puggle_Pug_Beagle_Mix,
        'LIVE_MMLS_DOGS_Puli' => self::LIVE_MMLS_DOGS_Puli,
        'LIVE_MMLS_DOGS_Pyrenean_Mastiff' => self::LIVE_MMLS_DOGS_Pyrenean_Mastiff,
        'LIVE_MMLS_DOGS_Pyrenean_Shepherd' => self::LIVE_MMLS_DOGS_Pyrenean_Shepherd,
        'LIVE_MMLS_DOGS_Rat_Terrier' => self::LIVE_MMLS_DOGS_Rat_Terrier,
        'LIVE_MMLS_DOGS_Redbone_Coonhound' => self::LIVE_MMLS_DOGS_Redbone_Coonhound,
        'LIVE_MMLS_DOGS_Rhodesian_Ridgeback' => self::LIVE_MMLS_DOGS_Rhodesian_Ridgeback,
        'LIVE_MMLS_DOGS_Rottweiler' => self::LIVE_MMLS_DOGS_Rottweiler,
        'LIVE_MMLS_DOGS_Russell_Terrier' => self::LIVE_MMLS_DOGS_Russell_Terrier,
        'LIVE_MMLS_DOGS_Saint_Bernard' => self::LIVE_MMLS_DOGS_Saint_Bernard,
        'LIVE_MMLS_DOGS_Saluki' => self::LIVE_MMLS_DOGS_Saluki,
        'LIVE_MMLS_DOGS_Samoyed' => self::LIVE_MMLS_DOGS_Samoyed,
        'LIVE_MMLS_DOGS_Schipperke' => self::LIVE_MMLS_DOGS_Schipperke,
        'LIVE_MMLS_DOGS_Schnoodle_Schnauzer_Poodle_Mix' => self::LIVE_MMLS_DOGS_Schnoodle_Schnauzer_Poodle_Mix,
        'LIVE_MMLS_DOGS_Scottish_Deerhound' => self::LIVE_MMLS_DOGS_Scottish_Deerhound,
        'LIVE_MMLS_DOGS_Scottish_Terrier' => self::LIVE_MMLS_DOGS_Scottish_Terrier,
        'LIVE_MMLS_DOGS_Sealyham_Terrier' => self::LIVE_MMLS_DOGS_Sealyham_Terrier,
        'LIVE_MMLS_DOGS_Shar_Pei' => self::LIVE_MMLS_DOGS_Shar_Pei,
        'LIVE_MMLS_DOGS_Sheepadoodle_Old_English_Sheepdog_Poodle_Mix' => self::LIVE_MMLS_DOGS_Sheepadoodle_Old_English_Sheepdog_Poodle_Mix,
        'LIVE_MMLS_DOGS_Shetland_Sheepdog' => self::LIVE_MMLS_DOGS_Shetland_Sheepdog,
        'LIVE_MMLS_DOGS_Shiba_Inu' => self::LIVE_MMLS_DOGS_Shiba_Inu,
        'LIVE_MMLS_DOGS_Shih_Poo' => self::LIVE_MMLS_DOGS_Shih_Poo,
        'LIVE_MMLS_DOGS_Shih_Tzu' => self::LIVE_MMLS_DOGS_Shih_Tzu,
        'LIVE_MMLS_DOGS_Shihpoo_Shih_Tzu_Poodle_Mix' => self::LIVE_MMLS_DOGS_Shihpoo_Shih_Tzu_Poodle_Mix,
        'LIVE_MMLS_DOGS_Siberian_Husky' => self::LIVE_MMLS_DOGS_Siberian_Husky,
        'LIVE_MMLS_DOGS_Silky_Terrier' => self::LIVE_MMLS_DOGS_Silky_Terrier,
        'LIVE_MMLS_DOGS_Skye_Terrier' => self::LIVE_MMLS_DOGS_Skye_Terrier,
        'LIVE_MMLS_DOGS_Sloughi_Arabian_Greyhound' => self::LIVE_MMLS_DOGS_Sloughi_Arabian_Greyhound,
        'LIVE_MMLS_DOGS_Smooth_Fox_Terrier' => self::LIVE_MMLS_DOGS_Smooth_Fox_Terrier,
        'LIVE_MMLS_DOGS_Soft_Coated_Wheaten_Terrier' => self::LIVE_MMLS_DOGS_Soft_Coated_Wheaten_Terrier,
        'LIVE_MMLS_DOGS_South_African_Mastiff' => self::LIVE_MMLS_DOGS_South_African_Mastiff,
        'LIVE_MMLS_DOGS_Spanish_Mastiff' => self::LIVE_MMLS_DOGS_Spanish_Mastiff,
        'LIVE_MMLS_DOGS_Spanish_Water_Dog' => self::LIVE_MMLS_DOGS_Spanish_Water_Dog,
        'LIVE_MMLS_DOGS_Spinone_Italiano' => self::LIVE_MMLS_DOGS_Spinone_Italiano,
        'LIVE_MMLS_DOGS_Staffordshire_Bull_Terrier' => self::LIVE_MMLS_DOGS_Staffordshire_Bull_Terrier,
        'LIVE_MMLS_DOGS_Standard_Schnauzer' => self::LIVE_MMLS_DOGS_Standard_Schnauzer,
        'LIVE_MMLS_DOGS_Sussex_Spaniel' => self::LIVE_MMLS_DOGS_Sussex_Spaniel,
        'LIVE_MMLS_DOGS_Swedish_Vallhund' => self::LIVE_MMLS_DOGS_Swedish_Vallhund,
        'LIVE_MMLS_DOGS_Tibetan_Mastiff' => self::LIVE_MMLS_DOGS_Tibetan_Mastiff,
        'LIVE_MMLS_DOGS_Tibetan_Spaniel' => self::LIVE_MMLS_DOGS_Tibetan_Spaniel,
        'LIVE_MMLS_DOGS_Tibetan_Terrier' => self::LIVE_MMLS_DOGS_Tibetan_Terrier,
        'LIVE_MMLS_DOGS_Tosa_Japanese_Mastiff' => self::LIVE_MMLS_DOGS_Tosa_Japanese_Mastiff,
        'LIVE_MMLS_DOGS_Toy_Fox_Terrier' => self::LIVE_MMLS_DOGS_Toy_Fox_Terrier,
        'LIVE_MMLS_DOGS_Treeing_Walker_Coonhound' => self::LIVE_MMLS_DOGS_Treeing_Walker_Coonhound,
        'LIVE_MMLS_DOGS_Utonagan_Northern_Inuit_Dog' => self::LIVE_MMLS_DOGS_Utonagan_Northern_Inuit_Dog,
        'LIVE_MMLS_DOGS_Valley_Bulldog' => self::LIVE_MMLS_DOGS_Valley_Bulldog,
        'LIVE_MMLS_DOGS_Vizsla' => self::LIVE_MMLS_DOGS_Vizsla,
        'LIVE_MMLS_DOGS_Weimaraner' => self::LIVE_MMLS_DOGS_Weimaraner,
        'LIVE_MMLS_DOGS_Welsh_Springer_Spaniel' => self::LIVE_MMLS_DOGS_Welsh_Springer_Spaniel,
        'LIVE_MMLS_DOGS_Welsh_Terrier' => self::LIVE_MMLS_DOGS_Welsh_Terrier,
        'LIVE_MMLS_DOGS_West_Highland_White_Terrier' => self::LIVE_MMLS_DOGS_West_Highland_White_Terrier,
        'LIVE_MMLS_DOGS_Whippet' => self::LIVE_MMLS_DOGS_Whippet,
        'LIVE_MMLS_DOGS_Wire_Fox_Terrier' => self::LIVE_MMLS_DOGS_Wire_Fox_Terrier,
        'LIVE_MMLS_DOGS_Wirehaired_Pointing_Griffon' => self::LIVE_MMLS_DOGS_Wirehaired_Pointing_Griffon,
        'LIVE_MMLS_DOGS_Wirehaired_Vizsla' => self::LIVE_MMLS_DOGS_Wirehaired_Vizsla,
        'LIVE_MMLS_DOGS_Xoloitzcuintli_Mexican_Hairless_Dog' => self::LIVE_MMLS_DOGS_Xoloitzcuintli_Mexican_Hairless_Dog,
        'LIVE_MMLS_DOGS_Yorkipoo_Yorkshire_Terrier_Poodle_Mix' => self::LIVE_MMLS_DOGS_Yorkipoo_Yorkshire_Terrier_Poodle_Mix,
        'LIVE_MMLS_DOGS_Yorkshire_Terrier' => self::LIVE_MMLS_DOGS_Yorkshire_Terrier,
        'LIVE_MMLS_FERR' => self::LIVE_MMLS_FERR,
        'LIVE_MMLS_GOAT' => self::LIVE_MMLS_GOAT,
        'LIVE_MMLS_HORS' => self::LIVE_MMLS_HORS,
        'LIVE_MMLS_MNKY' => self::LIVE_MMLS_MNKY,
        'LIVE_MMLS_PIGS' => self::LIVE_MMLS_PIGS,
        'LIVE_MMLS_RDNT' => self::LIVE_MMLS_RDNT,
        'LIVE_MMLS_SHEP' => self::LIVE_MMLS_SHEP,
        'LIVE_REPT' => self::LIVE_REPT,
        'LIVE_VANI' => self::LIVE_VANI,
        'LIVE_ZOOA' => self::LIVE_ZOOA,
        'MAIL' => self::MAIL,
        'MART' => self::MART,
        'MART_ART' => self::MART_ART,
        'MART_ENGR' => self::MART_ENGR,
        'MART_HAND' => self::MART_HAND,
        'MART_MUEQ' => self::MART_MUEQ,
        'MART_MUSI' => self::MART_MUSI,
        'MART_PNTG' => self::MART_PNTG,
        'MLTY' => self::MLTY,
        'MLTY_AMUN' => self::MLTY_AMUN,
        'MLTY_MSUP' => self::MLTY_MSUP,
        'MLTY_SPWE' => self::MLTY_SPWE,
        'MLTY_WPNS' => self::MLTY_WPNS,
        'PHAR' => self::PHAR,
        'PHAR_BIOP' => self::PHAR_BIOP,
        'PHAR_BIOP_BIOC' => self::PHAR_BIOP_BIOC,
        'PHAR_BIOP_HEMO' => self::PHAR_BIOP_HEMO,
        'PHAR_BIOP_HUBL' => self::PHAR_BIOP_HUBL,
        'PHAR_BIOP_HUSR' => self::PHAR_BIOP_HUSR,
        'PHAR_BIOP_LHOR' => self::PHAR_BIOP_LHOR,
        'PHAR_BIOP_SEME' => self::PHAR_BIOP_SEME,
        'PHAR_BIOP_SMPL' => self::PHAR_BIOP_SMPL,
        'PHAR_MDCN' => self::PHAR_MDCN,
        'PHAR_MDCN_ANTB' => self::PHAR_MDCN_ANTB,
        'PHAR_MDCN_VACC' => self::PHAR_MDCN_VACC,
        'PHAR_MDCN_VETE' => self::PHAR_MDCN_VETE,
        'PHAR_MEDI' => self::PHAR_MEDI,
        'PHAR_PHAR' => self::PHAR_PHAR,
        'PHAR_PHAR_SUEQ' => self::PHAR_PHAR_SUEQ,
        'PRIN' => self::PRIN,
        'PRIN_ADVM' => self::PRIN_ADVM,
        'PRIN_BOOK' => self::PRIN_BOOK,
        'PRIN_DOCU' => self::PRIN_DOCU,
        'PRIN_EDUM' => self::PRIN_EDUM,
        'PRIN_NEWS' => self::PRIN_NEWS,
        'PRIN_PPRP' => self::PRIN_PPRP,
        'RAWM' => self::RAWM,
        'RAWM_BLDM' => self::RAWM_BLDM,
        'RAWM_CLAY' => self::RAWM_CLAY,
        'RAWM_GLAS' => self::RAWM_GLAS,
        'RAWM_GRAN' => self::RAWM_GRAN,
        'RAWM_GUMS' => self::RAWM_GUMS,
        'RAWM_MARB' => self::RAWM_MARB,
        'RAWM_METL' => self::RAWM_METL,
        'RAWM_METP' => self::RAWM_METP,
        'RAWM_MICA' => self::RAWM_MICA,
        'RAWM_MINE' => self::RAWM_MINE,
        'RAWM_MIRR' => self::RAWM_MIRR,
        'RAWM_OILS' => self::RAWM_OILS,
        'RAWM_PLST' => self::RAWM_PLST,
        'RAWM_QRTZ' => self::RAWM_QRTZ,
        'RAWM_RUBR' => self::RAWM_RUBR,
        'RAWM_RUBR_RTYR' => self::RAWM_RUBR_RTYR,
        'RAWM_STNS' => self::RAWM_STNS,
        'RAWM_WOOD' => self::RAWM_WOOD,
        'SCIN' => self::SCIN,
        'SCIN_DEEQ' => self::SCIN_DEEQ,
        'SCIN_DIAG' => self::SCIN_DIAG,
        'SCIN_HEAR' => self::SCIN_HEAR,
        'SCIN_LBEQ' => self::SCIN_LBEQ,
        'SCIN_MEEQ' => self::SCIN_MEEQ,
        'SCIN_OPTI' => self::SCIN_OPTI,
        'SCIN_PRCI' => self::SCIN_PRCI,
        'TRPH' => self::TRPH,
        'TRPH_HTRH' => self::TRPH_HTRH,
        'TRPH_OTRH' => self::TRPH_OTRH,
        'TXTL' => self::TXTL,
        'TXTL_FREW' => self::TXTL_FREW,
        'TXTL_FUR' => self::TXTL_FUR,
        'TXTL_FURW' => self::TXTL_FURW,
        'TXTL_LEXW' => self::TXTL_LEXW,
        'TXTL_LTHR' => self::TXTL_LTHR,
        'TXTL_LTWR' => self::TXTL_LTWR,
        'TXTL_TXEW' => self::TXTL_TXEW,
        'TXTL_TXEW_CARP' => self::TXTL_TXEW_CARP,
        'TXTL_TXEW_CURT' => self::TXTL_TXEW_CURT,
        'TXTL_TXEW_FABR' => self::TXTL_TXEW_FABR,
        'TXTL_TXEW_FURN' => self::TXTL_TXEW_FURN,
        'TXTL_TXEW_HIDE' => self::TXTL_TXEW_HIDE,
        'TXTL_TXEW_NDLE' => self::TXTL_TXEW_NDLE,
        'TXTL_TXEW_SKIN' => self::TXTL_TXEW_SKIN,
        'TXTL_TXEW_TRLS' => self::TXTL_TXEW_TRLS,
        'TXTL_TXEW_YARN' => self::TXTL_TXEW_YARN,
        'TXTL_TXLW' => self::TXTL_TXLW,
        'TXTL_TXLW_APPR' => self::TXTL_TXLW_APPR,
        'TXTL_TXLW_CLTH' => self::TXTL_TXLW_CLTH,
        'TXTL_TXLW_FOOT' => self::TXTL_TXLW_FOOT,
        'TXTL_TXLW_GARM' => self::TXTL_TXLW_GARM,
        'TXTL_TXTL' => self::TXTL_TXTL,
        'VALU' => self::VALU,
        'VALU_BANK' => self::VALU_BANK,
        'VALU_DIAM' => self::VALU_DIAM,
        'VALU_GOLD' => self::VALU_GOLD,
        'VALU_JWRY' => self::VALU_JWRY,
        'VALU_PLAT' => self::VALU_PLAT,
        'VALU_PMET' => self::VALU_PMET,
        'VALU_PSTN' => self::VALU_PSTN,
        'VALU_SLVR' => self::VALU_SLVR,
        'VALU_WTCH' => self::VALU_WTCH,
        'VHCL' => self::VHCL,
        'VHCL_AIRC' => self::VHCL_AIRC,
        'VHCL_AIRC_AACC' => self::VHCL_AIRC_AACC,
        'VHCL_AIRC_AENG' => self::VHCL_AIRC_AENG,
        'VHCL_AIRC_AMTR' => self::VHCL_AIRC_AMTR,
        'VHCL_AIRC_APRT' => self::VHCL_AIRC_APRT,
        'VHCL_AIRC_ASUP' => self::VHCL_AIRC_ASUP,
        'VHCL_AIRC_AWHL' => self::VHCL_AIRC_AWHL,
        'VHCL_AIRC_HELI' => self::VHCL_AIRC_HELI,
        'VHCL_AIRC_HPRT' => self::VHCL_AIRC_HPRT,
        'VHCL_MACH' => self::VHCL_MACH,
        'VHCL_MACH_COIL' => self::VHCL_MACH_COIL,
        'VHCL_MACH_COMP' => self::VHCL_MACH_COMP,
        'VHCL_MACH_HRDW' => self::VHCL_MACH_HRDW,
        'VHCL_MACH_MECH' => self::VHCL_MACH_MECH,
        'VHCL_MACH_MTSP' => self::VHCL_MACH_MTSP,
        'VHCL_MACH_OILD' => self::VHCL_MACH_OILD,
        'VHCL_MACH_PART' => self::VHCL_MACH_PART,
        'VHCL_MACH_PUEQ' => self::VHCL_MACH_PUEQ,
        'VHCL_SHIP' => self::VHCL_SHIP,
        'VHCL_SHIP_SENG' => self::VHCL_SHIP_SENG,
        'VHCL_SHIP_SMTR' => self::VHCL_SHIP_SMTR,
        'VHCL_SHIP_SPAR' => self::VHCL_SHIP_SPAR,
        'VHCL_SHIP_SSPA' => self::VHCL_SHIP_SSPA,
        'VHCL_SVCL' => self::VHCL_SVCL,
        'VHCL_SVCL_AUTO' => self::VHCL_SVCL_AUTO,
        'VHCL_SVCL_BICY' => self::VHCL_SVCL_BICY,
        'VHCL_SVCL_CRTA' => self::VHCL_SVCL_CRTA,
        'VHCL_SVCL_MOTO' => self::VHCL_SVCL_MOTO,
        'VHCL_SVCL_PART' => self::VHCL_SVCL_PART,
        'VHCL_SVCL_TIRE' => self::VHCL_SVCL_TIRE,
    ];
}
